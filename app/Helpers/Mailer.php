<?php

namespace App\Helpers;

use App\Config\Env;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    /**
     * Send an HTML email via SMTP using credentials from .env.
     * Returns true on success, false on failure (and logs the error —
     * never throws, so a mail outage never crashes a registration request).
     */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = Env::get('MAIL_HOST', 'smtp.gmail.com');
            $mail->Port = (int) Env::get('MAIL_PORT', 587);
            $mail->Timeout = 10; // seconds — fail fast instead of hanging if SMTP is unreachable/misconfigured
            $mail->SMTPKeepAlive = false;

            $username = Env::get('MAIL_USERNAME', '');
            $password = Env::get('MAIL_PASSWORD', '');

            // Local dev catchers (e.g. Laragon's bundled Mailpit on 127.0.0.1:1025)
            // need no auth and no encryption. Real providers (Gmail, etc.) need both.
            // Leave MAIL_USERNAME blank in .env to use a local catcher.
            if ($username !== '') {
                $mail->SMTPAuth = true;
                $mail->Username = $username;
                $mail->Password = $password;

                // MAIL_ENCRYPTION=ssl -> port 465 (implicit TLS); anything else -> port 587 (STARTTLS).
                $mail->SMTPSecure = strtolower(Env::get('MAIL_ENCRYPTION', 'tls')) === 'ssl'
                    ? PHPMailer::ENCRYPTION_SMTPS
                    : PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPAuth = false;
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;
            }

            $mail->setFrom(
                Env::get('MAIL_FROM_ADDRESS', 'noreply@smartenroll.local'),
                Env::get('MAIL_FROM_NAME', 'SmartEnroll')
            );
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = trim(strip_tags($htmlBody));

            $mail->send();
            self::log("SENT to {$toEmail}: {$subject}");
            return true;
        } catch (PHPMailerException $e) {
            self::log("FAILED to {$toEmail}: {$subject} — {$mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Write to storage/logs/mail.log directly, rather than relying on
     * PHP's error_log ini setting (which varies by install and can be
     * hard to locate). This gives one guaranteed, predictable place to
     * check when a verification email doesn't arrive.
     */
    private static function log(string $message): void
    {
        $logDir = __DIR__ . '/../../storage/logs';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }

        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        @file_put_contents($logDir . '/mail.log', $line, FILE_APPEND | LOCK_EX);

        // Also try the normal PHP error log as a secondary channel, in case
        // it IS configured — costs nothing if it isn't.
        error_log('Mailer: ' . $message);
    }

    public static function verificationCodeEmail(string $firstName, string $code): string
    {
        $name = htmlspecialchars($firstName);
        $codeEscaped = htmlspecialchars($code);

        return <<<HTML
        <div style="font-family: 'Nunito', Arial, sans-serif; background:#EDE7DD; padding:32px;">
            <div style="max-width:480px; margin:0 auto; background:#F5F1E8; border-radius:20px; padding:32px; text-align:center;">
                <h1 style="color:#55704F; font-size:22px; margin-top:0;">Welcome to SmartEnroll, {$name}!</h1>
                <p style="color:#3B362C; font-size:15px; line-height:1.5;">
                    Enter this code to verify your email and activate your account:
                </p>
                <p style="margin:28px 0;">
                    <span style="background:#DCE6D8; color:#55704F; font-family:'Fredoka', Arial, sans-serif;
                                 font-size:34px; font-weight:700; letter-spacing:10px;
                                 padding:16px 24px; border-radius:16px; display:inline-block;">
                        {$codeEscaped}
                    </span>
                </p>
                <p style="color:#8A8272; font-size:13px; line-height:1.5;">
                    This code expires in 15 minutes. If you didn't create a SmartEnroll account,
                    you can safely ignore this email.
                </p>
            </div>
        </div>
        HTML;
    }
}