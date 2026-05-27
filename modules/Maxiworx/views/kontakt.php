<?php $this->extend('layouts/maxiworx'); ?>

<?php $this->section('title'); ?>Kontakt<?php $this->endSection(); ?>
<?php $this->section('meta_description'); ?>Kontaktiere Maxiworx — Superior Music Production Munich. Wir freuen uns auf dein Projekt.<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<div class="mw-inner-hero">
    <div class="mw-container">
        <span class="mw-label mw-inner-hero__label">Get in Touch</span>
        <h1 class="mw-inner-hero__title">Kontakt</h1>
        <p class="mw-inner-hero__sub">Fragen, Anfragen, Kooperationen — wir hören zu.</p>
    </div>
</div>

<section class="mw-section">
    <div class="mw-container">
        <div class="mw-contact-grid">

            <!-- Info -->
            <div>
                <div class="mw-contact-info__item">
                    <span class="mw-contact-info__label">Studio</span>
                    <span class="mw-contact-info__value">Maxiworx Studio<br>Musterstraße 12<br>80331 München</span>
                </div>
                <div class="mw-contact-info__item">
                    <span class="mw-contact-info__label">E-Mail</span>
                    <span class="mw-contact-info__value">
                        <a href="mailto:kontakt@maxiworx.de" style="color:var(--mw-gold)">kontakt@maxiworx.de</a>
                    </span>
                </div>
                <div class="mw-contact-info__item">
                    <span class="mw-contact-info__label">Öffnungszeiten</span>
                    <span class="mw-contact-info__value">Mo – Fr: 10:00 – 22:00 Uhr<br>Sa – So: nach Vereinbarung</span>
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
