<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Maxiworx\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Pages\Models\PagesModel;

class MaxiworxController extends Controller
{
    // ─── Page data helpers ───────────────────────────────────────────────────

    /** True when a logged-in admin opens the page with ?ve_edit=1 or ?ve_embedded=1 */
    private function isVeMode(): bool
    {
        return Auth::check() && (isset($_GET['ve_edit']) || isset($_GET['ve_embedded']));
    }

    /** Return the DB id of a section by index, or 0 if not found. */
    private function sectionId(array $sections, int $secIdx): int
    {
        return (int) ($sections[$secIdx]['id'] ?? 0);
    }

    /** Return the DB id of a box at [secIdx][row=0][boxIdx], or 0 if not found. */
    private function boxId(array $sections, int $secIdx, int $boxIdx = 0): int
    {
        return (int) ($sections[$secIdx]['rows'][0]['boxes'][$boxIdx]['id'] ?? 0);
    }


    /**
     * Load all sections (with rows + boxes) for a given page slug.
     * Returns [pageId, sections] — pageId is 0 when the page doesn't exist yet.
     */
    private function loadPage(string $slug): array
    {
        try {
            $model = new PagesModel();
            $page  = $model->getBySlug($slug);
            if (!$page) {
                return [0, []];
            }
            $id = (int) $page['id'];
            return [$id, $model->getFullPage($id)];
        } catch (\Throwable) {
            return [0, []];
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
        [$pageId, $rawSections] = $this->loadPage('home');
        $veMode      = $this->isVeMode();

        // Section 0 → hero text
        $hero      = $this->boxContent($rawSections, 0);
        // Section 1 → portfolio / reference cards
        $portfolio = $this->sectionBoxes($rawSections, 1);
        // Section 2 → gear / hardware items
        $gear      = $this->sectionBoxes($rawSections, 2);
        // Section 3 → service cards
        $services  = $this->sectionBoxes($rawSections, 3);
        // Section 4 → CTA block
        $cta       = $this->boxContent($rawSections, 4);

        $this->render('home', compact('hero', 'portfolio', 'gear', 'services', 'cta', 'veMode', 'rawSections', 'pageId'));
    }

    public function equipment(): void
    {
        [$pageId, $rawSections] = $this->loadPage('equipment');
        $veMode      = $this->isVeMode();
        $innerHero   = $this->boxContent($rawSections, 0);   // label, title, subtitle
        $pageContent = $this->boxContent($rawSections, 1);   // html

        $this->render('equipment', compact('innerHero', 'pageContent', 'veMode', 'rawSections', 'pageId'));
    }

    public function servicePreise(): void
    {
        [$pageId, $rawSections] = $this->loadPage('service-preise');
        $veMode      = $this->isVeMode();
        $innerHero   = $this->boxContent($rawSections, 0);
        $services    = $this->sectionBoxes($rawSections, 1); // icon, title, items[], price
        $pageContent = $this->boxContent($rawSections, 2);   // html (Pakete & Bundles prose)

        $this->render('service-preise', compact('innerHero', 'services', 'pageContent', 'veMode', 'rawSections', 'pageId'));
    }

    public function specials(): void
    {
        [$pageId, $rawSections] = $this->loadPage('specials');
        $veMode      = $this->isVeMode();
        $innerHero   = $this->boxContent($rawSections, 0);
        $pageContent = $this->boxContent($rawSections, 1);

        $this->render('specials', compact('innerHero', 'pageContent', 'veMode', 'rawSections', 'pageId'));
    }

    public function referenzen(): void
    {
        [$pageId, $rawSections] = $this->loadPage('referenzen');
        $veMode      = $this->isVeMode();
        $innerHero   = $this->boxContent($rawSections, 0);
        $projects    = $this->sectionBoxes($rawSections, 1); // title, genre, description

        $this->render('referenzen', compact('innerHero', 'projects', 'veMode', 'rawSections', 'pageId'));
    }

    public function kontakt(): void
    {
        [$pageId, $rawSections] = $this->loadPage('kontakt');
        $veMode      = $this->isVeMode();
        $innerHero   = $this->boxContent($rawSections, 0);
        $info        = $this->boxContent($rawSections, 1); // address, email, hours

        $this->render('kontakt', compact('innerHero', 'info', 'veMode', 'rawSections', 'pageId'));
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
        [$pageId, $rawSections] = $this->loadPage('impressum');
        $veMode      = $this->isVeMode();
        $innerHero   = $this->boxContent($rawSections, 0);
        $pageContent = $this->boxContent($rawSections, 1);

        $this->render('impressum', compact('innerHero', 'pageContent', 'veMode', 'rawSections', 'pageId'));
    }

    public function datenschutz(): void
    {
        [$pageId, $rawSections] = $this->loadPage('datenschutz');
        $veMode      = $this->isVeMode();
        $innerHero   = $this->boxContent($rawSections, 0);
        $pageContent = $this->boxContent($rawSections, 1);

        $this->render('datenschutz', compact('innerHero', 'pageContent', 'veMode', 'rawSections', 'pageId'));
    }

    public function agb(): void
    {
        [$pageId, $rawSections] = $this->loadPage('agb');
        $veMode      = $this->isVeMode();
        $innerHero   = $this->boxContent($rawSections, 0);
        $pageContent = $this->boxContent($rawSections, 1);

        $this->render('agb', compact('innerHero', 'pageContent', 'veMode', 'rawSections', 'pageId'));
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
