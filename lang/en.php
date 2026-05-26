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
