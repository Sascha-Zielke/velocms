<?php $this->extend('maxiworx'); ?>

<?php $this->section('title'); ?>Kontakt<?php $this->endSection(); ?>
<?php $this->section('meta_description'); ?>Kontaktiere Maxiworx — Superior Music Production Munich. Wir freuen uns auf dein Projekt.<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<?php
$heroLabel    = e($innerHero['label']    ?? 'Get in Touch');
$heroTitle    = e($innerHero['title']    ?? 'Kontakt');
$heroSubtitle = e($innerHero['subtitle'] ?? 'Fragen, Anfragen, Kooperationen — wir hören zu.');

// Contact info — editable via DB, fallback to studio placeholder
$infoAddress = $info['address']  ?? "Maxiworx Studio\nMusterstraße 12\n80331 München";
$infoEmail   = $info['email']    ?? 'kontakt@maxiworx.de';
$infoHours   = $info['hours']    ?? "Mo – Fr: 10:00 – 22:00 Uhr\nSa – So: nach Vereinbarung";
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
        <div class="mw-contact-grid">

            <!-- Info -->
            <div>
                <div class="mw-contact-info__item">
                    <span class="mw-contact-info__label">Studio</span>
                    <span class="mw-contact-info__value">
                        <?= nl2br(e($infoAddress)) ?>
                    </span>
                </div>
                <div class="mw-contact-info__item">
                    <span class="mw-contact-info__label">E-Mail</span>
                    <span class="mw-contact-info__value">
                        <a href="mailto:<?= e($infoEmail) ?>" style="color:var(--mw-gold)"><?= e($infoEmail) ?></a>
                    </span>
                </div>
                <div class="mw-contact-info__item">
                    <span class="mw-contact-info__label">Öffnungszeiten</span>
                    <span class="mw-contact-info__value">
                        <?= nl2br(e($infoHours)) ?>
                    </span>
                </div>
                <div class="mw-contact-info__item">
                    <span class="mw-contact-info__label">Session buchen</span>
                    <span class="mw-contact-info__value">
                        <button class="mw-btn-book" id="mw-open-overlay" type="button"
                                style="margin-top:.5rem">
                            Book Session
                        </button>
                    </span>
                </div>
            </div>

            <!-- Form -->
            <div>
                <?php if (!empty($_SESSION['mw_contact_success'])): ?>
                <div class="mw-form__success">
                    <?= e($_SESSION['mw_contact_success']) ?>
                    <?php unset($_SESSION['mw_contact_success']); ?>
                </div>
                <?php elseif (!empty($_SESSION['mw_contact_error'])): ?>
                <div class="mw-form__error">
                    <?= e($_SESSION['mw_contact_error']) ?>
                    <?php unset($_SESSION['mw_contact_error']); ?>
                </div>
                <?php endif ?>

                <form class="mw-form" method="POST" action="/kontakt">
                    <?= csrf_field() ?>
                    <div class="mw-form__row">
                        <div class="mw-form__group">
                            <label class="mw-form__label" for="c-name">Name *</label>
                            <input class="mw-form__input" id="c-name" name="name" type="text"
                                   placeholder="Max Mustermann" required autocomplete="name">
                        </div>
                        <div class="mw-form__group">
                            <label class="mw-form__label" for="c-email">E-Mail *</label>
                            <input class="mw-form__input" id="c-email" name="email" type="email"
                                   placeholder="max@example.com" required autocomplete="email">
                        </div>
                    </div>
                    <div class="mw-form__group">
                        <label class="mw-form__label" for="c-subject">Betreff</label>
                        <input class="mw-form__input" id="c-subject" name="subject" type="text"
                               placeholder="Worum geht es?">
                    </div>
                    <div class="mw-form__group">
                        <label class="mw-form__label" for="c-message">Nachricht *</label>
                        <textarea class="mw-form__textarea" id="c-message" name="message"
                                  placeholder="Beschreib dein Anliegen …" required></textarea>
                    </div>
                    <button class="mw-btn-primary" type="submit" style="align-self:flex-start">
                        Nachricht senden →
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

<?php $this->endSection(); ?>
