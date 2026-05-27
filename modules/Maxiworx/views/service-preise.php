<?php $this->extend('layouts/maxiworx'); ?>

<?php $this->section('title'); ?>Service & Preise<?php $this->endSection(); ?>
<?php $this->section('meta_description'); ?>Recording, Mixing &amp; Mastering — transparente Preise für professionelle Studioarbeit bei Maxiworx.<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<div class="mw-inner-hero">
    <div class="mw-container">
        <span class="mw-label mw-inner-hero__label">Leistungen</span>
        <h1 class="mw-inner-hero__title">Service &amp; Preise</h1>
        <p class="mw-inner-hero__sub">Transparente Konditionen — keine versteckten Kosten.</p>
    </div>
</div>

<section class="mw-section">
    <div class="mw-container">
        <div class="mw-placeholder-content">
            <div class="mw-placeholder-banner">
                💶 Die endgültigen Preise werden in Kürze veröffentlicht. Für ein individuelles Angebot — einfach anfragen.
            </div>
            <div class="mw-services__grid" style="margin-bottom:3rem">
                <?php
                $services = [
                    [
                        'title' => 'Recording',
                        'icon'  => '🎙',
                        'items' => ['Studio-Session ab 4 h', 'Vocal Booth + Live Room', 'Engineer inklusive', 'SSL + Outboard Gear'],
                        'price' => 'ab 120 €/h',
                    ],
                    [
                        'title' => 'Mixing',
                        'icon'  => '🎚',
                        'items' => ['Analog Summing', 'Revision inklusive', 'Stem-Delivery', 'Streaming-optimiert'],
                        'price' => 'ab 250 €/Track',
                    ],
                    [
                        'title' => 'Mastering',
                        'icon'  => '💿',
                        'items' => ['ISRC-Einbindung', 'Streaming + CD-Master', 'Loudness nach LUFS-Standard', '24h Delivery'],
                        'price' => 'ab 80 €/Track',
                    ],
                ];
                foreach ($services as $s): ?>
                <div class="mw-service-card">
                    <div class="mw-service-card__icon" aria-hidden="true"><?= $s['icon'] ?></div>
                    <h2 class="mw-service-card__title"><?= e($s['title']) ?></h2>
                    <ul class="mw-service-card__text" style="list-style:none;padding:0">
                        <?php foreach ($s['items'] as $item): ?>
                        <li style="padding:.15rem 0;color:rgba(255,255,255,0.55)">→ <?= e($item) ?></li>
                        <?php endforeach ?>
                    </ul>
                    <p style="font-family:'Barlow Condensed',sans-serif;font-size:1.25rem;font-weight:700;color:var(--mw-gold);margin-top:auto">
                        <?= e($s['price']) ?>
                    </p>
                    <div class="mw-service-card__line" aria-hidden="true"></div>
                </div>
                <?php endforeach ?>
            </div>
            <div class="mw-prose">
                <h2>Pakete &amp; Bundles</h2>
                <p>Für komplette Produktionen (Recording + Mixing + Mastering) bieten wir attraktive Paketpreise. Kontaktiere uns für ein individuelles Angebot — wir finden die passende Lösung für dein Budget.</p>
                <h2>Inklusivleistungen</h2>
                <ul>
                    <li>Kostenloser 30-minütiger Vorab-Call zur Projektbesprechung</li>
                    <li>Archivierung aller Rohdaten für 12 Monate</li>
                    <li>Revisions nach Vereinbarung</li>
                    <li>Stem-Export auf Anfrage</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php $this->endSection(); ?>
