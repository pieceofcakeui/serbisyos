<?php
session_start();
require_once 'db_connection.php';

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client as GuzzleClient;

require '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $newState = $_POST['account_state'] ?? null;

    if ($userId && $newState) {
        if ($_SESSION['id'] == $userId && $newState == 'Inactive') {
            $_SESSION['error_message'] = "You cannot deactivate your own account";
            header("Location: ../user-management.php");
            exit();
        }

        $userQuery = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
        $userQuery->bind_param("i", $userId);
        $userQuery->execute();
        $result = $userQuery->get_result();
        $userData = $result->fetch_assoc();
        $userQuery->close();

        $stmt = $conn->prepare("UPDATE users SET account_state = ? WHERE id = ?");
        $stmt->bind_param("si", $newState, $userId);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Updated successfully";

            if ($userData) {
                try {
                    $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
                    if (!empty($apiKey)) {
                        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
                        $apiInstance = new TransactionalEmailsApi(new GuzzleClient(), $config);

                        $sendSmtpEmail = new SendSmtpEmail([
                            'to' => [[
                                'email' => $userData['email'], 
                                'name' => $userData['fullname']
                            ]],
                            'templateId' => 28,
                            'params' => [
                                'USER_NAME' => $userData['fullname'],
                                'STATUS' => $newState,
                                'BASE_URL' => $_ENV['BASE_URL'],
                                'SUPPORT_EMAIL' => $_ENV['SUPPORT_EMAIL'],
                                'LOGO_URL' => $_ENV['BASE_URL'] . '/assets/img/logo.png'
                            ],
                            'subject' => "Account " . $newState . " - Serbisyos",
                            'sender' => [
                                'name' => 'Serbisyos Team', 
                                'email' => $_ENV['NO_REPLY_EMAIL']
                            ],
                            'replyTo' => [
                                'name' => 'Serbisyos Support', 
                                'email' => $_ENV['SUPPORT_EMAIL']
                            ]
                        ]);

                        $apiInstance->sendTransacEmail($sendSmtpEmail);
                    }
                } catch (Exception $e) {
                    error_log("Brevo Mailer Error for account status: " . $e->getMessage());
                }
            }

        } else {
            $_SESSION['error_message'] = "Failed to update account state";
        }

        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Invalid request parameters";
    }

    header("Location: ../user-management.php");
    exit();
}

header("Location: ../user-management.php");
exit();
?>