import hmac
import hashlib
import logging

def verify_hmac_otp(received_otp, stored_otp_hash, secret_key):
    try:
        received_otp = str(received_otp).strip()
        generated_hash = hmac.new(
            secret_key.encode(),
            received_otp.encode(),
            hashlib.sha256
        ).hexdigest()
        return hmac.compare_digest(generated_hash, stored_otp_hash)
    except Exception as e:
        logging.error(f"HMAC verification error: {e}")
        return False