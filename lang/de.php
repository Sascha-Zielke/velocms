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

    // Navigation module
    'nav.navigation'          => 'Navigation',
    'nav.new_item'            => 'Neuer Menüpunkt',
    'nav.edit_item'           => 'Menüpunkt bearbeiten',
    'nav.empty'               => 'Noch keine Navigationspunkte vorhanden.',
    'nav.col_order'           => 'Reihenfolge',
    'action.move_up'          => 'Nach oben',
    'action.move_down'        => 'Nach unten',

    // Settings module
    'settings.section_site'            => 'Website',
    'settings.section_branding'        => 'Branding',
    'settings.section_seo'             => 'SEO',
    'settings.section_social'          => 'Social Media',
    'settings.section_footer'          => 'Footer',
    'settings.site_name'               => 'Website-Name',
    'settings.site_tagline'            => 'Tagline / Slogan',
    'settings.site_email'              => 'Kontakt-E-Mail',
    'settings.homepage_slug'           => 'Homepage-Slug',
    'settings.homepage_slug_hint'      => 'Slug der Seite, die als Startseite angezeigt wird (z.B. startseite).',
    'settings.maintenance_mode'        => 'Wartungsmodus',
    'settings.logo_path'               => 'Logo-Pfad',
    'settings.favicon_path'            => 'Favicon-Pfad',
    'settings.meta_title_suffix'       => 'Meta-Titel-Suffix',
    'settings.meta_description_default'=> 'Standard-Meta-Beschreibung',
    'settings.meta_keywords_default'   => 'Standard-Meta-Keywords',
    'settings.footer_text'             => 'Footer-Text',
    'settings.footer_impressum_url'    => 'Impressum-URL',
    'settings.footer_datenschutz_url'  => 'Datenschutz-URL',

    // Error pages
    'error.404_headline'     => 'Seite nicht gefunden',
    'error.404_text'         => 'Die gesuchte Seite existiert nicht oder wurde verschoben.',
    'error.403_headline'     => 'Zugriff verweigert',
    'error.403_text'         => 'Du hast keine Berechtigung für diesen Bereich.',
    'error.500_headline'     => 'Interner Serverfehler',
    'error.500_text'         => 'Es ist ein Fehler aufgetreten. Bitte versuche es später erneut.',

    // User management
    'users.headline'         => 'Benutzerverwaltung',
    'users.new'              => 'Neuer Benutzer',
    'users.edit'             => 'Benutzer bearbeiten',
    'users.empty'            => 'Noch keine Benutzer vorhanden.',
    'users.status'           => 'Status',
    'users.active'           => 'Aktiv',
    'users.inactive'         => 'Inaktiv',
    'users.you'              => 'Du',
    'users.last_login'       => 'Letzter Login',
    'users.section_profile'  => 'Profil',
    'users.section_password' => 'Passwort setzen',
    'users.password_hint'    => '(mind. 8 Zeichen)',
    'users.password_confirm' => 'Passwort bestätigen',
    'users.set_password'     => 'Passwort speichern',

    // Roles
    'role.editor'            => 'Editor',
    'role.admin'             => 'Admin',
    'role.superadmin'        => 'Superadmin',

    // User errors + success
    'error.email_taken'          => 'Diese E-Mail-Adresse wird bereits verwendet.',
    'error.password_min'         => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
    'error.password_mismatch'    => 'Die Passwörter stimmen nicht überein.',
    'error.cannot_delete_self'   => 'Du kannst deinen eigenen Account nicht löschen.',
    'success.user_created'       => 'Benutzer erfolgreich angelegt.',
    'success.password_changed'   => 'Passwort erfolgreich geändert.',
    'blog.cover_image' => 'Titelbild (Pfad)',
    'blog.excerpt'     => 'Teaser (DE)',
    'blog.excerpt_en'  => 'Teaser (EN)',
    'blog.content'     => 'Inhalt (DE — HTML)',
    'blog.content_en'  => 'Inhalt (EN — HTML)',

];
