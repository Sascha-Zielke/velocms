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
];
