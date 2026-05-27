<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Maxiworx\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;

class MaxiworxController extends Controller
{
    public function home(): void
    {
        $this->render('home');
    }

    public function equipment(): void
    {
        $this->render('equipment');
    }

    public function servicePreise(): void
    {
        $this->render('service-preise');
    }

    public function specials(): void
    {
        $this->render('specials');
    }

    public function referenzen(): void
    {
        $this->render('referenzen');
    }

    public function kontakt(): void
    {
        $this->render('kontakt');
    }

    public function kontaktSend(): void
    {
        Auth::verifyCsrf();

        $name    = trim((string) $this->input('name', ''));
        $email   = trim((string) $this->input('email', ''));
        $subject = trim((string) $this->input('subject', ''));
        $message = trim((string) $this->input('message', ''));

        if ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['mw_contact_error'] = 'Bitte fülle alle Pflichtfelder korrekt aus.';
            $this->redirect('/kontakt');
        }

        $to      = 'kontakt@maxiworx.de';
        $subject = $subject !== '' ? '[Maxiworx Kontakt] ' . $subject : '[Maxiworx Kontakt] Neue Anfrage';
        $body    = "Name: {$name}\nE-Mail: {$email}\n\n{$message}";
        $headers = "From: noreply@maxiworx.de\r\nReply-To: {$email}";

        @mail($to, $subject, $body, $headers);

        $_SESSION['mw_contact_success'] = 'Vielen Dank! Deine Nachricht wurde gesendet. Wir melden uns innerhalb von 24 Stunden.';
        $this->redirect('/kontakt');
    }

    public function impressum(): void
    {
        $this->render('impressum');
    }

    public function datenschutz(): void
    {
        $this->render('datenschutz');
    }

    public function agb(): void
    {
        $this->render('agb');
    }

    public function bookSession(): void
    {
        Auth::verifyCsrf();

        $name         = trim((string) $this->input('name', ''));
        $email        = trim((string) $this->input('email', ''));
        $sessionType  = trim((string) $this->input('session_type', ''));
        $preferredDate = trim((string) $this->input('preferred_date', ''));
        $phone        = trim((string) $this->input('phone', ''));
        $message      = trim((string) $this->input('message', ''));

        $allowed = ['recording', 'mixing', 'mastering', 'podcast', 'other'];

        if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || !in_array($sessionType, $allowed, true)) {
            $_SESSION['mw_booking_error'] = 'Bitte fülle alle Pflichtfelder korrekt aus.';
            $this->redirect('/');
        }

        $to      = 'booking@maxiworx.de';
        $subject = '[Maxiworx] Session-Anfrage: ' . ucfirst($sessionType);
        $body    = "Name: {$name}\n"
                 . "E-Mail: {$email}\n"
                 . "Telefon: {$phone}\n"
                 . "Session-Typ: {$sessionType}\n"
                 . "Wunschdatum: {$preferredDate}\n\n"
                 . $message;
        $headers = "From: noreply@maxiworx.de\r\nReply-To: {$email}";

        @mail($to, $subject, $body, $headers);

        $_SESSION['mw_booking_success'] = 'Perfekt! Deine Anfrage ist angekommen. Wir bestätigen deinen Slot innerhalb von 24 Stunden.';
        $this->redirect('/');
    }
}
