<?php declare(strict_types=1); ?>
<?php $this->extend('frontend') ?>
<?php $this->section('content') ?>

<?php $lang = $_COOKIE['vcms_lang'] ?? 'de'; ?>

<?php if (!empty($page['meta_title']) || !empty($page['title'])): ?>
<?php /* Meta is handled by layout */ ?>
<?php endif ?>

<main class="vcms-frontend-page">
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
                $cols = (int)($box['data']['layout']['cols'] ?? 12);
                $content = $box['data']['content'] ?? [];
                ?>
                <div class="vcms-col vcms-col-<?= $cols ?>">
                    <?php if ($box['type'] === 'text'): ?>
                        <div class="vcms-text-box">
                            <?= safe_html(localized($content, 'text')) ?>
                        </div>

                    <?php elseif ($box['type'] === 'image'): ?>
                        <?php if (!empty($content['src'])): ?>
                        <img src="<?= e($content['src']) ?>"
                             alt="<?= e($content['alt'] ?? '') ?>"
                             loading="lazy"
                             class="vcms-img-box">
                        <?php endif ?>

                    <?php elseif ($box['type'] === 'button'): ?>
                        <?php if (!empty($content['href'])): ?>
                        <a href="<?= e($content['href']) ?>" class="vcms-btn-frontend">
                            <?= e($content['label'] ?? 'Button') ?>
                        </a>
                        <?php endif ?>

                    <?php elseif ($box['type'] === 'video'): ?>
                        <?php if (!empty($content['video_id'])): ?>
                        <!-- DSGVO: 2-Click Video -->
                        <div class="vcms-video-consent" data-video-id="<?= e($content['video_id']) ?>"
                             data-provider="<?= e($content['provider'] ?? 'youtube') ?>">
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
                             style="height: <?= (int)($box['data']['settings']['height'] ?? 40) ?>px;"></div>
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
