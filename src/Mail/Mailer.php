<?php

class Mailer
{
    private static function smtpSend(string $to, string $subject, string $body): bool
    {
        $host = SMTP_HOST;
        $port = SMTP_PORT;
        $user = SMTP_USER;
        $pass = SMTP_PASS;

        $errno = 0;
        $errstr = '';

        $conn = @stream_socket_client(
            'tcp://' . $host . ':' . $port,
            $errno,
            $errstr,
            15
        );

        if (!$conn) {
            return false;
        }

        $res = self::readReply($conn);

        self::sendCmd($conn, 'EHLO localhost');
        self::readReply($conn);

        self::sendCmd($conn, 'STARTTLS');
        self::readReply($conn);

        $crypto = @stream_socket_enable_crypto($conn, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        if (!$crypto) {
            fclose($conn);
            return false;
        }

        self::sendCmd($conn, 'EHLO localhost');
        self::readReply($conn);

        $auth = base64_encode("\0" . $user . "\0" . $pass);
        self::sendCmd($conn, 'AUTH PLAIN ' . $auth);
        self::readReply($conn);

        self::sendCmd($conn, 'MAIL FROM:<' . MAIL_FROM . '>');
        self::readReply($conn);

        self::sendCmd($conn, 'RCPT TO:<' . $to . '>');
        self::readReply($conn);

        self::sendCmd($conn, 'DATA');
        self::readReply($conn);

        $headers = 'MIME-Version: 1.0' . "\r\n"
                 . 'Content-Type: text/plain; charset=UTF-8' . "\r\n"
                 . 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>' . "\r\n"
                 . 'To: <' . $to . '>' . "\r\n"
                 . 'Subject: ' . $subject . "\r\n"
                 . 'X-Mailer: PHP/' . phpversion() . "\r\n"
                 . 'Date: ' . date('r') . "\r\n";

        $body = wordwrap($body, 70, "\r\n");

        fwrite($conn, $headers . "\r\n" . $body . "\r\n.");
        self::readReply($conn);

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
        while ($line = fgets($conn, 512)) {
            $res .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $res;
    }

    public static function send(string $to, string $subject, string $body): bool
    {
        try {
            return self::smtpSend($to, $subject, $body);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
