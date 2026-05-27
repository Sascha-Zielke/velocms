<?php $this->extend('maxiworx'); ?>

<?php $this->section('title'); ?>Impressum<?php $this->endSection(); ?>
<?php $this->section('meta_description'); ?>Impressum — Maxiworx, Superior Music Production Munich.<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<?php
$heroLabel = e($innerHero['label'] ?? 'Legal');
$heroTitle = e($innerHero['title'] ?? 'Impressum');
?>

<?php $veSec0 = ($veMode ?? false) ? ($rawSections[0]['id'] ?? 0) : 0; ?>
<?php $veBox0 = ($veMode ?? false) ? ($rawSections[0]['rows'][0]['boxes'][0]['id'] ?? 0) : 0; ?>
<div class="mw-inner-hero" <?= $veSec0 ? "data-ve-section=\"{$veSec0}\" data-ve-label=\"Intro\"" : '' ?>>
    <div class="mw-container" <?= $veBox0 ? "data-ve-box=\"{$veBox0}\"" : '' ?>>
        <span class="mw-label mw-inner-hero__label"><?= $heroLabel ?></span>
        <h1 class="mw-inner-hero__title"><?= $heroTitle ?></h1>
    </div>
</div>

<?php $veSec1 = ($veMode ?? false) ? ($rawSections[1]['id'] ?? 0) : 0; ?>
<?php $veBox1 = ($veMode ?? false) ? ($rawSections[1]['rows'][0]['boxes'][0]['id'] ?? 0) : 0; ?>
<section class="mw-section" <?= $veSec1 ? "data-ve-section=\"{$veSec1}\" data-ve-label=\"Inhalt\"" : '' ?>>
    <div class="mw-container">
        <?php if (!empty($pageContent['html'])): ?>
        <div class="mw-prose" <?= $veBox1 ? "data-ve-box=\"{$veBox1}\"" : '' ?>><?= safe_html($pageContent['html']) ?></div>
        <?php else: ?>
        <div class="mw-placeholder-content">
            <div class="mw-placeholder-banner">
                ⚠ Platzhalter — bitte mit den echten Angaben gemäß § 5 TMG ersetzen.
            </div>
            <div class="mw-prose">
                <h2>Angaben gemäß § 5 TMG</h2>
                <p>
                    Maxiworx [Rechtsform eintragen]<br>
                    Musterstraße 12<br>
                    80331 München<br>
                    Deutschland
                </p>
                <h2>Kontakt</h2>
                <p>E-Mail: <a href="mailto:kontakt@maxiworx.de">kontakt@maxiworx.de</a></p>
                <h2>Umsatzsteuer-ID</h2>
                <p>Umsatzsteuer-Identifikationsnummer gemäß § 27a UStG: DE[Nummer eintragen]</p>
                <h2>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
                <p>[Name, Anschrift]</p>
                <h2>Haftung für Inhalte</h2>
                <p>Als Diensteanbieter sind wir gemäß § 7 Abs. 1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.</p>
                <h2>Haftung für Links</h2>
                <p>Unser Angebot enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich.</p>
                <h2>Urheberrecht</h2>
                <p>Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechts bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers.</p>
            </div>
        </div>
        <?php endif ?>
    </div>
</section>

<?php $this->endSection(); ?>
