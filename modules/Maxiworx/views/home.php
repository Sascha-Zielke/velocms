<?php $this->extend('maxiworx'); ?>

<?php $this->section('title'); ?>Maxiworx — Superior Music Production<?php $this->endSection(); ?>

<?php $this->section('meta_description'); ?>Professional recording, mixing & mastering studio in Munich. Where sounds become legends.<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<?php
// ── Fallback data (used when DB has not been seeded yet) ──────────────────────
$heroDefault = [
    'headline' => 'Where Sounds<br>Become Legends',
    'subline'  => 'Munich — Superior Music Production',
    'tagline'  => 'Recording · Mixing · Mastering',
];
$portfolioDefault = [
    ['title' => 'Project Alpha',    'genre' => 'Hip-Hop / Trap'],
    ['title' => 'Nachtklang EP',    'genre' => 'Electronic'],
    ['title' => 'Silverline Mix',   'genre' => 'R&B / Soul'],
    ['title' => 'Bassline Stories', 'genre' => 'House'],
];
$gearDefault = [
    ['name' => 'SSL 4000 G Console',      'desc' => 'The legendary analog console that defined the sound of decades of chart-topping records.'],
    ['name' => 'Neve 1073 Preamps',        'desc' => 'Classic British warmth and character for vocals, guitars, and drums.'],
    ['name' => 'Manley VOXBOX',            'desc' => 'All-in-one channel strip — preamp, compressor, EQ, de-esser — precision in every chain.'],
    ['name' => 'Studer A827 Tape Machine', 'desc' => '24-track analog tape for artists who want that unmistakable warmth.'],
];
$servicesDefault = [
    ['icon' => '🎙', 'title' => 'Recording', 'text' => 'From single vocals to full live bands — our acoustic treatment and signal chain capture every nuance with pristine clarity.'],
    ['icon' => '🎚', 'title' => 'Mixing',    'text' => 'We blend depth, width, and dynamics to give your tracks that polished, radio-ready sound while keeping your vision intact.'],
    ['icon' => '💿', 'title' => 'Mastering', 'text' => 'Loudness, tonal balance, and streaming optimisation — every master is crafted for the platform and the audience.'],
];
$ctaDefault = [
    'title' => 'Ready to Record<br>Your Next Hit?',
    'text'  => 'Slots are limited. Book your session now and let\'s create something extraordinary together.',
];

// ── Merge DB data over defaults ────────────────────────────────────────────────
$h  = array_merge($heroDefault,  $hero  ?? []);
$pf = !empty($portfolio) ? $portfolio : $portfolioDefault;
$gr = !empty($gear)      ? $gear      : $gearDefault;
$sv = !empty($services)  ? $services  : $servicesDefault;
$ct = array_merge($ctaDefault,   $cta   ?? []);
?>

<!-- ─── Hero ────────────────────────────────────────────────────────────────── -->
<?php $veSec0  = ($veMode ?? false) ? ($rawSections[0]['id'] ?? 0) : 0; ?>
<?php $veRBox0 = ($veMode ?? false) ? ($rawSections[0]['rows'][0]['boxes'][0] ?? []) : []; ?>
<?php $veBox0  = $veRBox0['id'] ?? 0; ?>
<section class="mw-hero" aria-label="Hero"
    <?= $veSec0 ? "data-ve-section=\"{$veSec0}\" data-ve-label=\"Hero\"" : '' ?>>
    <div class="mw-hero__bg"
         style="background-image:url('/assets/images/maxiworx/hero-studio.jpg')"
         role="presentation"></div>
    <div class="mw-hero__content" <?= $veBox0 ? "data-ve-box=\"{$veBox0}\" " . ve_gs_attrs($veRBox0) : '' ?>>
        <p class="mw-hero__sub"><?= e($h['subline']) ?></p>
        <h1 class="mw-hero__title"><?= $h['headline'] /* intentional — contains <br>, set in admin */ ?></h1>
        <p class="mw-hero__sub" style="margin-top:.5rem"><?= e($h['tagline']) ?></p>
    </div>
    <div class="mw-hero__scroll" aria-hidden="true"></div>
</section>

<!-- ─── References / Portfolio ───────────────────────────────────────────────── -->
<?php $veSec1 = ($veMode ?? false) ? ($rawSections[1]['id'] ?? 0) : 0; ?>
<section class="mw-section mw-portfolio" aria-label="Referenzen"
    <?= $veSec1 ? "data-ve-section=\"{$veSec1}\" data-ve-label=\"Referenzen\"" : '' ?>>
    <div class="mw-container">
        <span class="mw-label">Referenzen</span>
        <h2 class="mw-h2">Recent Productions</h2>
        <div class="mw-portfolio__grid" role="list">
            <?php foreach ($pf as $pIdx => $p): ?>
            <?php $veRBoxP = ($veMode ?? false) ? ($rawSections[1]['rows'][0]['boxes'][$pIdx] ?? []) : []; ?>
            <?php $veBoxP  = $veRBoxP['id'] ?? 0; ?>
            <div class="mw-portfolio__item" role="listitem" <?= $veBoxP ? "data-ve-box=\"{$veBoxP}\" " . ve_gs_attrs($veRBoxP) : '' ?>>
                <div class="mw-portfolio__placeholder">
                    <div style="text-align:center;padding:1rem">
                        <div style="font-family:'Barlow Condensed',sans-serif;font-size:0.9rem;font-weight:700;color:rgba(201,162,39,.6);margin-bottom:.3rem">
                            <?= e($p['title'] ?? '') ?>
                        </div>
                        <div style="font-size:.65rem;color:rgba(255,255,255,.2)">
                            <?= e($p['genre'] ?? '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach ?>
        </div>
        <a href="/referenzen" class="mw-link-arrow" style="margin-top:2rem;display:inline-flex">
            Alle Referenzen ansehen →
        </a>
    </div>
</section>

<!-- ─── Hardware Excellence ──────────────────────────────────────────────────── -->
<?php $veSec2 = ($veMode ?? false) ? ($rawSections[2]['id'] ?? 0) : 0; ?>
<section class="mw-section mw-hardware" aria-label="Hardware &amp; Equipment"
    <?= $veSec2 ? "data-ve-section=\"{$veSec2}\" data-ve-label=\"Hardware\"" : '' ?>>
    <div class="mw-container">
        <div class="mw-hardware__inner">

            <div class="mw-hardware__header">
                <span class="mw-label">The Gear</span>
                <h2 class="mw-h2">Hardware<br>Excellence</h2>
            </div>

            <div class="mw-hardware__items">
                <?php foreach ($gr as $gIdx => $item): ?>
                <?php $veRBoxG = ($veMode ?? false) ? ($rawSections[2]['rows'][0]['boxes'][$gIdx] ?? []) : []; ?>
                <?php $veBoxG  = $veRBoxG['id'] ?? 0; ?>
                <div class="mw-hardware__item" <?= $veBoxG ? "data-ve-box=\"{$veBoxG}\" " . ve_gs_attrs($veRBoxG) : '' ?>>
                    <h3 class="mw-h3"><?= e($item['name'] ?? '') ?></h3>
                    <p><?= e($item['desc'] ?? '') ?></p>
                </div>
                <?php endforeach ?>
                <a href="/equipment" class="mw-link-arrow">Full Equipment List →</a>
            </div>

            <div class="mw-hardware__images">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="mw-hardware__img">
                    <div class="mw-img-placeholder">Studio <?= $i ?></div>
                </div>
                <?php endfor ?>
            </div>

        </div>
    </div>
</section>

<!-- ─── Services ─────────────────────────────────────────────────────────────── -->
<?php $veSec3 = ($veMode ?? false) ? ($rawSections[3]['id'] ?? 0) : 0; ?>
<section class="mw-section" aria-label="Services"
    <?= $veSec3 ? "data-ve-section=\"{$veSec3}\" data-ve-label=\"Services\"" : '' ?>>
    <div class="mw-container">
        <div class="mw-services__header">
            <span class="mw-label">What We Do</span>
            <h2 class="mw-h2">Beyond the Booth</h2>
        </div>
        <div class="mw-services__grid">
            <?php foreach ($sv as $sIdx => $s): ?>
            <?php $veRBoxS = ($veMode ?? false) ? ($rawSections[3]['rows'][0]['boxes'][$sIdx] ?? []) : []; ?>
            <?php $veBoxS  = $veRBoxS['id'] ?? 0; ?>
            <div class="mw-service-card" <?= $veBoxS ? "data-ve-box=\"{$veBoxS}\" " . ve_gs_attrs($veRBoxS) : '' ?>>
                <div class="mw-service-card__icon" aria-hidden="true"><?= e($s['icon'] ?? '') ?></div>
                <h3 class="mw-service-card__title"><?= e($s['title'] ?? '') ?></h3>
                <p class="mw-service-card__text"><?= e($s['text'] ?? '') ?></p>
                <div class="mw-service-card__line" aria-hidden="true"></div>
            </div>
            <?php endforeach ?>
        </div>
        <div style="margin-top:2.5rem">
            <a href="/service-preise" class="mw-link-arrow">Alle Services &amp; Preise ansehen →</a>
        </div>
    </div>
</section>

<!-- ─── CTA ──────────────────────────────────────────────────────────────────── -->
<?php $veSec4  = ($veMode ?? false) ? ($rawSections[4]['id'] ?? 0) : 0; ?>
<?php $veRBox4 = ($veMode ?? false) ? ($rawSections[4]['rows'][0]['boxes'][0] ?? []) : []; ?>
<?php $veBox4  = $veRBox4['id'] ?? 0; ?>
<section class="mw-section mw-cta" aria-label="Anfrage"
    <?= $veSec4 ? "data-ve-section=\"{$veSec4}\" data-ve-label=\"CTA\"" : '' ?>>
    <div class="mw-container">
        <div class="mw-cta__inner" <?= $veBox4 ? "data-ve-box=\"{$veBox4}\" " . ve_gs_attrs($veRBox4) : '' ?>>
            <div>
                <h2 class="mw-cta__title"><?= $ct['title'] /* may contain <br> */ ?></h2>
                <p class="mw-cta__sub"><?= e($ct['text']) ?></p>
            </div>
            <div class="mw-cta__form" style="flex-direction:column;align-items:flex-start;gap:0.5rem">
                <p class="mw-cta__label">Start with your e-mail</p>
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                    <input class="mw-input" type="email" placeholder="your@email.com"
                           aria-label="E-Mail für Session-Anfrage"
                           id="mw-cta-email">
                    <button class="mw-btn-primary" type="button" id="mw-cta-book">
                        Book Session →
                    </button>
                </div>
            </div>
            <div class="mw-waveform" aria-hidden="true">
                <span></span><span></span><span></span><span></span>
                <span></span><span></span><span></span><span></span>
            </div>
        </div>
    </div>
</section>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
/* CTA email → pre-fill overlay and open it */
document.getElementById('mw-cta-book')?.addEventListener('click', function () {
    var email = document.getElementById('mw-cta-email')?.value ?? '';
    var overlayEmail = document.getElementById('bs-email');
    if (overlayEmail && email) overlayEmail.value = email;
    document.getElementById('mw-open-overlay')?.click();
});
</script>
<?php $this->endSection(); ?>
