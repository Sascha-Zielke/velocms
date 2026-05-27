<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Core\Services;

use VeloCMS\Modules\Booking\Core\Entities\Booking;
use VeloCMS\Modules\Booking\Core\Entities\Resource;

class BookingMailer
{
    private string $siteName;
    private string $fromAddress;
    private string $adminEmail;

    public function __construct()
    {
        $this->siteName    = function_exists('setting') ? setting('site_name', 'VeloCMS') : 'VeloCMS';
        $host              = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $this->fromAddress = 'noreply@' . $host;
        $this->adminEmail  = function_exists('setting') ? setting('site_email', '') : '';
    }

    public function sendCustomerConfirmation(Booking $booking, Resource $resource): void
    {
        $isPending = $booking->isPending();
        $subject   = '[' . $this->siteName . '] ' . t('booking.mail_confirm_subject') . ' #' . $booking->id;

        $body  = t('booking.mail_confirm_greeting') . ' ' . $booking->customerName . ",\n\n";
        $body .= $isPending
            ? t('booking.mail_confirm_pending')
            : t('booking.mail_confirm_body');
        $body .= "\n\n";
        $body .= t('booking.field_resource') . ': ' . $resource->name . "\n";
        $body .= t('booking.field_start_at') . ': ' . $booking->range->startUtc() . " UTC\n";
        $body .= t('booking.field_end_at')   . ': ' . $booking->range->endUtc()   . " UTC\n";
        if ($booking->notes !== null) {
            $body .= t('booking.field_notes') . ': ' . $booking->notes . "\n";
        }
        $body .= "\n" . t('booking.mail_confirm_footer') . "\n-- \n" . $this->siteName;

        $this->send($booking->customerEmail, $subject, $body);
    }

    public function sendAdminNotification(Booking $booking, Resource $resource): void
    {
        if ($this->adminEmail === '') {
            return;
        }

        $subject = '[' . $this->siteName . '] ' . t('booking.mail_admin_subject') . ' #' . $booking->id;

        $body  = t('booking.mail_admin_body') . "\n\n";
        $body .= 'ID: ' . $booking->id . "\n";
        $body .= t('booking.field_customer_name')  . ': ' . $booking->customerName  . "\n";
        $body .= t('booking.field_customer_email') . ': ' . $booking->customerEmail . "\n";
        if ($booking->customerPhone !== null) {
            $body .= t('booking.field_customer_phone') . ': ' . $booking->customerPhone . "\n";
        }
        $body .= t('booking.field_resource')  . ': ' . $resource->name . "\n";
        $body .= t('booking.field_start_at')  . ': ' . $booking->range->startUtc() . " UTC\n";
        $body .= t('booking.field_end_at')    . ': ' . $booking->range->endUtc()   . " UTC\n";
        $body .= t('booking.field_status')    . ': ' . $booking->status->label()   . "\n";

        $this->send($this->adminEmail, $subject, $body);
    }

    public function sendCancellationNotice(Booking $booking): void
    {
        $subject = '[' . $this->siteName . '] ' . t('booking.mail_canceled_subject') . ' #' . $booking->id;

        $body  = t('booking.mail_confirm_greeting') . ' ' . $booking->customerName . ",\n\n";
        $body .= t('booking.mail_canceled_body') . "\n\n";
        $body .= t('booking.field_start_at') . ': ' . $booking->range->startUtc() . " UTC\n";
        $body .= t('booking.field_end_at')   . ': ' . $booking->range->endUtc()   . " UTC\n";
        $body .= "\n-- \n" . $this->siteName;

        $this->send($booking->customerEmail, $subject, $body);
    }

    private function send(string $to, string $subject, string $body): void
    {
        // Strip newlines to prevent header injection
        $to      = str_replace(["\r", "\n"], '', $to);
        $subject = str_replace(["\r", "\n"], '', $subject);

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . str_replace(["\r", "\n"], '', $this->siteName)
                  . ' <' . $this->fromAddress . '>' . "\r\n";
        $headers .= 'X-Mailer: VeloCMS-Booking' . "\r\n";

        mail($to, $subject, $body, $headers);
    }
}
