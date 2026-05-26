<?php

declare(strict_types=1);

return [
    // Navigation
    'nav.dashboard'      => 'Dashboard',
    'nav.blog'           => 'Blog',
    'nav.pages'          => 'Pages',
    'nav.users'          => 'Users',
    'nav.settings'       => 'Settings',
    'nav.logout'         => 'Logout',
    'nav.media'          => 'Media',

    // Actions
    'action.save'        => 'Save',
    'action.delete'      => 'Delete',
    'action.edit'        => 'Edit',
    'action.new'         => 'Create New',
    'action.cancel'      => 'Cancel',
    'action.back'        => 'Back',
    'action.publish'     => 'Publish',
    'action.unpublish'   => 'Unpublish',
    'action.login'       => 'Login',
    'action.logout'      => 'Logout',
    'action.confirm'     => 'Confirm',
    'action.search'      => 'Search',

    // Fields
    'field.title'        => 'Title',
    'field.slug'         => 'Slug',
    'field.content'      => 'Content',
    'field.status'       => 'Status',
    'field.email'        => 'Email',
    'field.password'     => 'Password',
    'field.name'         => 'Name',
    'field.role'         => 'Role',
    'field.actions'      => 'Actions',
    'field.language'     => 'Language',
    'field.created_at'   => 'Created at',
    'field.updated_at'   => 'Updated at',
    'field.author'       => 'Author',
    'field.description'  => 'Description',

    // Status values
    'status.draft'       => 'Draft',
    'status.published'   => 'Published',
    'status.archived'    => 'Archived',

    // Success messages
    'success.saved'      => 'Saved successfully.',
    'success.deleted'    => 'Deleted successfully.',
    'success.published'  => 'Published successfully.',

    // Error messages
    'error.not_found'        => 'Entry not found.',
    'error.required'         => 'Required field missing: :field',
    'error.invalid_email'    => 'Invalid email address.',
    'error.invalid_login'    => 'Invalid credentials.',
    'error.unauthorized'     => 'Unauthorized.',
    'error.forbidden'        => 'Access denied.',
    'error.csrf'             => 'Invalid security token.',
    'error.title_required'   => 'Title is required.',
    'error.slug_taken'       => 'This slug is already taken.',
    'error.server'           => 'Server error. Please try again.',

    // Auth
    'auth.login'             => 'Login',
    'auth.login_headline'    => 'Welcome back',
    'auth.email'             => 'Email',
    'auth.password'          => 'Password',
    'auth.logout_success'    => 'Logged out successfully.',

    // Admin dashboard
    'dashboard.headline'     => 'Dashboard',
    'dashboard.welcome'      => 'Welcome to VeloCMS Admin.',

    // Pagination
    'pagination.prev'        => 'Previous',
    'pagination.next'        => 'Next',
    'pagination.of'          => 'of',

    // Contact module — Frontend
    'contact.headline'              => 'Contact',
    'contact.intro'                 => 'Fill in the form and we will get back to you as soon as possible.',
    'contact.field_name'            => 'Name',
    'contact.field_email'           => 'Email address',
    'contact.field_subject'         => 'Subject',
    'contact.field_message'         => 'Message',
    'contact.submit'                => 'Send message',
    'contact.success'               => 'Thank you! Your message has been sent successfully. We will get back to you.',
    'contact.consent_text'          => 'I have read the <a href="%s">privacy policy</a> and consent to the processing of my data to handle my enquiry. *',

    // Contact module — Validation errors
    'contact.error_name_required'   => 'Please enter your name.',
    'contact.error_name_too_long'   => 'Name must not exceed 255 characters.',
    'contact.error_email_invalid'   => 'Please enter a valid email address.',
    'contact.error_subject_too_long'=> 'Subject must not exceed 255 characters.',
    'contact.error_message_required'=> 'Please enter a message.',
    'contact.error_message_too_long'=> 'Message must not exceed 10,000 characters.',
    'contact.error_consent_required'=> 'Please consent to the privacy policy.',
    'contact.error_rate_limit'      => 'Too many requests. Please try again later.',

    // Contact module — Admin
    'contact.admin_menu'            => 'Contact',
    'contact.admin_settings'        => 'Settings',
    'contact.admin_view'            => 'Read message',
    'contact.admin_empty'           => 'No messages found.',
    'contact.admin_reply'           => 'Reply',
    'contact.admin_mark_spam'       => 'Mark as spam',
    'contact.admin_marked_spam'     => 'Marked as spam.',
    'contact.admin_purge'           => 'Purge old',
    'contact.admin_purge_confirm'   => 'Permanently delete all messages older than the configured retention period?',
    'contact.admin_purged'          => '%d message(s) deleted.',

    // Contact module — Status labels
    'contact.status_new'            => 'New',
    'contact.status_read'           => 'Read',
    'contact.status_replied'        => 'Replied',
    'contact.status_spam'           => 'Spam',

    // Contact module — Filter tabs
    'contact.filter_all'            => 'All',
    'contact.filter_new'            => 'New',
    'contact.filter_read'           => 'Read',
    'contact.filter_replied'        => 'Replied',
    'contact.filter_spam'           => 'Spam',

    // Sites module (Superadmin)
    'nav.sites'                  => 'Sites',
    'sites.headline'             => 'Site management',
    'sites.new'                  => 'New site',
    'sites.empty'                => 'No sites registered yet.',
    'sites.create_headline'      => 'Create new site',
    'sites.create_submit'        => 'Create site',
    'sites.edit_headline'        => 'Edit site',
    'sites.created'              => 'Site created. Please provision the database.',
    'sites.field_name'           => 'Site name',
    'sites.field_domain'         => 'Domain',
    'sites.field_www_alias'      => 'WWW alias',
    'sites.field_db_name'        => 'Database name',
    'sites.field_status'         => 'Status',
    'sites.hint_domain'          => 'Without http:// and without trailing slash, e.g. example.com',
    'sites.hint_www_alias'       => 'Optional. Will be resolved to the same site, e.g. www.example.com',
    'sites.hint_db_name'         => 'Letters, digits and underscores only. Used as the MySQL database name.',
    'sites.status_active'        => 'Active',
    'sites.status_suspended'     => 'Suspended',
    'sites.status_provisioning'  => 'Provisioning',
    'sites.provision_hint'       => 'Database not yet provisioned. Click "Create DB" to automatically create the MySQL database.',
    'sites.provision_btn'        => 'Create DB',
    'sites.provision_success'    => 'Database created successfully. Site is now active.',
    'sites.provision_failed'     => 'Could not create database. Please create it manually or check DB user privileges.',
    'sites.danger_zone'          => 'Danger zone',
    'sites.danger_hint'          => 'Deleting a site removes it from the registry. The database is not dropped.',
    'sites.delete_btn'           => 'Remove site from registry',
    'sites.error_domain_taken'   => 'This domain is already registered.',
    'sites.error_db_name_taken'  => 'This database name is already in use.',
    'sites.error_db_name_invalid'=> 'Invalid database name (letters, digits, underscores only, max 64 chars).',

    // Apps section (sidebar)
    'nav.apps'                         => 'Apps',
    'nav.translation'                  => 'Translations',

    // Translation App — general
    'translation.headline'             => 'Translation App',
    'translation.dashboard'            => 'Overview',
    'translation.settings'             => 'Settings',
    'translation.no_data'              => 'No translations yet.',

    // Translation App — status labels
    'translation.source_auto'          => 'AI-generated',
    'translation.source_manual'        => 'Manual',
    'translation.status_ok'            => 'Translated',
    'translation.status_stale'         => 'Outdated',
    'translation.status_missing'       => 'Missing',
    'translation.status_locked'        => 'Locked (manual)',

    // Translation App — actions
    'translation.action_translate_all' => 'Translate all pending',
    'translation.action_retranslate'   => 'Re-translate',
    'translation.action_unlock'        => 'Remove lock',
    'translation.action_export'        => 'Export CSV',
    'translation.action_import'        => 'Import CSV',

    // Translation App — settings form
    'translation.settings_languages'   => 'Active languages',
    'translation.settings_default'     => 'Default language',
    'translation.settings_provider'    => 'Translation provider',
    'translation.settings_deepl_hint'  => 'Add API key to .env as DEEPL_API_KEY.',
    'translation.settings_anth_hint'   => 'Add API key to .env as ANTHROPIC_API_KEY.',
    'translation.settings_saved'       => 'Settings saved.',

    // Translation App — progress
    'translation.progress_fields'      => 'Total fields',
    'translation.progress_auto'        => 'AI-generated',
    'translation.progress_manual'      => 'Manual',
    'translation.progress_stale'       => 'Outdated',
    'translation.progress_missing'     => 'Missing',

    // Translation App — editor
    'translation.editor'               => 'Editor',
    'translation.filter_all'           => 'All',
    'translation.filter_stale'         => 'Outdated',
    'translation.col_table'            => 'Table',
    'translation.col_field'            => 'Field',
    'translation.col_value'            => 'Translation',
    'translation.col_updated'          => 'Updated',
    'translation.save_manual'          => 'Save manually',
    'translation.confirm_unlock'       => 'Remove lock and release for auto-translation?',
    'translation.hint_empty_db'        => 'No translations in the database yet. The next time a post is saved, translations will be generated automatically.',
    'translation.entries'              => 'entries',
    'translation.settings_default_hint'=> 'The default language cannot be removed.',
    'translation.provider_deepl'       => 'DeepL (primary)',
    'translation.provider_anthropic'   => 'Anthropic Claude (fallback)',

    // Language switcher (frontend + admin)
    'lang.de'                          => 'German',
    'lang.en'                          => 'English',
    'lang.fr'                          => 'French',
    'lang.es'                          => 'Spanish',
    'lang.it'                          => 'Italian',
    'lang.nl'                          => 'Dutch',

    // Blog module
    'blog.headline'     => 'Blog',
    'blog.new'          => 'New post',
    'blog.edit'         => 'Edit post',
    'blog.empty'        => 'No posts yet.',
    'blog.back'         => 'Back to blog',
    'blog.excerpt'      => 'Excerpt',
    'blog.content'      => 'Content',
    'blog.cover_image'  => 'Cover image',
    'blog.translations' => 'Translations',
    'blog.trans_notice'  => 'Auto-translated — edit to override.',
    'blog.trans_pending' => 'Not yet translated — being generated in the background. Reload the page or click "Re-translate".',

    // Maintenance mode
    'maintenance.title'    => 'Maintenance',
    'maintenance.headline' => 'We\'ll be back soon',
    'maintenance.text'     => 'This website is currently undergoing maintenance. We\'ll be back up shortly.',

    // Password reset
    'password_reset.page_title_request' => 'Forgot password',
    'password_reset.page_title_form'    => 'Set new password',
    'password_reset.headline_request'   => 'Forgot your password?',
    'password_reset.headline_form'      => 'Set a new password',
    'password_reset.intro_request'      => 'Enter your email address. If an account exists, you will receive a reset link.',
    'password_reset.forgot_link'        => 'Forgot password?',
    'password_reset.back_to_login'      => 'Back to login',
    'password_reset.submit_request'     => 'Send reset link',
    'password_reset.submit_form'        => 'Save password',
    'password_reset.email_sent'         => 'If an account with this email address exists, a reset link has been sent.',
    'password_reset.token_invalid'      => 'The reset link is invalid or has expired. Please request a new one.',
    'password_reset.success'            => 'Your password has been changed successfully. You can now log in.',
    'password_reset.mail_subject'       => 'Password reset',
    'password_reset.mail_greeting'      => 'Hello',
    'password_reset.mail_body'          => 'You requested a password reset for your VeloCMS account. Click the link below to reset your password:',
    'password_reset.mail_expiry'        => 'The link is valid for 60 minutes.',
    'password_reset.mail_ignore'        => 'If you did not request a reset, you can safely ignore this email.',

    // Contact module — Settings
    'contact.settings_section_email'    => 'Email delivery',
    'contact.settings_section_spam'     => 'Spam protection',
    'contact.settings_section_dsgvo'    => 'Privacy (GDPR)',
    'contact.settings_recipient_email'  => 'Recipient email',
    'contact.settings_recipient_hint'   => 'Incoming messages are sent here. Empty = site_email is used.',
    'contact.settings_from_name'        => 'Sender name',
    'contact.settings_subject_prefix'   => 'Subject prefix',
    'contact.settings_rate_limit'       => 'Max messages per IP / hour',
    'contact.settings_rate_limit_hint'  => 'Messages over this limit will be rejected.',
    'contact.settings_store_messages'   => 'Store messages in database',
    'contact.settings_retention_days'   => 'Retention (days)',
    'contact.settings_retention_hint'   => 'Messages older than this value can be removed via "Purge old".',
    'contact.settings_privacy_url'      => 'Privacy policy URL',
];
