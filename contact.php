<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

if (!empty($_POST['website'] ?? '')) {
    echo json_encode(['success' => true, 'message' => 'Message received.']);
    exit;
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$subject = trim((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$length = static function (string $value): int {
    return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
};

$hasHeaderBreak = static function (string $value): bool {
    return str_contains($value, "\r") || str_contains($value, "\n");
};

$valid = $name !== ''
    && $length($name) <= 120
    && filter_var($email, FILTER_VALIDATE_EMAIL) !== false
    && !$hasHeaderBreak($email)
    && $subject !== ''
    && $length($subject) <= 180
    && !$hasHeaderBreak($subject)
    && $message !== ''
    && $length($message) <= 5000;

if (!$valid) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please review the form fields and try again.']);
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

$smtpHost = getenv('CSM_SMTP_HOST') ?: 'smtp.gmail.com';
$smtpPort = (int) (getenv('CSM_SMTP_PORT') ?: 587);
$smtpUser = getenv('CSM_SMTP_USER') ?: 'csmexperience@gmail.com';
$smtpPassword = getenv('CSM_SMTP_PASSWORD') ?: '';
$smtpEncryption = strtolower(getenv('CSM_SMTP_ENCRYPTION') ?: 'tls');
$mailFrom = getenv('CSM_MAIL_FROM') ?: $smtpUser;
$mailFromName = getenv('CSM_MAIL_FROM_NAME') ?: 'CSM-Hub Website';

if ($smtpHost === '' || $smtpUser === '' || $smtpPassword === '' || $mailFrom === '') {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'The contact service is not configured yet.']);
    exit;
}

if (!in_array($smtpEncryption, ['tls', 'ssl', 'none'], true)) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'The contact service has an invalid SMTP encryption setting.']);
    exit;
}

try {
    $mailer = new PHPMailer(true);
    $mailer->isSMTP();
    $mailer->Host = $smtpHost;
    $mailer->Port = $smtpPort;
    $mailer->SMTPAuth = true;
    $mailer->Username = $smtpUser;
    $mailer->Password = $smtpPassword;
    $mailer->SMTPSecure = match ($smtpEncryption) {
        'ssl' => PHPMailer::ENCRYPTION_SMTPS,
        'none' => '',
        default => PHPMailer::ENCRYPTION_STARTTLS,
    };
    $mailer->CharSet = 'UTF-8';
    $mailer->setFrom($mailFrom, $mailFromName);
    $mailer->addAddress('csmexperience@gmail.com');
    $mailer->addReplyTo($email, $name);
    $mailer->Subject = 'CSM-Hub contact: ' . $subject;
    $mailer->Body = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}";
    $mailer->AltBody = $mailer->Body;
    $mailer->send();
} catch (Exception $exception) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'The message could not be sent right now. Please try again later.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Your message was sent successfully.']);
