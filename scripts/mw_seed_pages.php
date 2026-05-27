<?php
/**
 * Maxiworx — Page Seed Script
 *
 * Bootstraps VeloCMS with the Maxiworx tenant context and creates all
 * pages/sections/rows/boxes in velocms_maxiworx.
 *
 * Run once on the server:
 *   php /var/www/velocms/scripts/mw_seed_pages.php
 *
 * Safe to re-run: existing pages (by slug) are skipped.
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// ── Bootstrap VeloCMS (loads .env, sets up tenant, gets DB) ──────────────────
// VeloCMS has no external dependencies — load its own autoloader-equivalent
spl_autoload_register(function (string $class): void {
    $map = [
        'VeloCMS\\Core\\' => BASE_PATH . '/core/',
        'VeloCMS\\Modules\\' => BASE_PATH . '/modules/',
    ];
    foreach ($map as $prefix => $dir) {
        if (!str_starts_with($class, $prefix)) continue;
        $rel  = str_replace('\\', '/', substr($class, strlen($prefix)));
        $file = $dir . $rel . '.php';
        if (file_exists($file)) { require $file; return; }
    }
});

// Load .env the same way App::loadEnv() does
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $key = trim($key); $val = trim($val, " \t\"'");
        $_ENV[$key] = $val;
        putenv("{$key}={$val}");
    }
}

// Load config.php to get DB host/user/pass, then connect directly to the
// Maxiworx tenant DB — bypassing Tenant::resolve() which forces single-site
// in CLI mode.
$config = require BASE_PATH . '/config/config.php';

\VeloCMS\Core\Database::connect(
    $config['db_host'],
    (int) ($config['db_port'] ?? 3306),
    'velocms_maxiworx',
    $config['db_user'],
    $config['db_pass']
);
$pdo = \VeloCMS\Core\Database::getInstance()->getPdo();

echo "Connected directly to velocms_maxiworx.\n";

// ── Helper functions ───────────────────────────────────────────────────────────

function pageExists(PDO $pdo, string $slug): bool
{
    $s = $pdo->prepare("SELECT id FROM velocms_pages WHERE slug=:s AND deleted_at IS NULL LIMIT 1");
    $s->execute([':s' => $slug]);
    return (bool) $s->fetchColumn();
}

function createPage(PDO $pdo, array $data): int
{
    $pdo->prepare(
        "INSERT INTO velocms_pages (slug,title,title_en,status,meta_title,meta_description)
         VALUES (:slug,:title,:title_en,:status,:meta_title,:meta_desc)"
    )->execute([
        ':slug'       => $data['slug'],
        ':title'      => $data['title'],
        ':title_en'   => $data['title_en']        ?? null,
        ':status'     => $data['status']           ?? 'published',
        ':meta_title' => $data['meta_title']       ?? null,
        ':meta_desc'  => $data['meta_description'] ?? null,
    ]);
    return (int) $pdo->lastInsertId();
}

function addSection(PDO $pdo, int $pageId, array $settings = []): int
{
    $ord = (int) $pdo->query(
        "SELECT COALESCE(MAX(sort_order),0)+1 FROM velocms_sections WHERE page_id={$pageId}"
    )->fetchColumn();
    $pdo->prepare(
        "INSERT INTO velocms_sections (page_id,sort_order,settings) VALUES (:pid,:ord,:set)"
    )->execute([':pid' => $pageId, ':ord' => $ord, ':set' => json_encode($settings)]);
    return (int) $pdo->lastInsertId();
}

function addRow(PDO $pdo, int $sectionId): int
{
    $ord = (int) $pdo->query(
        "SELECT COALESCE(MAX(sort_order),0)+1 FROM velocms_rows WHERE section_id={$sectionId}"
    )->fetchColumn();
    $pdo->prepare(
        "INSERT INTO velocms_rows (section_id,sort_order,cols_config) VALUES (:sid,:ord,:cfg)"
    )->execute([':sid' => $sectionId, ':ord' => $ord, ':cfg' => '{}']);
    return (int) $pdo->lastInsertId();
}

function addBox(PDO $pdo, int $rowId, string $type, array $content): int
{
    $ord = (int) $pdo->query(
        "SELECT COALESCE(MAX(sort_order),0)+1 FROM velocms_boxes WHERE row_id={$rowId}"
    )->fetchColumn();
    $data = json_encode(['layout' => ['cols' => 12], 'content' => $content, 'settings' => []]);
    $pdo->prepare(
        "INSERT INTO velocms_boxes (row_id,sort_order,type,data) VALUES (:rid,:ord,:type,:data)"
    )->execute([':rid' => $rowId, ':ord' => $ord, ':type' => $type, ':data' => $data]);
    return (int) $pdo->lastInsertId();
}

// ── Seed pages ─────────────────────────────────────────────────────────────────

// 1. HOME ─────────────────────────────────────────────────────────────────────
if (pageExists($pdo, 'home')) {
    echo "  SKIP  home\n";
} else {
    $pid = createPage($pdo, [
        'slug'             => 'home',
        'title'            => 'Home',
        'title_en'         => 'Home',
        'status'           => 'published',
        'meta_title'       => 'Maxiworx — Superior Music Production',
        'meta_description' => 'Professional recording, mixing & mastering studio in Munich. Where sounds become legends.',
    ]);

    // Section 0: Hero
    $s = addSection($pdo, $pid, ['name' => 'hero']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', [
        'headline' => 'Where Sounds<br>Become Legends',
        'subline'  => 'Munich — Superior Music Production',
        'tagline'  => 'Recording · Mixing · Mastering',
    ]);

    // Section 1: Portfolio / References
    $s = addSection($pdo, $pid, ['name' => 'portfolio']);
    $r = addRow($pdo, $s);
    foreach ([
        ['title' => 'Project Alpha',    'genre' => 'Hip-Hop / Trap'],
        ['title' => 'Nachtklang EP',    'genre' => 'Electronic'],
        ['title' => 'Silverline Mix',   'genre' => 'R&B / Soul'],
        ['title' => 'Bassline Stories', 'genre' => 'House'],
    ] as $item) {
        addBox($pdo, $r, 'text', $item);
    }

    // Section 2: Hardware / Gear
    $s = addSection($pdo, $pid, ['name' => 'hardware']);
    $r = addRow($pdo, $s);
    foreach ([
        ['name' => 'SSL 4000 G Console',      'desc' => 'The legendary analog console that defined the sound of decades of chart-topping records.'],
        ['name' => 'Neve 1073 Preamps',        'desc' => 'Classic British warmth and character for vocals, guitars, and drums.'],
        ['name' => 'Manley VOXBOX',            'desc' => 'All-in-one channel strip — preamp, compressor, EQ, de-esser — precision in every chain.'],
        ['name' => 'Studer A827 Tape Machine', 'desc' => '24-track analog tape for artists who want that unmistakable warmth.'],
    ] as $item) {
        addBox($pdo, $r, 'text', $item);
    }

    // Section 3: Services
    $s = addSection($pdo, $pid, ['name' => 'services']);
    $r = addRow($pdo, $s);
    foreach ([
        ['icon' => '🎙', 'title' => 'Recording', 'text' => 'From single vocals to full live bands — our acoustic treatment and signal chain capture every nuance with pristine clarity.'],
        ['icon' => '🎚', 'title' => 'Mixing',    'text' => 'We blend depth, width, and dynamics to give your tracks that polished, radio-ready sound while keeping your vision intact.'],
        ['icon' => '💿', 'title' => 'Mastering', 'text' => 'Loudness, tonal balance, and streaming optimisation — every master is crafted for the platform and the audience.'],
    ] as $item) {
        addBox($pdo, $r, 'text', $item);
    }

    // Section 4: CTA
    $s = addSection($pdo, $pid, ['name' => 'cta']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', [
        'title' => 'Ready to Record<br>Your Next Hit?',
        'text'  => "Slots are limited. Book your session now and let's create something extraordinary together.",
    ]);

    echo "  DONE  home (5 sections, 13 boxes)\n";
}

// 2. EQUIPMENT ────────────────────────────────────────────────────────────────
if (pageExists($pdo, 'equipment')) {
    echo "  SKIP  equipment\n";
} else {
    $pid = createPage($pdo, [
        'slug' => 'equipment', 'title' => 'Equipment', 'status' => 'published',
        'meta_title' => 'Equipment — Maxiworx Studio Munich',
        'meta_description' => 'SSL-Console, Neve-Preamps, Manley, Studer — our full studio equipment list.',
    ]);
    $s = addSection($pdo, $pid, ['name' => 'inner-hero']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['label' => 'The Gear', 'title' => 'Equipment', 'subtitle' => 'State-of-the-art analog and digital signal chain.']);

    $s = addSection($pdo, $pid, ['name' => 'content']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['html' =>
        '<h2>Konsolen &amp; Signalkette</h2>' .
        '<ul><li>SSL 4000 G+ Mixing Console</li><li>Neve 1073 Preamps (×8)</li><li>API 512c Preamps (×4)</li>' .
        '<li>Manley VOXBOX Channel Strip</li><li>Universal Audio 1176 (Blackface)</li>' .
        '<li>Tube-Tech CL 1B Compressor</li><li>Pultec EQP-1A</li></ul>' .
        '<h2>Recorders &amp; Interfaces</h2>' .
        '<ul><li>Studer A827 24-Track Tape Machine</li><li>Avid Pro Tools HDX (192 I/O)</li>' .
        '<li>Lynx Aurora (n) 32-Channel Converter</li></ul>' .
        '<h2>Monitoring</h2>' .
        '<ul><li>Genelec 1037C Main Monitors</li><li>Yamaha NS-10M (mit Hafler P3000)</li>' .
        '<li>Auratone 5C Super Sound Cubes</li></ul>' .
        '<h2>Microphones</h2>' .
        '<ul><li>Neumann U87 Ai (×3)</li><li>AKG C414 B-XLS (×4)</li>' .
        '<li>Shure SM7B, SM57, SM58</li><li>Royer R-121 Ribbon (×2)</li><li>Sennheiser MD 421 (×4)</li></ul>'
    ]);
    echo "  DONE  equipment\n";
}

// 3. SERVICE & PREISE ─────────────────────────────────────────────────────────
if (pageExists($pdo, 'service-preise')) {
    echo "  SKIP  service-preise\n";
} else {
    $pid = createPage($pdo, [
        'slug' => 'service-preise', 'title' => 'Service & Preise', 'status' => 'published',
        'meta_title' => 'Service & Preise — Maxiworx Munich',
        'meta_description' => 'Recording, Mixing & Mastering — transparente Preise für professionelle Studioarbeit.',
    ]);
    $s = addSection($pdo, $pid, ['name' => 'inner-hero']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['label' => 'Leistungen', 'title' => 'Service & Preise', 'subtitle' => 'Transparente Konditionen — keine versteckten Kosten.']);

    $s = addSection($pdo, $pid, ['name' => 'service-cards']);
    $r = addRow($pdo, $s);
    foreach ([
        ['icon' => '🎙', 'title' => 'Recording', 'items' => json_encode(['Studio-Session ab 4 h', 'Vocal Booth + Live Room', 'Engineer inklusive', 'SSL + Outboard Gear']), 'price' => 'ab 120 €/h'],
        ['icon' => '🎚', 'title' => 'Mixing',    'items' => json_encode(['Analog Summing', 'Revision inklusive', 'Stem-Delivery', 'Streaming-optimiert']), 'price' => 'ab 250 €/Track'],
        ['icon' => '💿', 'title' => 'Mastering', 'items' => json_encode(['ISRC-Einbindung', 'Streaming + CD-Master', 'Loudness nach LUFS-Standard', '24h Delivery']), 'price' => 'ab 80 €/Track'],
    ] as $item) {
        addBox($pdo, $r, 'text', $item);
    }

    $s = addSection($pdo, $pid, ['name' => 'prose']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['html' =>
        '<h2>Pakete &amp; Bundles</h2>' .
        '<p>Für komplette Produktionen (Recording + Mixing + Mastering) bieten wir attraktive Paketpreise. Kontaktiere uns für ein individuelles Angebot.</p>' .
        '<h2>Inklusivleistungen</h2>' .
        '<ul><li>Kostenloser 30-minütiger Vorab-Call zur Projektbesprechung</li>' .
        '<li>Archivierung aller Rohdaten für 12 Monate</li>' .
        '<li>Revisions nach Vereinbarung</li><li>Stem-Export auf Anfrage</li></ul>'
    ]);
    echo "  DONE  service-preise\n";
}

// 4. SPECIALS ─────────────────────────────────────────────────────────────────
if (pageExists($pdo, 'specials')) {
    echo "  SKIP  specials\n";
} else {
    $pid = createPage($pdo, ['slug' => 'specials', 'title' => 'Specials', 'status' => 'published',
        'meta_description' => 'Exklusive Angebote und Aktionen bei Maxiworx.']);
    $s = addSection($pdo, $pid, ['name' => 'inner-hero']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['label' => 'Deals', 'title' => 'Specials', 'subtitle' => 'Aktuelle Angebote & limitierte Slots.']);
    $s = addSection($pdo, $pid, ['name' => 'content']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['html' =>
        '<h2>Early Bird — Sommer 2026</h2>' .
        '<p>Buche eine Full-Day-Session (min. 8 h) bis zum 30.06.2026 und erhalte kostenloses Mastering für bis zu 3 Tracks. Nur 5 Slots verfügbar.</p>' .
        '<h2>New Artist Package</h2>' .
        '<p>Du produzierst deine erste EP? Unser New-Artist-Paket beinhaltet 6 Stunden Recording, Mixing für 4 Tracks und Mastering — zum Einstiegspreis.</p>' .
        '<h2>Podcast Studio</h2>' .
        '<p>Halbtages-Slot (4 h) inkl. Audio-Nachbearbeitung, Intro/Outro-Schnitt und MP3-Delivery — ideal für Content Creator und Brands.</p>'
    ]);
    echo "  DONE  specials\n";
}

// 5. REFERENZEN ───────────────────────────────────────────────────────────────
if (pageExists($pdo, 'referenzen')) {
    echo "  SKIP  referenzen\n";
} else {
    $pid = createPage($pdo, ['slug' => 'referenzen', 'title' => 'Referenzen', 'status' => 'published',
        'meta_description' => 'Produktionen aus dem Maxiworx Studio — unsere Referenzen.']);
    $s = addSection($pdo, $pid, ['name' => 'inner-hero']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['label' => 'Portfolio', 'title' => 'Referenzen', 'subtitle' => 'Ausgewählte Produktionen aus unserem Katalog.']);
    $s = addSection($pdo, $pid, ['name' => 'projects']);
    $r = addRow($pdo, $s);
    foreach ([
        ['title' => 'Project Alpha',    'genre' => 'Hip-Hop / Trap', 'role' => 'Recording + Mixing'],
        ['title' => 'Nachtklang EP',    'genre' => 'Electronic',      'role' => 'Mixing + Mastering'],
        ['title' => 'Silverline Mix',   'genre' => 'R&B / Soul',      'role' => 'Mastering'],
        ['title' => 'Bassline Stories', 'genre' => 'House',           'role' => 'Full Production'],
        ['title' => 'Deep Cuts Vol. 2', 'genre' => 'Jazz Fusion',     'role' => 'Recording'],
        ['title' => 'Echo Chamber',     'genre' => 'Indie Pop',       'role' => 'Mixing + Mastering'],
        ['title' => 'Lowend Theory',    'genre' => 'Drum & Bass',     'role' => 'Mastering'],
        ['title' => 'Golden Hour',      'genre' => 'Neo-Soul',        'role' => 'Full Production'],
    ] as $item) {
        addBox($pdo, $r, 'text', $item);
    }
    echo "  DONE  referenzen (8 project boxes)\n";
}

// 6. KONTAKT ──────────────────────────────────────────────────────────────────
if (pageExists($pdo, 'kontakt')) {
    echo "  SKIP  kontakt\n";
} else {
    $pid = createPage($pdo, ['slug' => 'kontakt', 'title' => 'Kontakt', 'status' => 'published',
        'meta_description' => 'Kontaktiere Maxiworx — Superior Music Production Munich.']);
    $s = addSection($pdo, $pid, ['name' => 'inner-hero']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['label' => 'Get in Touch', 'title' => 'Kontakt', 'subtitle' => 'Fragen, Anfragen, Kooperationen — wir hören zu.']);
    $s = addSection($pdo, $pid, ['name' => 'contact-info']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', [
        'address' => "Maxiworx Studio\nMusterstraße 12\n80331 München",
        'email'   => 'kontakt@maxiworx.de',
        'hours'   => "Mo – Fr: 10:00 – 22:00 Uhr\nSa – So: nach Vereinbarung",
    ]);
    echo "  DONE  kontakt\n";
}

// 7. IMPRESSUM ────────────────────────────────────────────────────────────────
if (pageExists($pdo, 'impressum')) {
    echo "  SKIP  impressum\n";
} else {
    $pid = createPage($pdo, ['slug' => 'impressum', 'title' => 'Impressum', 'status' => 'published']);
    $s = addSection($pdo, $pid, ['name' => 'inner-hero']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['label' => 'Legal', 'title' => 'Impressum']);
    $s = addSection($pdo, $pid, ['name' => 'content']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['html' => '<!-- Impressum-Inhalt hier via Visual Editor eintragen -->']);
    echo "  DONE  impressum\n";
}

// 8. DATENSCHUTZ ──────────────────────────────────────────────────────────────
if (pageExists($pdo, 'datenschutz')) {
    echo "  SKIP  datenschutz\n";
} else {
    $pid = createPage($pdo, ['slug' => 'datenschutz', 'title' => 'Datenschutzerklärung', 'status' => 'published']);
    $s = addSection($pdo, $pid, ['name' => 'inner-hero']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['label' => 'Legal', 'title' => 'Datenschutzerklärung']);
    $s = addSection($pdo, $pid, ['name' => 'content']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['html' => '<!-- Datenschutzerklärung via Visual Editor eintragen -->']);
    echo "  DONE  datenschutz\n";
}

// 9. AGB ──────────────────────────────────────────────────────────────────────
if (pageExists($pdo, 'agb')) {
    echo "  SKIP  agb\n";
} else {
    $pid = createPage($pdo, ['slug' => 'agb', 'title' => 'AGB', 'status' => 'published']);
    $s = addSection($pdo, $pid, ['name' => 'inner-hero']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['label' => 'Legal', 'title' => 'AGB', 'subtitle' => 'Allgemeine Geschäftsbedingungen']);
    $s = addSection($pdo, $pid, ['name' => 'content']);
    $r = addRow($pdo, $s);
    addBox($pdo, $r, 'text', ['html' => '<!-- AGB via Visual Editor eintragen -->']);
    echo "  DONE  agb\n";
}

// ── Settings ──────────────────────────────────────────────────────────────────
$pdo->prepare(
    "INSERT INTO velocms_settings (`key`, `value`) VALUES ('homepage_slug', 'home')
     ON DUPLICATE KEY UPDATE `value` = 'home'"
)->execute();
echo "  SET   homepage_slug = home\n";

$pdo->prepare(
    "INSERT INTO velocms_settings (`key`, `value`) VALUES ('site_name', 'Maxiworx')
     ON DUPLICATE KEY UPDATE `value` = 'Maxiworx'"
)->execute();
echo "  SET   site_name = Maxiworx\n";

echo "\nSeed complete.\n";
