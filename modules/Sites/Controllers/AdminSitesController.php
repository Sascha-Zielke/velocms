<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Sites\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Sites\Models\SiteModel;

class AdminSitesController extends Controller
{
    private SiteModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('superadmin');
        $this->model = new SiteModel();
    }

    public function index(): void
    {
        $this->view->extend('admin');
        $this->render('admin/sites/index', [
            'sites' => $this->model->getAll(),
        ]);
    }

    public function create(): void
    {
        $this->view->extend('admin');
        $this->render('admin/sites/create', []);
    }

    public function store(): void
    {
        Auth::verifyCsrf();

        $domain    = strtolower(trim($this->input('domain', '')));
        $wwwAlias  = strtolower(trim($this->input('www_alias', '')));
        $name      = trim($this->input('name', ''));
        $dbName    = trim($this->input('db_name', ''));

        // Validation
        if ($domain === '' || $name === '' || $dbName === '') {
            $this->redirectWithError('/admin/sites/create', t('error.required'));
        }
        if (!SiteModel::isValidDbName($dbName)) {
            $this->redirectWithError('/admin/sites/create', t('sites.error_db_name_invalid'));
        }
        if ($this->model->domainExists($domain)) {
            $this->redirectWithError('/admin/sites/create', t('sites.error_domain_taken'));
        }
        if ($this->model->dbNameExists($dbName)) {
            $this->redirectWithError('/admin/sites/create', t('sites.error_db_name_taken'));
        }

        $id = $this->model->create([
            'domain'    => $domain,
            'www_alias' => $wwwAlias,
            'name'      => $name,
            'db_name'   => $dbName,
            'status'    => 'provisioning',
        ]);

        $this->redirectWithSuccess(
            '/admin/sites/' . $id . '/edit',
            t('sites.created')
        );
    }

    public function edit(string $id): void
    {
        $site = $this->loadSite((int) $id);
        $this->view->extend('admin');
        $this->render('admin/sites/edit', ['site' => $site]);
    }

    public function update(string $id): void
    {
        Auth::verifyCsrf();
        $site = $this->loadSite((int) $id);

        $domain   = strtolower(trim($this->input('domain', '')));
        $wwwAlias = strtolower(trim($this->input('www_alias', '')));
        $name     = trim($this->input('name', ''));
        $dbName   = trim($this->input('db_name', ''));
        $status   = $this->input('status', 'active');

        if ($domain === '' || $name === '' || $dbName === '') {
            $this->redirectWithError('/admin/sites/' . $id . '/edit', t('error.required'));
        }
        if (!SiteModel::isValidDbName($dbName)) {
            $this->redirectWithError('/admin/sites/' . $id . '/edit', t('sites.error_db_name_invalid'));
        }
        if ($this->model->domainExists($domain, (int) $id)) {
            $this->redirectWithError('/admin/sites/' . $id . '/edit', t('sites.error_domain_taken'));
        }
        if ($this->model->dbNameExists($dbName, (int) $id)) {
            $this->redirectWithError('/admin/sites/' . $id . '/edit', t('sites.error_db_name_taken'));
        }

        $this->model->update((int) $id, [
            'domain'    => $domain,
            'www_alias' => $wwwAlias,
            'name'      => $name,
            'db_name'   => $dbName,
            'status'    => $status,
        ]);

        $this->redirectWithSuccess('/admin/sites/' . $id . '/edit', t('success.saved'));
    }

    public function provision(string $id): void
    {
        Auth::verifyCsrf();
        $site = $this->loadSite((int) $id);

        $provisioned = $this->model->provisionDb($site['db_name']);

        if ($provisioned) {
            $this->model->markActive((int) $id);
            $this->redirectWithSuccess(
                '/admin/sites/' . $id . '/edit',
                t('sites.provision_success')
            );
        } else {
            $this->redirectWithError(
                '/admin/sites/' . $id . '/edit',
                t('sites.provision_failed')
            );
        }
    }

    public function delete(string $id): void
    {
        Auth::verifyCsrf();
        $this->loadSite((int) $id); // ensure it exists
        $this->model->softDelete((int) $id);
        $this->redirectWithSuccess('/admin/sites', t('success.deleted'));
    }

    private function loadSite(int $id): array
    {
        $site = $this->model->getById($id);
        if ($site === null) {
            $this->redirectWithError('/admin/sites', t('error.not_found'));
        }
        return $site;
    }
}
