<?php

require_once __DIR__ . '/app.php';

define('MAIL_FROM', $_ENV['MAIL_FROM'] ?? $_SERVER['MAIL_FROM'] ?? 'onboarding@resend.dev');
define('MAIL_FROM_NAME', 'BlackBOX Clipboard');

define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? $_SERVER['SMTP_HOST'] ?? 'smtp.resend.com');
define('SMTP_PORT', (int) ($_ENV['SMTP_PORT'] ?? $_SERVER['SMTP_PORT'] ?? 587));
define('SMTP_USER', $_ENV['SMTP_USER'] ?? $_SERVER['SMTP_USER'] ?? 'resend');
define('SMTP_PASS', $_ENV['SMTP_PASS'] ?? $_SERVER['SMTP_PASS'] ?? '');
