<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Contact\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Contact\Models\ContactModel;
use VeloCMS\Modules\Settings\Models\SettingsModel;

class AdminContactController extends Controller
{
    private ContactModel   $model;
    private SettingsModel  $settings;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->model    = new ContactModel();
        $this->settings = new SettingsModel();
    }

    /**
     * GET /admin/contact — Inbox overview.
     */
    public function index(): void
    {
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $status  = in_array($_GET['status'] ?? '', ['new', 'read', 'replied', 'spam'], true)
                   ? ($_GET['status'])
                   : '';
        $perPage = 20;

        ['rows' => $rows, 'total' => $total] = $this->model->paginate($page, $perPage, $status);
        $pages = (int) ceil($total / $perPage);

        $this->render('admin/index', [
            'messages'    => $rows,
            'total'       => $total,
            'currentPage' => $page,
            'pages'       => $pages,
            'perPage'     => $perPage,
            'filter'      => $status,
            'unread'      => $this->model->countNew(),
        ]);
    }

    /**
     * GET /admin/contact/[i:id] — View a single message.
     */
    public function show(int $id): void
    {
        $msg = $this->model->find($id);
        if ($msg === null) {
            $this->redirectWithError('/admin/contact', t('error.not_found'));
        }

        // Mark as read when viewed
        if ($msg['status'] === 'new') {
            $this->model->markRead($id);
            $msg['status'] = 'read';
        }

        $this->render('admin/show', ['message' => $msg]);
    }

    /**
     * POST /admin/contact/[i:id]/spam — Mark as spam.
     */
    public function spam(int $id): void
    {
        Auth::verifyCsrf();
        $this->model->markSpam($id);
        $this->redirectWithSuccess('/admin/contact', t('contact.admin_marked_spam'));
    }

    /**
     * POST /admin/contact/[i:id]/delete — Soft-delete a message.
     */
    public function delete(int $id): void
    {
        Auth::verifyCsrf();
        $this->requireRole('admin');
        $this->model->softDelete($id);
        $this->redirectWithSuccess('/admin/contact', t('success.deleted'));
    }

    /**
     * GET /admin/contact/settings — Contact settings form.
     */
    public function settings(): void
    {
        $this->requireRole('admin');
        $this->render('admin/settings', [
            'settings' => $this->settings->getAll(),
        ]);
    }

    /**
     * POST /admin/contact/settings/save — Save contact settings.
     */
    public function saveSettings(): void
    {
        Auth::verifyCsrf();
        $this->requireRole('admin');

        $allowed = [
            'contact_recipient_email',
            'contact_from_name',
            'contact_subject_prefix',
            'contact_rate_limit',
            'contact_store_messages',
            'contact_retention_days',
            'contact_privacy_url',
        ];

        $data = [];
        foreach ($allowed as $key) {
            $data[$key] = trim($_POST[$key] ?? '');
        }

        // Sanitise numeric fields
        $data['contact_rate_limit']      = (string) max(1, (int) $data['contact_rate_limit']);
        $data['contact_retention_days']  = (string) max(1, (int) $data['contact_retention_days']);
        $data['contact_store_messages']  = $data['contact_store_messages'] === '1' ? '1' : '0';

        $this->settings->bulkSave($data);
        $this->redirectWithSuccess('/admin/contact/settings', t('success.saved'));
    }

    /**
     * POST /admin/contact/purge — DSGVO data purge (manual trigger).
     */
    public function purge(): void
    {
        Auth::verifyCsrf();
        $this->requireRole('admin');
        $days    = max(1, (int) setting('contact_retention_days', '90'));
        $deleted = $this->model->purgeOlderThan($days);
        $this->redirectWithSuccess('/admin/contact', sprintf(t('contact.admin_purged'), $deleted));
    }
}
