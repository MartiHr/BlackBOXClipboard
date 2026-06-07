<?php

class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        if (!extension_loaded('openssl')) {
            return false;
        }

        try {
            $ok = self::smtpSend($to, $subject, $body);
            self::log("Send to $to: " . ($ok ? 'OK' : 'FAIL'));
            return $ok;
        } catch (\Throwable $e) {
            self::log("Exception: " . $e->getMessage());
            return false;
        }
    }

    private static function smtpSend(string $to, string $subject, string $body): bool
    {
        $host = SMTP_HOST;
        $port = (int) (defined('SMTP_SSL_PORT') ? SMTP_SSL_PORT : 465);
        $user = SMTP_USER;
        $pass = SMTP_PASS;

        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $errno = 0;
        $errstr = '';
        $conn = @stream_socket_client('ssl://' . $host . ':' . $port, $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx);
        if (!$conn) {
            self::log("SSL connect failed: $errstr ($errno)");
            return false;
        }

        @stream_set_timeout($conn, 30);

        if (!self::expect($conn, 220)) {
            self::log("Banner not OK");
            fclose($conn);
            return false;
        }

        self::sendCmd($conn, 'EHLO localhost');
        if (!self::expect($conn, 250)) {
            fclose($conn);
            return false;
        }

        $auth = base64_encode("\0" . $user . "\0" . $pass);
        self::sendCmd($conn, 'AUTH PLAIN ' . $auth);
        if (!self::expect($conn, 235)) {
            self::log("AUTH failed");
            fclose($conn);
            return false;
        }

        self::sendCmd($conn, 'MAIL FROM:<' . MAIL_FROM . '>');
        if (!self::expect($conn, 250)) {
            fclose($conn);
            return false;
        }

        self::sendCmd($conn, 'RCPT TO:<' . $to . '>');
        if (!self::expect($conn, 250)) {
            fclose($conn);
            return false;
        }

        self::sendCmd($conn, 'DATA');
        if (!self::expect($conn, 354)) {
            fclose($conn);
            return false;
        }

        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $headers = 'MIME-Version: 1.0' . "\r\n"
                 . 'Content-Type: text/plain; charset=UTF-8' . "\r\n"
                 . 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>' . "\r\n"
                 . 'To: <' . $to . '>' . "\r\n"
                 . 'Subject: ' . $encodedSubject . "\r\n"
                 . 'X-Mailer: PHP/' . phpversion() . "\r\n"
                 . 'Date: ' . date('r') . "\r\n";
        $body = wordwrap($body, 70, "\r\n");

        fwrite($conn, $headers . "\r\n" . $body . "\r\n.\r\n");
        if (!self::expect($conn, 250)) {
            self::log("DATA rejected");
            fclose($conn);
            return false;
        }

        self::sendCmd($conn, 'QUIT');
        fclose($conn);
        return true;
    }

    private static function sendCmd($conn, string $cmd): void
    {
        fwrite($conn, $cmd . "\r\n");
    }

    private static function readReply($conn): string
    {
        $res = '';
        while ($line = @fgets($conn, 512)) {
            $res .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $res;
    }

    private static function expect($conn, int $code): bool
    {
        $reply = self::readReply($conn);
        $got = (int) substr($reply, 0, 3);
        if ($got !== $code) {
            self::log("Expected $code, got: " . trim($reply));
        }
        return $got === $code;
    }

    private static function log(string $msg): void
    {
        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }
        @file_put_contents($logDir . '/mail.log', date('Y-m-d H:i:s') . ' ' . $msg . "\n", FILE_APPEND);
    }
}
