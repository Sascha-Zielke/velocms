<?php $this->extend('maxiworx'); ?>

<?php $this->section('title'); ?>Referenzen<?php $this->endSection(); ?>
<?php $this->section('meta_description'); ?>Produktionen aus dem Maxiworx Studio — unsere Referenzen aus Hip-Hop, Electronic, R&amp;B und mehr.<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<?php
$heroLabel    = e($innerHero['label']    ?? 'Portfolio');
$heroTitle    = e($innerHero['title']    ?? 'Referenzen');
$heroSubtitle = e($innerHero['subtitle'] ?? 'Ausgewählte Produktionen aus unserem Katalog.');

$refsDefault = [
    ['title' => 'Project Alpha',    'genre' => 'Hip-Hop / Trap', 'role' => 'Recording + Mixing'],
    ['title' => 'Nachtklang EP',    'genre' => 'Electronic',      'role' => 'Mixing + Mastering'],
    ['title' => 'Silverline Mix',   'genre' => 'R&B / Soul',      'role' => 'Mastering'],
    ['title' => 'Bassline Stories', 'genre' => 'House',           'role' => 'Full Production'],
    ['title' => 'Deep Cuts Vol. 2', 'genre' => 'Jazz Fusion',     'role' => 'Recording'],
    ['title' => 'Echo Chamber',     'genre' => 'Indie Pop',       'role' => 'Mixing + Mastering'],
    ['title' => 'Lowend Theory',    'genre' => 'Drum & Bass',     'role' => 'Mastering'],
    ['title' => 'Golden Hour',      'genre' => 'Neo-Soul',        'role' => 'Full Production'],
];

$refs = !empty($projects) ? $projects : $refsDefault;
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
        <?php if (empty($projects)): ?>
        <div class="mw-placeholder-banner" style="margin-bottom:3rem">
            🎵 Portfolio wird in Kürze mit echten Releases und Artwork befüllt.
        </div>
        <?php endif ?>
        <div class="mw-portfolio__grid">
            <?php foreach ($refs as $r): ?>
            <div class="mw-portfolio__item">
                <div class="mw-portfolio__placeholder">
                    <div style="text-align:center;padding:1rem">
                        <div style="font-family:'Barlow Condensed',sans-serif;font-size:0.9rem;font-weight:700;color:rgba(201,162,39,.6);margin-bottom:.3rem">
                            <?= e($r['title'] ?? '') ?>
                        </div>
                        <div style="font-size:.65rem;color:rgba(255,255,255,.25);margin-bottom:.4rem">
                            <?= e($r['genre'] ?? '') ?>
                        </div>
                        <div style="font-size:.6rem;color:rgba(255,255,255,.15);letter-spacing:.05em;text-transform:uppercase">
                            <?= e($r['role'] ?? '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<?php $this->endSection(); ?>
