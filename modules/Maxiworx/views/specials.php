<?php $this->extend('maxiworx'); ?>

<?php $this->section('title'); ?>Specials<?php $this->endSection(); ?>
<?php $this->section('meta_description'); ?>Exklusive Angebote und Aktionen bei Maxiworx — Superior Music Production Munich.<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<?php
$heroLabel    = e($innerHero['label']    ?? 'Deals');
$heroTitle    = e($innerHero['title']    ?? 'Specials');
$heroSubtitle = e($innerHero['subtitle'] ?? 'Aktuelle Angebote &amp; limitierte Slots.');
?>

<div class="mw-inner-hero">
    <div class="mw-container">
        <span class="mw-label mw-inner-hero__label"><?= $heroLabel ?></span>
        <h1 class="mw-inner-hero__title"><?= $heroTitle ?></h1>
        <p class="mw-inner-hero__sub"><?= $heroSubtitle ?></p>
    </div>
</div>

<section class="mw-section">
    <div class="mw-container">
        <?php if (!empty($pageContent['html'])): ?>
        <div class="mw-prose"><?= safe_html($pageContent['html']) ?></div>
        <?php else: ?>
        <div class="mw-placeholder-content">
            <div class="mw-placeholder-banner">
                ⚡ Aktuelle Specials werden in Kürze hier veröffentlicht. Trag dich in unsere Liste ein und erhalte als Erstes Bescheid.
            </div>
            <div class="mw-prose">
                <h2>Early Bird — Sommer 2026</h2>
                <p>Buche eine Full-Day-Session (min. 8 h) bis zum 30.06.2026 und erhalte kostenloses Mastering für bis zu 3 Tracks. Nur 5 Slots verfügbar.</p>
                <h2>New Artist Package</h2>
                <p>Du produzierst deine erste EP? Unser New-Artist-Paket beinhaltet 6 Stunden Recording, Mixing für 4 Tracks und Mastering — zum Einstiegspreis. Anfragen per Kontaktformular.</p>
                <h2>Podcast Studio</h2>
                <p>Halbtages-Slot (4 h) inkl. Audio-Nachbearbeitung, Intro/Outro-Schnitt und MP3-Delivery — ideal für Content Creator und Brands.</p>
            </div>
        </div>
        <?php endif ?>
    </div>
</section>

<?php $this->endSection(); ?>
