<?php

class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $headers = 'MIME-Version: 1.0' . "\r\n"
                 . 'Content-Type: text/plain; charset=UTF-8' . "\r\n"
                 . 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>' . "\r\n"
                 . 'X-Mailer: PHP/' . phpversion();

        $body = wordwrap($body, 70, "\r\n");

        try {
            $ok = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
        } catch (\Throwable $e) {
            $ok = false;
        }

        return $ok;
    }
}
