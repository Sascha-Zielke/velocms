<?php $this->extend('maxiworx'); ?>

<?php $this->section('title'); ?>Datenschutzerklärung<?php $this->endSection(); ?>
<?php $this->section('meta_description'); ?>Datenschutzerklärung — Maxiworx, Superior Music Production Munich.<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<?php
$heroLabel = e($innerHero['label'] ?? 'Legal');
$heroTitle = e($innerHero['title'] ?? 'Datenschutzerklärung');
?>

<div class="mw-inner-hero">
    <div class="mw-container">
        <span class="mw-label mw-inner-hero__label"><?= $heroLabel ?></span>
        <h1 class="mw-inner-hero__title"><?= $heroTitle ?></h1>
    </div>
</div>

<section class="mw-section">
    <div class="mw-container">
        <?php if (!empty($pageContent['html'])): ?>
        <div class="mw-prose"><?= safe_html($pageContent['html']) ?></div>
        <?php else: ?>
        <div class="mw-placeholder-content">
            <div class="mw-placeholder-banner">
                ⚠ Platzhalter — bitte mit einer vollständigen DSGVO-konformen Datenschutzerklärung ersetzen (z.B. via datenschutz-generator.de).
            </div>
            <div class="mw-prose">
                <h2>1. Datenschutz auf einen Blick</h2>
                <h3>Allgemeine Hinweise</h3>
                <p>Die folgenden Hinweise geben einen einfachen Überblick darüber, was mit Ihren personenbezogenen Daten passiert, wenn Sie diese Website besuchen. Personenbezogene Daten sind alle Daten, mit denen Sie persönlich identifiziert werden können.</p>
                <h3>Datenerfassung auf dieser Website</h3>
                <p>Die Datenverarbeitung auf dieser Website erfolgt durch den Websitebetreiber. Dessen Kontaktdaten können Sie dem Impressum dieser Website entnehmen.</p>
                <h2>2. Hosting</h2>
                <p>[Hosting-Anbieter und Ort der Datenverarbeitung eintragen]</p>
                <h2>3. Allgemeine Hinweise und Pflichtinformationen</h2>
                <h3>Datenschutz</h3>
                <p>Die Betreiber dieser Seiten nehmen den Schutz Ihrer persönlichen Daten sehr ernst. Wir behandeln Ihre personenbezogenen Daten vertraulich und entsprechend der gesetzlichen Datenschutzvorschriften sowie dieser Datenschutzerklärung.</p>
                <h3>Verantwortliche Stelle</h3>
                <p>[Name und Anschrift des Verantwortlichen gemäß Art. 4 Nr. 7 DSGVO eintragen]</p>
                <h2>4. Kontaktformular</h2>
                <p>Wenn Sie uns per Kontaktformular Anfragen zukommen lassen, werden Ihre Angaben aus dem Anfrageformular inklusive der von Ihnen dort angegebenen Kontaktdaten zwecks Bearbeitung der Anfrage und für den Fall von Anschlussfragen bei uns gespeichert. Diese Daten geben wir nicht ohne Ihre Einwilligung weiter. Rechtsgrundlage: Art. 6 Abs. 1 lit. b DSGVO.</p>
                <h2>5. Ihre Rechte</h2>
                <p>Sie haben jederzeit das Recht auf unentgeltliche Auskunft über Ihre gespeicherten personenbezogenen Daten, deren Herkunft und Empfänger und den Zweck der Datenverarbeitung sowie ggf. ein Recht auf Berichtigung oder Löschung dieser Daten.</p>
            </div>
        </div>
        <?php endif ?>
    </div>
</section>

<?php $this->endSection(); ?>
