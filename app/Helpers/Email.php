<?php

namespace App\Helpers;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * An helper for email methods.
 */
class Email
{
    /**
     * Send an email
     *
     * @param \PHPMailer\PHPMailer\PHPMailer $mailer
     * @param string $email
     * @param string $mailSubject
     * @param string $mailContent
     * @param array $attachments
     *
     * @return bool
     */
    public static function send(PHPMailer $mailer, string $email, string $mailSubject, string $mailContent, array $attachments = []): bool
    {
        try {
            //Recipients
            $mailer->addAddress($email);

            // Attachments
            if (count($attachments) > 0) {
                foreach ($attachments as $file_path => $file_name) {
                    $mailer->addAttachment($file_path, $file_name);
                }
            }

            // Content
            $mailer->isHTML(true);
            $mailer->Subject = $mailSubject;
            $mailer->Body    = $mailContent;

            // Send Mail
            $mailer->send();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
