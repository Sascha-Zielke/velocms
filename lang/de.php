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
    'settings.app_url'                 => 'Website-URL (für Canonical + Sitemap)',
    'settings.app_url_hint'            => 'Vollständige URL ohne abschließenden Slash, z.B. https://meine-website.de',
    'settings.robots_txt'              => 'robots.txt Inhalt',
    'settings.robots_txt_hint'         => 'Leer lassen für Standardinhalt (Allow: /, Disallow: /admin + Sitemap-Link).',
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

    // Contact module — Frontend
    'contact.headline'              => 'Kontakt',
    'contact.intro'                 => 'Füllen Sie das Formular aus und wir melden uns so schnell wie möglich bei Ihnen.',
    'contact.field_name'            => 'Name',
    'contact.field_email'           => 'E-Mail-Adresse',
    'contact.field_subject'         => 'Betreff',
    'contact.field_message'         => 'Nachricht',
    'contact.submit'                => 'Nachricht senden',
    'contact.success'               => 'Vielen Dank! Ihre Nachricht wurde erfolgreich übermittelt. Wir melden uns bei Ihnen.',
    'contact.consent_text'          => 'Ich habe die <a href="%s">Datenschutzerklärung</a> gelesen und stimme der Verarbeitung meiner Daten zur Bearbeitung meiner Anfrage zu. *',

    // Contact module — Validation errors
    'contact.error_name_required'   => 'Bitte geben Sie Ihren Namen an.',
    'contact.error_name_too_long'   => 'Der Name darf maximal 255 Zeichen lang sein.',
    'contact.error_email_invalid'   => 'Bitte geben Sie eine gültige E-Mail-Adresse an.',
    'contact.error_subject_too_long'=> 'Der Betreff darf maximal 255 Zeichen lang sein.',
    'contact.error_message_required'=> 'Bitte geben Sie eine Nachricht ein.',
    'contact.error_message_too_long'=> 'Die Nachricht darf maximal 10.000 Zeichen lang sein.',
    'contact.error_consent_required'=> 'Bitte stimmen Sie der Datenschutzerklärung zu.',
    'contact.error_rate_limit'      => 'Zu viele Anfragen. Bitte versuchen Sie es später erneut.',

    // Contact module — Admin
    'contact.admin_menu'            => 'Kontakt',
    'contact.admin_settings'        => 'Einstellungen',
    'contact.admin_view'            => 'Nachricht lesen',
    'contact.admin_empty'           => 'Keine Nachrichten vorhanden.',
    'contact.admin_reply'           => 'Antworten',
    'contact.admin_mark_spam'       => 'Als Spam markieren',
    'contact.admin_marked_spam'     => 'Als Spam markiert.',
    'contact.admin_purge'           => 'Alte löschen',
    'contact.admin_purge_confirm'   => 'Alle Nachrichten älter als die konfigurierte Aufbewahrungsdauer unwiderruflich löschen?',
    'contact.admin_purged'          => '%d Nachricht(en) gelöscht.',

    // Contact module — Status labels
    'contact.status_new'            => 'Neu',
    'contact.status_read'           => 'Gelesen',
    'contact.status_replied'        => 'Beantwortet',
    'contact.status_spam'           => 'Spam',

    // Contact module — Filter tabs
    'contact.filter_all'            => 'Alle',
    'contact.filter_new'            => 'Neu',
    'contact.filter_read'           => 'Gelesen',
    'contact.filter_replied'        => 'Beantwortet',
    'contact.filter_spam'           => 'Spam',

    // Sites module (Superadmin)
    'nav.sites'                  => 'Sites',
    'sites.headline'             => 'Site-Verwaltung',
    'sites.new'                  => 'Neue Site',
    'sites.empty'                => 'Noch keine Sites registriert.',
    'sites.create_headline'      => 'Neue Site anlegen',
    'sites.create_submit'        => 'Site anlegen',
    'sites.edit_headline'        => 'Site bearbeiten',
    'sites.created'              => 'Site angelegt. Bitte Datenbank provisionieren.',
    'sites.field_name'           => 'Site-Name',
    'sites.field_domain'         => 'Domain',
    'sites.field_www_alias'      => 'WWW-Alias',
    'sites.field_db_name'        => 'Datenbankname',
    'sites.field_status'         => 'Status',
    'sites.hint_domain'          => 'Ohne http:// und ohne abschließenden Slash, z.B. example.com',
    'sites.hint_www_alias'       => 'Optional. Wird auf dieselbe Site weitergeleitet, z.B. www.example.com',
    'sites.hint_db_name'         => 'Nur Buchstaben, Ziffern und Unterstriche. Wird als MySQL-Datenbankname verwendet.',
    'sites.status_active'        => 'Aktiv',
    'sites.status_suspended'     => 'Gesperrt',
    'sites.status_provisioning'  => 'Wird eingerichtet',
    'sites.provision_hint'       => 'Datenbank noch nicht provisioniert. Klicke auf "DB anlegen", um die MySQL-Datenbank automatisch zu erstellen.',
    'sites.provision_btn'        => 'DB anlegen',
    'sites.provision_success'    => 'Datenbank erfolgreich angelegt. Site ist jetzt aktiv.',
    'sites.provision_failed'     => 'Datenbank konnte nicht angelegt werden. Bitte manuell erstellen oder DB-User-Rechte prüfen.',
    'sites.danger_zone'          => 'Gefahrenzone',
    'sites.danger_hint'          => 'Site löschen entfernt den Eintrag aus der Registry. Die Datenbank bleibt erhalten.',
    'sites.delete_btn'           => 'Site aus Registry entfernen',
    'sites.error_domain_taken'   => 'Diese Domain ist bereits registriert.',
    'sites.error_db_name_taken'  => 'Dieser Datenbankname wird bereits verwendet.',
    'sites.error_db_name_invalid'=> 'Datenbankname ungültig (nur Buchstaben, Ziffern, Unterstriche, max. 64 Zeichen).',

    // Maintenance mode
    'maintenance.title'    => 'Wartungsarbeiten',
    'maintenance.headline' => 'Wartungsarbeiten',
    'maintenance.text'     => 'Die Website wird gerade gewartet. Wir sind bald wieder für dich da.',

    // Password reset
    'password_reset.page_title_request' => 'Passwort vergessen',
    'password_reset.page_title_form'    => 'Neues Passwort setzen',
    'password_reset.headline_request'   => 'Passwort vergessen?',
    'password_reset.headline_form'      => 'Neues Passwort setzen',
    'password_reset.intro_request'      => 'Gib deine E-Mail-Adresse ein. Wenn ein Konto existiert, erhältst du einen Reset-Link.',
    'password_reset.forgot_link'        => 'Passwort vergessen?',
    'password_reset.back_to_login'      => 'Zurück zum Login',
    'password_reset.submit_request'     => 'Reset-Link senden',
    'password_reset.submit_form'        => 'Passwort speichern',
    'password_reset.email_sent'         => 'Falls ein Konto mit dieser E-Mail-Adresse existiert, wurde ein Reset-Link verschickt.',
    'password_reset.token_invalid'      => 'Der Reset-Link ist ungültig oder abgelaufen. Bitte fordere einen neuen an.',
    'password_reset.success'            => 'Dein Passwort wurde erfolgreich geändert. Du kannst dich jetzt anmelden.',
    'password_reset.mail_subject'       => 'Passwort zurücksetzen',
    'password_reset.mail_greeting'      => 'Hallo',
    'password_reset.mail_body'          => 'Du hast einen Passwort-Reset für dein VeloCMS-Konto angefordert. Klicke auf den folgenden Link, um dein Passwort zurückzusetzen:',
    'password_reset.mail_expiry'        => 'Der Link ist 60 Minuten gültig.',
    'password_reset.mail_ignore'        => 'Falls du keinen Reset angefordert hast, kannst du diese E-Mail ignorieren.',

    // Contact module — Settings
    'contact.settings_section_email'    => 'E-Mail-Versand',
    'contact.settings_section_spam'     => 'Spam-Schutz',
    'contact.settings_section_dsgvo'    => 'Datenschutz (DSGVO)',
    'contact.settings_recipient_email'  => 'Empfänger-E-Mail',
    'contact.settings_recipient_hint'   => 'Hierhin werden eingehende Nachrichten gesendet. Leer = site_email wird verwendet.',
    'contact.settings_from_name'        => 'Absender-Name',
    'contact.settings_subject_prefix'   => 'Betreff-Präfix',
    'contact.settings_rate_limit'       => 'Max. Nachrichten pro IP / Stunde',
    'contact.settings_rate_limit_hint'  => 'Nachrichten über diesem Limit werden abgelehnt.',
    'contact.settings_store_messages'   => 'Nachrichten in der Datenbank speichern',
    'contact.settings_retention_days'   => 'Aufbewahrung (Tage)',
    'contact.settings_retention_hint'   => 'Nachrichten älter als dieser Wert können über "Alte löschen" entfernt werden.',
    'contact.settings_privacy_url'      => 'URL der Datenschutzerklärung',

];
