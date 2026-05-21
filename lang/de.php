<?php

declare(strict_types=1);

return [
    // Navigation
    'nav.dashboard'      => 'Dashboard',
    'nav.blog'           => 'Blog',
    'nav.pages'          => 'Seiten',
    'nav.users'          => 'Benutzer',
    'nav.settings'       => 'Einstellungen',
    'nav.logout'         => 'Abmelden',
    'nav.media'          => 'Medien',

    // Actions
    'action.save'        => 'Speichern',
    'action.delete'      => 'Löschen',
    'action.edit'        => 'Bearbeiten',
    'action.new'         => 'Neu erstellen',
    'action.cancel'      => 'Abbrechen',
    'action.back'        => 'Zurück',
    'action.publish'     => 'Veröffentlichen',
    'action.unpublish'   => 'Depublizieren',
    'action.login'       => 'Anmelden',
    'action.logout'      => 'Abmelden',
    'action.confirm'     => 'Bestätigen',
    'action.search'      => 'Suchen',

    // Fields
    'field.title'        => 'Titel',
    'field.slug'         => 'Slug',
    'field.content'      => 'Inhalt',
    'field.status'       => 'Status',
    'field.email'        => 'E-Mail',
    'field.password'     => 'Passwort',
    'field.name'         => 'Name',
    'field.role'         => 'Rolle',
    'field.actions'      => 'Aktionen',
    'field.created_at'   => 'Erstellt am',
    'field.updated_at'   => 'Aktualisiert am',
    'field.author'       => 'Autor',
    'field.description'  => 'Beschreibung',

    // Status values
    'status.draft'       => 'Entwurf',
    'status.published'   => 'Veröffentlicht',
    'status.archived'    => 'Archiviert',

    // Success messages
    'success.saved'      => 'Erfolgreich gespeichert.',
    'success.deleted'    => 'Erfolgreich gelöscht.',
    'success.published'  => 'Erfolgreich veröffentlicht.',

    // Error messages
    'error.not_found'        => 'Eintrag nicht gefunden.',
    'error.required'         => 'Pflichtfeld fehlt: :field',
    'error.invalid_email'    => 'Ungültige E-Mail-Adresse.',
    'error.invalid_login'    => 'Ungültige Anmeldedaten.',
    'error.unauthorized'     => 'Nicht autorisiert.',
    'error.forbidden'        => 'Zugriff verweigert.',
    'error.csrf'             => 'Ungültiges Sicherheitstoken.',
    'error.title_required'   => 'Titel ist ein Pflichtfeld.',
    'error.slug_taken'       => 'Dieser Slug ist bereits vergeben.',
    'error.server'           => 'Serverfehler. Bitte versuche es erneut.',

    // Auth
    'auth.login'             => 'Anmelden',
    'auth.login_headline'    => 'Willkommen zurück',
    'auth.email'             => 'E-Mail',
    'auth.password'          => 'Passwort',
    'auth.logout_success'    => 'Erfolgreich abgemeldet.',

    // Admin dashboard
    'dashboard.headline'     => 'Dashboard',
    'dashboard.welcome'      => 'Willkommen im VeloCMS Admin.',

    // Pagination
    'pagination.prev'        => 'Zurück',
    'pagination.next'        => 'Weiter',
    'pagination.of'          => 'von',
];
