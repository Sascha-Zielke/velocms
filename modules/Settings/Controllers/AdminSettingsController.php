<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Settings\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Settings\Models\SettingsModel;

class AdminSettingsController extends Controller
{
    private SettingsModel $model;

    private const ALLOWED_KEYS = [
        'site_name', 'site_tagline', 'site_email',
        'logo_path', 'favicon_path',
        'meta_title_suffix', 'meta_description_default', 'meta_keywords_default',
        'social_facebook', 'social_instagram', 'social_linkedin', 'social_twitter',
        'footer_text', 'footer_impressum_url', 'footer_datenschutz_url',
        'homepage_slug', 'maintenance_mode',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
        $this->model = new SettingsModel();
    }

    public function index(): void
    {
        $this->view->extend('admin');
        $this->render('admin/settings/index', [
            'settings' => $this->model->getAll(),
        ]);
    }

    public function save(): void
    {
        Auth::verifyCsrf();

        $data = [];
        foreach (self::ALLOWED_KEYS as $key) {
            $value = trim((string) ($_POST[$key] ?? ''));
            if ($key === 'site_email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->redirectWithError('/admin/settings', t('error.invalid_email'));
            }
            $data[$key] = $value;
        }

        $this->model->bulkSave($data);
        $this->redirectWithSuccess('/admin/settings', t('success.saved'));
    }
}
