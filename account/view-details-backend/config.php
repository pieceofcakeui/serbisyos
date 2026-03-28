<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

define('ATTACHMENT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/serbisyos/assets/uploads/attachments/');
define('ATTACHMENT_URL', '/serbisyos/assets/uploads/attachments/');
define('ENCRYPT_METHOD', 'AES-256-CBC');

define('SECRET_KEY', hex2bin($_ENV['SECRET_KEY']));
define('SECRET_IV', hex2bin($_ENV['SECRET_IV']));

if (!file_exists(ATTACHMENT_DIR)) {
    mkdir(ATTACHMENT_DIR, 0755, true);
}