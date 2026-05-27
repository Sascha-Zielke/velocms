<?php $this->extend('maxiworx'); ?>

<?php $this->section('title'); ?>Specials<?php $this->endSection(); ?>
<?php $this->section('meta_description'); ?>Exklusive Angebote und Aktionen bei Maxiworx — Superior Music Production Munich.<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<?php
$heroLabel    = e($innerHero['label']    ?? 'Deals');
$heroTitle    = e($innerHero['title']    ?? 'Specials');
$heroSubtitle = e($innerHero['subtitle'] ?? 'Aktuelle Angebote &amp; limitierte Slots.');
?>

<?php $veSec0  = ($veMode ?? false) ? ($rawSections[0]['id'] ?? 0) : 0; ?>
<?php $veRBox0 = ($veMode ?? false) ? ($rawSections[0]['rows'][0]['boxes'][0] ?? []) : []; ?>
<?php $veBox0  = $veRBox0['id'] ?? 0; ?>
<div class="mw-inner-hero" <?= $veSec0 ? "data-ve-section=\"{$veSec0}\" data-ve-label=\"Intro\"" : '' ?>>
    <div class="mw-container" <?= $veBox0 ? "data-ve-box=\"{$veBox0}\" " . ve_gs_attrs($veRBox0) : '' ?>>
        <span class="mw-label mw-inner-hero__label"><?= $heroLabel ?></span>
        <h1 class="mw-inner-hero__title"><?= $heroTitle ?></h1>
        <p class="mw-inner-hero__sub"><?= $heroSubtitle ?></p>
    </div>
</div>

<?php $veSec1  = ($veMode ?? false) ? ($rawSections[1]['id'] ?? 0) : 0; ?>
<?php $veRBox1 = ($veMode ?? false) ? ($rawSections[1]['rows'][0]['boxes'][0] ?? []) : []; ?>
<?php $veBox1  = $veRBox1['id'] ?? 0; ?>
<section class="mw-section" <?= $veSec1 ? "data-ve-section=\"{$veSec1}\" data-ve-label=\"Inhalt\"" : '' ?>>
    <div class="mw-container">
        <?php if (!empty($pageContent['html'])): ?>
        <div class="mw-prose" <?= $veBox1 ? "data-ve-box=\"{$veBox1}\" " . ve_gs_attrs($veRBox1) : '' ?>><?= safe_html($pageContent['html']) ?></div>
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
