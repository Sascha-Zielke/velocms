<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Auth\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function index(): void
    {
        $this->view->extend('admin');
        $this->render('admin/dashboard', [
            'userName' => Auth::name() ?? '',
        ]);
    }
}