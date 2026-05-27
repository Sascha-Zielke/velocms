<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Maxiworx\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Pages\Models\PagesModel;

class MaxiworxController extends Controller
{
    // ─── Page data helpers ───────────────────────────────────────────────────

    /**
     * Load all sections (with rows + boxes) for a given page slug.
     * Returns [] if the page doesn't exist in the DB yet (graceful degradation).
     */
    private function loadPage(string $slug): array
    {
        try {
            $model = new PagesModel();
            $page  = $model->getBySlug($slug);
            if (!$page) {
                return [];
            }
            return $model->getFullPage((int) $page['id']);
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Extract the `content` array from a single box at [section][row=0][box].
     * Returns [] when the path doesn't exist — callers use ?? for fallbacks.
     */
    private function boxContent(array $sections, int $secIdx, int $boxIdx = 0): array
    {
        $content = $sections[$secIdx]['rows'][0]['boxes'][$boxIdx]['data']['content'] ?? null;
        return is_array($content) ? $content : [];
    }

    /**
     * Extract the `content` arrays from all boxes in a section's first row.
     * Useful for sections that hold a list of cards (services, gear, portfolio).
     */
    private function sectionBoxes(array $sections, int $secIdx): array
    {
        $boxes = $sections[$secIdx]['rows'][0]['boxes'] ?? [];
        return array_map(
            fn(array $b): array => is_array($b['data']['content'] ?? null) ? $b['data']['content'] : [],
            $boxes
        );
    }

    // ─── Frontend actions ────────────────────────────────────────────────────

    public function home(): void
    {
        $sections  = $this->loadPage('home');

        // Section 0 → hero text
        $hero      = $this->boxContent($sections, 0);
        // Section 1 → portfolio / reference cards
        $portfolio = $this->sectionBoxes($sections, 1);
        // Section 2 → gear / hardware items
        $gear      = $this->sectionBoxes($sections, 2);
        // Section 3 → service cards
        $services  = $this->sectionBoxes($sections, 3);
        // Section 4 → CTA block
        $cta       = $this->boxContent($sections, 4);

        $this->render('home', compact('hero', 'portfolio', 'gear', 'services', 'cta'));
    }

    public function equipment(): void
    {
        $sections    = $this->loadPage('equipment');
        $innerHero   = $this->boxContent($sections, 0);   // label, title, subtitle
        $pageContent = $this->boxContent($sections, 1);   // html

        $this->render('equipment', compact('innerHero', 'pageContent'));
    }

    public function servicePreise(): void
    {
        $sections    = $this->loadPage('service-preise');
        $innerHero   = $this->boxContent($sections, 0);
        $services    = $this->sectionBoxes($sections, 1); // icon, title, items[], price
        $pageContent = $this->boxContent($sections, 2);   // html (Pakete & Bundles prose)

        $this->render('service-preise', compact('innerHero', 'services', 'pageContent'));
    }

    public function specials(): void
    {
        $sections    = $this->loadPage('specials');
        $innerHero   = $this->boxContent($sections, 0);
        $pageContent = $this->boxContent($sections, 1);

        $this->render('specials', compact('innerHero', 'pageContent'));
    }

    public function referenzen(): void
    {
        $sections    = $this->loadPage('referenzen');
        $innerHero   = $this->boxContent($sections, 0);
        $projects    = $this->sectionBoxes($sections, 1); // title, genre, description

        $this->render('referenzen', compact('innerHero', 'projects'));
    }

    public function kontakt(): void
    {
        $sections  = $this->loadPage('kontakt');
        $innerHero = $this->boxContent($sections, 0);
        $info      = $this->boxContent($sections, 1); // address, email, hours

        $this->render('kontakt', compact('innerHero', 'info'));
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
        $sections    = $this->loadPage('impressum');
        $innerHero   = $this->boxContent($sections, 0);
        $pageContent = $this->boxContent($sections, 1);

        $this->render('impressum', compact('innerHero', 'pageContent'));
    }

    public function datenschutz(): void
    {
        $sections    = $this->loadPage('datenschutz');
        $innerHero   = $this->boxContent($sections, 0);
        $pageContent = $this->boxContent($sections, 1);

        $this->render('datenschutz', compact('innerHero', 'pageContent'));
    }

    public function agb(): void
    {
        $sections    = $this->loadPage('agb');
        $innerHero   = $this->boxContent($sections, 0);
        $pageContent = $this->boxContent($sections, 1);

        $this->render('agb', compact('innerHero', 'pageContent'));
    }

    public function bookSession(): void
    {
        Auth::verifyCsrf();

        $name          = trim((string) $this->input('name', ''));
        $email         = trim((string) $this->input('email', ''));
        $sessionType   = trim((string) $this->input('session_type', ''));
        $preferredDate = trim((string) $this->input('preferred_date', ''));
        $phone         = trim((string) $this->input('phone', ''));
        $message       = trim((string) $this->input('message', ''));

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
