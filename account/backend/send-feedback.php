<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/db_connection.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($subject === 'Other' && !empty($_POST['other_subject'])) {
    $subject = trim($_POST['other_subject']);
}

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

$config = Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['BREVO_API_KEY']);
$apiInstance = new Brevo\Client\Api\TransactionalEmailsApi(
    new GuzzleHttp\Client(),
    $config
);

try {
    $stmt = $conn->prepare("INSERT INTO feedback (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    $stmt->execute();
    $stmt->close();

    $adminEmail = new \Brevo\Client\Model\SendSmtpEmail();
    $adminEmail->setTo([['email' => $_ENV['SENDER_EMAIL'], 'name' => 'Serbisyos Support']]);
    $adminEmail->setReplyTo(['email' => $email, 'name' => $name]);
    $adminEmail->setSender(['email' => $_ENV['SENDER_EMAIL'], 'name' => $name . " (via Feedback Form)"]);
    $adminEmail->setSubject("New Feedback: " . htmlspecialchars($subject));
    $adminEmail->setHtmlContent("
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #dddddd; border-radius: 5px; line-height: 1.6; color: #333333;'>
            <h2 style='color: #222222; border-bottom: 1px solid #cccccc; padding-bottom: 10px; font-size: 24px;'>New Feedback Received</h2>
            <p>You have received a new feedback submission with the following details:</p>
            <div style='background-color: #f9f9f9; border-radius: 5px; padding: 20px; margin: 20px 0;'>
                <p style='margin: 0 0 10px 0;'><strong>From:</strong> " . htmlspecialchars($name) . "</p>
                <p style='margin: 0 0 10px 0;'><strong>Email:</strong> <a href='mailto:" . htmlspecialchars($email) . "' style='color: #007bff; text-decoration: none;'>" . htmlspecialchars($email) . "</a></p>
                <p style='margin: 0 0 10px 0;'><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
                <hr style='border: 0; border-top: 1px solid #eeeeee; margin: 15px 0;'>
                <p style='margin: 0;'><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
            </div>
            <p style='margin-top: 25px;'>You can reply directly to this email to contact the user.</p>
        </div>");
    
    $apiInstance->sendTransacEmail($adminEmail);

    $userConfirmationEmail = new \Brevo\Client\Model\SendSmtpEmail();
    $userConfirmationEmail->setTemplateId(11);
    $userConfirmationEmail->setTo([['email' => $email, 'name' => $name]]);
    $userConfirmationEmail->setSender(['email' => $_ENV['SENDER_EMAIL'], 'name' => $_ENV['SENDER_NAME']]);
    
    $userConfirmationEmail->setParams([
        'NAME' => $name,
        'SUBJECT' => $subject,
        'MESSAGE' => $message,
    ]);
    
    $apiInstance->sendTransacEmail($userConfirmationEmail);

    echo json_encode(['success' => true, 'message' => 'Thank you for your feedback! We will review it shortly.']);

} catch (Exception $e) {
    if ($e instanceof mysqli_sql_exception) {
        error_log("Database Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save feedback. Please try again later.']);
    } else {
        error_log("Brevo API Error: ". $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to send feedback. Please try again later.']);
    }
}
?>