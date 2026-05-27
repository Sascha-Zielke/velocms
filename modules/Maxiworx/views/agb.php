<?php $this->extend('maxiworx'); ?>

<?php $this->section('title'); ?>AGB<?php $this->endSection(); ?>
<?php $this->section('meta_description'); ?>Allgemeine Geschäftsbedingungen — Maxiworx, Superior Music Production Munich.<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<?php
$heroLabel    = e($innerHero['label']    ?? 'Legal');
$heroTitle    = e($innerHero['title']    ?? 'AGB');
$heroSubtitle = e($innerHero['subtitle'] ?? 'Allgemeine Geschäftsbedingungen');
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
                ⚠ Platzhalter — bitte mit rechtskonformen AGB ersetzen (durch einen Anwalt oder AGB-Generator erstellen lassen).
            </div>
            <div class="mw-prose">
                <h2>§ 1 Geltungsbereich</h2>
                <p>Diese Allgemeinen Geschäftsbedingungen gelten für alle Leistungen, die Maxiworx (nachfolgend „Auftragnehmer") gegenüber dem Auftraggeber erbringt, sofern nicht ausdrücklich schriftlich etwas anderes vereinbart wurde.</p>
                <h2>§ 2 Leistungsumfang</h2>
                <p>Der Umfang der zu erbringenden Leistungen ergibt sich aus der jeweiligen Auftragsbestätigung oder dem individuell vereinbarten Angebot. Änderungen und Erweiterungen des Leistungsumfangs bedürfen der Schriftform.</p>
                <h2>§ 3 Vergütung und Zahlung</h2>
                <p>[Zahlungsbedingungen, Anzahlungen, Fälligkeiten hier eintragen]</p>
                <h2>§ 4 Stornierung und Absage</h2>
                <p>[Stornierungsbedingungen und Ausfall-Regelungen hier eintragen]</p>
                <h2>§ 5 Urheberrecht und Nutzungsrechte</h2>
                <p>[Regelungen zur Rechteübertragung an Aufnahmen, Mixen und Masters hier eintragen]</p>
                <h2>§ 6 Haftung</h2>
                <p>[Haftungsausschlüsse und -begrenzungen hier eintragen]</p>
                <h2>§ 7 Anwendbares Recht</h2>
                <p>Es gilt das Recht der Bundesrepublik Deutschland. Gerichtsstand ist München, sofern der Auftraggeber Kaufmann, juristische Person des öffentlichen Rechts oder öffentlich-rechtliches Sondervermögen ist.</p>
            </div>
        </div>
        <?php endif ?>
    </div>
</section>

<?php $this->endSection(); ?>
