<?php declare(strict_types=1); ?>
<?php $this->extend('frontend') ?>
<?php $this->section('content') ?>

<?php $lang = $_COOKIE['vcms_lang'] ?? 'de'; ?>

<?php if (!empty($page['meta_title']) || !empty($page['title'])): ?>
<?php /* Meta is handled by layout */ ?>
<?php endif ?>

<main class="vcms-page">
<?php foreach ($sections as $section): ?>
    <?php
    $settings = $section['settings'];
    $overlay  = (int)($settings['overlay'] ?? 0);
    $bgColor  = preg_replace('/[^#a-fA-F0-9]/', '', '#' . ($settings['bg_color'] ?? 'ffffff'));
    $padding  = in_array($settings['padding'] ?? '', ['none','sm','md','lg']) ? $settings['padding'] : 'md';
    ?>
    <section class="vcms-section vcms-section--pad-<?= e($padding) ?>"
             style="background-color: <?= e($bgColor) ?>; position:relative;">
        <?php if ($overlay > 0): ?>
        <div class="vcms-section-overlay" style="opacity: <?= $overlay / 100 ?>;"></div>
        <?php endif ?>

        <div class="vcms-container">
        <?php foreach ($section['rows'] as $row): ?>
            <div class="vcms-row">
            <?php foreach ($row['boxes'] as $box): ?>
                <?php
                $cols    = 12; // full width by default
                $boxData = $box['data']; // flat array: {text, text_en, src, alt, ...}
                ?>
                <div class="vcms-col vcms-col-<?= $cols ?>">
                    <?php if ($box['type'] === 'text'): ?>
                        <div class="vcms-text-box">
                            <?= safe_html(localized($boxData, 'text')) ?>
                        </div>

                    <?php elseif ($box['type'] === 'image'): ?>
                        <?php if (!empty($boxData['src'])): ?>
                        <img src="<?= e($boxData['src']) ?>"
                             alt="<?= e($boxData['alt'] ?? '') ?>"
                             loading="lazy"
                             class="vcms-img-box">
                        <?php endif ?>

                    <?php elseif ($box['type'] === 'button'): ?>
                        <?php if (!empty($boxData['href'])): ?>
                        <a href="<?= e($boxData['href']) ?>" class="vcms-btn-frontend">
                            <?= e($boxData['label'] ?? 'Button') ?>
                        </a>
                        <?php endif ?>

                    <?php elseif ($box['type'] === 'video'): ?>
                        <?php if (!empty($boxData['video_id'])): ?>
                        <!-- DSGVO: 2-Click Video -->
                        <div class="vcms-video-consent" data-video-id="<?= e($boxData['video_id']) ?>"
                             data-provider="<?= e($boxData['provider'] ?? 'youtube') ?>">
                            <div class="vcms-video-placeholder">
                                <button class="vcms-video-consent-btn">
                                    ▶ <?= t('video.consent_btn') ?>
                                </button>
                                <p class="vcms-video-consent-hint"><?= t('video.consent_hint') ?></p>
                            </div>
                        </div>
                        <?php endif ?>

                    <?php elseif ($box['type'] === 'spacer'): ?>
                        <div class="vcms-spacer"
                             style="height: <?= (int)($boxData['height'] ?? 40) ?>px;"></div>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
            </div>
        <?php endforeach ?>
        </div>
    </section>
<?php endforeach ?>
</main>

<?php $this->endSection() ?>
