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
    'action.preview'     => 'Vorschau',
    'action.publish'     => 'Veröffentlichen',
    'action.unpublish'   => 'Depublizieren',
    'action.login'       => 'Anmelden',
    'action.logout'      => 'Abmelden',
    'action.confirm'     => 'Bestätigen',
    'action.search'      => 'Suchen',
    'action.home'        => 'Zur Startseite',

    // Fields
    'field.title'        => 'Titel',
    'field.title_en'     => 'Titel (EN)',
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
    'field.meta_title'   => 'Meta-Titel (SEO)',
    'field.meta_description' => 'Meta-Beschreibung (SEO)',
    'field.image_path'   => 'Bildpfad',
    'field.alt'          => 'Alt-Text',
    'field.label'        => 'Beschriftung',
    'field.url'          => 'URL',
    'field.height'       => 'Höhe',
    'field.video_id'     => 'Video-ID',
    'field.video_provider' => 'Anbieter',

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
    'error.required'         => 'Pflichtfeld fehlt.',
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

    // Pages module
    'pages.headline'     => 'Seiten',
    'pages.new'          => 'Neue Seite',
    'pages.edit'         => 'Seite bearbeiten',
    'pages.empty'        => 'Noch keine Seiten vorhanden.',

    // Visual Editor
    'editor.headline'        => 'Seiten-Editor',
    'editor.add_section'     => '+ Abschnitt',
    'editor.add_row'         => '+ Zeile',
    'editor.section'         => 'Abschnitt',
    'editor.row'             => 'Zeile',
    'editor.overlay'         => 'Overlay',
    'editor.bg_color'        => 'Hintergrundfarbe',
    'editor.cols'            => 'Spaltenbreite',
    'editor.video_2click_hint' => 'Video wird erst nach Klick geladen (DSGVO).',

    // Video consent (Frontend)
    'video.consent_btn'      => 'Video laden',
    'video.consent_hint'     => 'Klicken Sie, um das Video zu laden. Dabei werden Daten an den Anbieter übertragen.',

    // Confirm
    'confirm.delete'     => 'Wirklich löschen?',

    // Pagination
    'pagination.prev'    => 'Zurück',
    'pagination.next'    => 'Weiter',
    'pagination.of'      => 'von',

    // Media module
    'media.headline'    => 'Mediathek',
    'media.upload'      => 'Hochladen',
    'media.drop_hint'   => 'Dateien hier ablegen oder oben auf "Hochladen" klicken (JPG, PNG, GIF, WebP, PDF · max. 10 MB)',
    'media.empty'       => 'Noch keine Dateien hochgeladen.',
    'media.alt_title'   => 'Alt-Text bearbeiten',
    'media.alt_de'      => 'Alt-Text (Deutsch)',
    'media.alt_en'      => 'Alt-Text (Englisch)',
    'media.copy_url'    => 'URL kopieren',
    'media.delete_confirm' => 'Datei unwiderruflich löschen?',


    // Blog module
    'blog.headline'     => 'Blog',
    'blog.new'         => 'Neuer Beitrag',
    'blog.edit'        => 'Beitrag bearbeiten',
    'blog.empty'       => 'Noch keine Beiträge vorhanden.',
    'blog.back'        => 'Zum Blog',
    'blog.cover_image' => 'Titelbild (Pfad)',
    'blog.excerpt'     => 'Teaser (DE)',
    'blog.excerpt_en'  => 'Teaser (EN)',
    'blog.content'     => 'Inhalt (DE — HTML)',
    'blog.content_en'  => 'Inhalt (EN — HTML)',

];
