<?php

namespace App\Services;

use App\Config;
use App\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

/**
 * Sends transactional emails and logs failures without exposing mail errors.
 */
class MailService
{
    private Logger $logger;

    public function __construct(?Logger $logger = null)
    {
        $this->logger = $logger ?: new Logger();
    }

    /**
     * Send an admin notification for a public contact enquiry.
     *
     * @param array $contact Contact payload.
     * @return bool
     */
    public function sendContactNotification(array $contact): bool
    {
        $subject = 'New website enquiry: ' . (string) ($contact['subject'] ?? 'Contact form');
        $body = sprintf(
            "Name: %s\nEmail: %s\nPhone: %s\nCompany: %s\nService: %s\n\nMessage:\n%s",
            (string) ($contact['full_name'] ?? ''),
            (string) ($contact['email'] ?? ''),
            (string) ($contact['phone'] ?? ''),
            (string) ($contact['company'] ?? ''),
            (string) ($contact['service_interested'] ?? ''),
            (string) ($contact['message'] ?? '')
        );

        return $this->sendAdminMail($subject, $body, (string) ($contact['email'] ?? ''));
    }

    /**
     * Send an admin notification for a new order.
     *
     * @param array $order Order payload.
     * @return bool
     */
    public function sendOrderNotification(array $order): bool
    {
        $subject = 'New website order: ' . (string) ($order['order_number'] ?? '');
        $body = sprintf(
            "Order: %s\nCustomer: %s\nEmail: %s\nPhone: %s\nTotal: NGN %s\nPayment: %s",
            (string) ($order['order_number'] ?? ''),
            (string) ($order['customer_name'] ?? ''),
            (string) ($order['customer_email'] ?? ''),
            (string) ($order['customer_phone'] ?? ''),
            number_format((float) ($order['total'] ?? 0), 2),
            (string) ($order['payment_method'] ?? '')
        );

        return $this->sendAdminMail($subject, $body, (string) ($order['customer_email'] ?? ''));
    }

    private function sendAdminMail(string $subject, string $body, string $replyTo = ''): bool
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = (string) Config::get('mail.mailers.smtp.host', 'localhost');
            $mail->Port = (int) Config::get('mail.mailers.smtp.port', 587);

            $username = (string) Config::get('mail.mailers.smtp.username', '');
            $password = (string) Config::get('mail.mailers.smtp.password', '');

            if ($username !== '') {
                $mail->SMTPAuth = true;
                $mail->Username = $username;
                $mail->Password = $password;
            }

            $encryption = (string) Config::get('mail.mailers.smtp.encryption', '');
            if ($encryption !== '') {
                $mail->SMTPSecure = $encryption;
            }

            $fromAddress = (string) Config::get('mail.from.address', 'noreply@desnkygroup.com');
            $fromName = (string) Config::get('mail.from.name', 'Desnky Global Resources');
            $adminAddress = (string) Config::get('mail.admin.address', 'info@desnkygroup.com');

            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($adminAddress);

            if (filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($replyTo);
            }

            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();

            return true;
        } catch (Throwable $exception) {
            $this->logger->error('Mail delivery failed', [
                'subject' => $subject,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
