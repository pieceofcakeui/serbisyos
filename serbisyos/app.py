from flask import Flask, request, jsonify
import pymysql
import logging
from datetime import datetime
import pytz

from functions.config import DB_CONFIG, SECRET_KEY, TIMEZONE
from functions.otp_utils import verify_hmac_otp

app = Flask(__name__)
logging.basicConfig(level=logging.DEBUG)

try:
    DISPLAY_TIMEZONE = pytz.timezone(TIMEZONE)
except pytz.UnknownTimeZoneError:
    logging.error(f"Unknown timezone set: {TIMEZONE}. Falling back to UTC.")
    DISPLAY_TIMEZONE = pytz.utc

@app.route('/', methods=['GET'])
def health_check():
    return jsonify({"status": "running", "message": "OTP API is operational"}), 200

@app.route('/verify_otp', methods=['POST'])
def verify_otp():
    conn = None
    try:
        data = request.json
        user_id = data.get("user_id")
        email = data.get("email")
        otp = data.get("otp")

        if not all([user_id, email, otp]):
            return jsonify({"success": False, "message": "Missing user_id, email, or otp."}), 400

        conn = pymysql.connect(**DB_CONFIG)
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        
        cursor.execute("SET time_zone = '+08:00'")

        cursor.execute("SELECT NOW() as db_now")
        db_time = cursor.fetchone()
        current_time_manila = DISPLAY_TIMEZONE.localize(db_time['db_now'])

        cursor.execute(
            "SELECT * FROM otp_verifications WHERE user_id = %s AND email = %s AND verified = 0 ORDER BY created_at DESC LIMIT 1",
            (user_id, email)
        )
        otp_record = cursor.fetchone()

        if otp_record:
            stored_otp_hash = otp_record["otp"]

            if verify_hmac_otp(otp, stored_otp_hash, SECRET_KEY):
                
                expires_at_from_db = otp_record["expires_at"]

                if isinstance(expires_at_from_db, str):
                    try:
                        expires_at_from_db = datetime.strptime(expires_at_from_db, "%Y-%m-%d %H:%M:%S")
                    except ValueError:
                        logging.error("Failed to parse expires_at string.")

                expires_at_manila = DISPLAY_TIMEZONE.localize(expires_at_from_db) if expires_at_from_db.tzinfo is None else expires_at_from_db

                if current_time_manila > expires_at_manila:
                    return jsonify({"success": False, "message": "Verification code expired!"}), 400

                cursor.execute(
                    "UPDATE otp_verifications SET verified = 1 WHERE id = %s",
                    (otp_record["id"],)
                )
                
                cursor.execute(
                    "UPDATE users SET status = 'verified' WHERE id = %s",
                    (user_id,)
                )
                conn.commit()

                return jsonify({"success": True, "message": "OTP verified successfully!"}), 200
            else:
                return jsonify({"success": False, "message": "Invalid verification code!"}), 400
        else:
            return jsonify({"success": False, "message": "Invalid verification code or no active code found."}), 400

    except pymysql.MySQLError as e:
        logging.error(f"Database Error: {e}")
        return jsonify({"success": False, "message": "Database operation failed."}), 500
    except Exception as e:
        logging.error(f"General Error in verify_otp: {str(e)}", exc_info=True)
        return jsonify({"success": False, "message": "Internal Server Error"}), 500
    finally:
        if conn:
            conn.close()

@app.route('/verify_otp_reset', methods=['POST'])
def verify_reset_otp():
    conn = None
    try:
        data = request.json
        user_id = data.get("user_id")
        email = data.get("email")
        otp = data.get("otp")

        if not all([user_id, email, otp]):
            return jsonify({"success": False, "message": "Missing user_id, email, or otp."}), 400

        conn = pymysql.connect(**DB_CONFIG)
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        
        cursor.execute("SET time_zone = '+08:00'")

        cursor.execute("SELECT NOW() as db_now")
        db_time = cursor.fetchone()
        current_time_manila = DISPLAY_TIMEZONE.localize(db_time['db_now'])

        cursor.execute(
            "SELECT * FROM otp_verifications WHERE user_id = %s AND email = %s AND verified = 0 ORDER BY created_at DESC LIMIT 1",
            (user_id, email)
        )
        otp_record = cursor.fetchone()

        if not otp_record:
            return jsonify({"success": False, "message": "No active verification code found."}), 400

        stored_otp_hash = otp_record["otp"]

        if not verify_hmac_otp(otp, stored_otp_hash, SECRET_KEY):
            return jsonify({"success": False, "message": "Invalid verification code."}), 400

        expires_at_from_db = otp_record["expires_at"]

        if isinstance(expires_at_from_db, str):
            try:
                expires_at_from_db = datetime.strptime(expires_at_from_db, "%Y-%m-%d %H:%M:%S")
            except ValueError:
                logging.error("Failed to parse expires_at string.")

        expires_at_manila = DISPLAY_TIMEZONE.localize(expires_at_from_db) if expires_at_from_db.tzinfo is None else expires_at_from_db

        if current_time_manila > expires_at_manila:
            return jsonify({"success": False, "message": "Verification code has expired."}), 400

        cursor.execute(
            "UPDATE otp_verifications SET verified = 1 WHERE id = %s",
            (otp_record["id"],)
        )
        conn.commit()

        return jsonify({
            "success": True,
            "message": "Verification successful!",
            "redirect_url": "reset_password.php"
        }), 200

    except pymysql.MySQLError as e:
        logging.error(f"Database Error: {e}")
        return jsonify({"success": False, "message": "Database operation failed."}), 500
    except Exception as e:
        logging.error(f"General Error in verify_reset_otp: {str(e)}", exc_info=True)
        return jsonify({"success": False, "message": "An error occurred during verification."}), 500
    finally:
        if conn:
            conn.close()

application = app

if __name__ == '__main__':
    app.run(debug=True)