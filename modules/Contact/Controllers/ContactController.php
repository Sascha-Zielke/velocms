<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Contact\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Contact\Models\ContactModel;

class ContactController extends Controller
{
    private ContactModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new ContactModel();
    }

    /**
     * GET /kontakt — Show contact form.
     */
    public function show(): void
    {
        $this->render('frontend/contact', [
            'errors'  => [],
            'old'     => [],
            'success' => false,
        ]);
    }

    /**
     * POST /kontakt — Process contact form submission.
     */
    public function submit(): void
    {
        Auth::verifyCsrf();

        $ip        = $this->clientIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // ── Honeypot check ────────────────────────────────────────────────────
        // Bots fill every field; real users leave the hidden hp_url field empty.
        if (($_POST['hp_url'] ?? '') !== '') {
            // Silently pretend success so bots don't retry
            $this->render('frontend/contact', [
                'errors'  => [],
                'old'     => [],
                'success' => true,
            ]);
            return;
        }

        // ── Rate-limit ────────────────────────────────────────────────────────
        $rateLimit = (int) setting('contact_rate_limit', '3');
        if ($this->model->countRecentByIp($ip) >= $rateLimit) {
            $this->render('frontend/contact', [
                'errors'  => ['form' => t('contact.error_rate_limit')],
                'old'     => $this->collectOld(),
                'success' => false,
            ]);
            return;
        }

        // ── Validation ────────────────────────────────────────────────────────
        $name    = trim($_POST['name']    ?? '');
        $email   = trim($_POST['email']   ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $consent = !empty($_POST['consent']);

        $errors = [];

        if ($name === '') {
            $errors['name'] = t('contact.error_name_required');
        } elseif (mb_strlen($name) > 255) {
            $errors['name'] = t('contact.error_name_too_long');
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = t('contact.error_email_invalid');
        }

        if (mb_strlen($subject) > 255) {
            $errors['subject'] = t('contact.error_subject_too_long');
        }

        if ($message === '') {
            $errors['message'] = t('contact.error_message_required');
        } elseif (mb_strlen($message) > 10000) {
            $errors['message'] = t('contact.error_message_too_long');
        }

        if (!$consent) {
            $errors['consent'] = t('contact.error_consent_required');
        }

        if (!empty($errors)) {
            $this->render('frontend/contact', [
                'errors'  => $errors,
                'old'     => $this->collectOld(),
                'success' => false,
            ]);
            return;
        }

        // ── Store in DB (if enabled) ──────────────────────────────────────────
        if (setting('contact_store_messages', '1') === '1') {
            $this->model->create($name, $email, $subject, $message, $ip, $userAgent);
        }

        // ── Send e-mail ───────────────────────────────────────────────────────
        $this->sendMail($name, $email, $subject, $message);

        // ── Success ───────────────────────────────────────────────────────────
        $this->render('frontend/contact', [
            'errors'  => [],
            'old'     => [],
            'success' => true,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function collectOld(): array
    {
        return [
            'name'    => htmlspecialchars(trim($_POST['name']    ?? ''), ENT_QUOTES, 'UTF-8'),
            'email'   => htmlspecialchars(trim($_POST['email']   ?? ''), ENT_QUOTES, 'UTF-8'),
            'subject' => htmlspecialchars(trim($_POST['subject'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'message' => htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8'),
        ];
    }

    private function clientIp(): string
    {
        // Respect reverse-proxy headers if present, otherwise use REMOTE_ADDR
        foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = trim(explode(',', $_SERVER[$header])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    private function sendMail(
        string $senderName,
        string $senderEmail,
        string $subject,
        string $message
    ): void {
        $recipient    = setting('contact_recipient_email') ?: setting('site_email', '');
        $fromName     = setting('contact_from_name', 'Kontaktformular');
        $subjectPrefix = setting('contact_subject_prefix', '[Kontakt]');
        $siteName     = setting('site_name', 'Website');

        if ($recipient === '') {
            // No recipient configured — log and skip silently
            error_log('[VeloCMS Contact] No recipient email configured (contact_recipient_email / site_email).');
            return;
        }

        $mailSubject = trim("{$subjectPrefix} {$subject}") ?: "{$subjectPrefix} Neue Kontaktanfrage";
        $mailBody    = "Name: {$senderName}\r\n"
                     . "E-Mail: {$senderEmail}\r\n"
                     . "Nachricht:\r\n{$message}\r\n"
                     . "\r\n-- \r\nGesendet über das Kontaktformular von {$siteName}";

        $encodedFromName = mb_encode_mimeheader($fromName, 'UTF-8', 'Q');
        $encodedSubject  = mb_encode_mimeheader($mailSubject, 'UTF-8', 'Q');

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "Content-Transfer-Encoding: 8bit\r\n";
        $headers .= "From: {$encodedFromName} <{$recipient}>\r\n";
        $headers .= "Reply-To: {$senderName} <{$senderEmail}>\r\n";
        $headers .= "X-Mailer: VeloCMS\r\n";

        mail($recipient, $encodedSubject, $mailBody, $headers);
    }
}
