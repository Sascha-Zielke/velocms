<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Controllers\Admin;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Booking\Core\ValueObjects\ResourceType;
use VeloCMS\Modules\Booking\Models\ResourceModel;
use VeloCMS\Modules\Booking\Models\SlotModel;

class AdminResourceController extends Controller
{
    private ResourceModel $model;
    private SlotModel     $slotModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->model     = new ResourceModel();
        $this->slotModel = new SlotModel();
    }

    public function index(): void
    {
        $this->render('admin/resource/index', [
            'resources' => $this->model->all(),
        ]);
    }

    public function create(): void
    {
        $this->render('admin/resource/form', [
            'resource' => null,
            'slots'    => [],
            'types'    => ResourceType::cases(),
        ]);
    }

    public function store(): void
    {
        Auth::verifyCsrf();

        $name        = trim($this->input('name', ''));
        $type        = $this->input('type', 'human');
        $templateKey = trim($this->input('template_key', 'generic'));

        if ($name === '') {
            $this->redirectWithError('/admin/apps/booking/resources/create', t('error.required'));
        }

        if (!in_array($type, array_column(ResourceType::cases(), 'value'), true)) {
            $type = 'human';
        }

        $id = $this->model->create($name, $type, $templateKey);
        $this->saveSlots($id);

        $this->redirectWithSuccess('/admin/apps/booking/resources', t('success.saved'));
    }

    public function edit(int $id): void
    {
        $resource = $this->model->find($id);
        if ($resource === null) {
            $this->redirectWithError('/admin/apps/booking/resources', t('error.not_found'));
        }

        $this->render('admin/resource/form', [
            'resource' => $resource,
            'slots'    => $this->slotModel->forResource($id, false),
            'types'    => ResourceType::cases(),
        ]);
    }

    public function update(int $id): void
    {
        Auth::verifyCsrf();

        $resource = $this->model->find($id);
        if ($resource === null) {
            $this->redirectWithError('/admin/apps/booking/resources', t('error.not_found'));
        }

        $name        = trim($this->input('name', ''));
        $type        = $this->input('type', $resource->type->value);
        $templateKey = trim($this->input('template_key', $resource->templateKey));
        $isActive    = (bool) $this->input('is_active', 0);

        if ($name === '') {
            $this->redirectWithError('/admin/apps/booking/resources/edit/' . $id, t('error.required'));
        }

        if (!in_array($type, array_column(ResourceType::cases(), 'value'), true)) {
            $type = $resource->type->value;
        }

        $this->model->update($id, $name, $type, $templateKey, $resource->metadata, $isActive);
        $this->slotModel->deleteForResource($id);
        $this->saveSlots($id);

        $this->redirectWithSuccess('/admin/apps/booking/resources', t('success.saved'));
    }

    public function delete(int $id): void
    {
        Auth::verifyCsrf();
        $this->slotModel->deleteForResource($id);
        $this->model->delete($id);
        $this->redirectWithSuccess('/admin/apps/booking/resources', t('success.deleted'));
    }

    private function saveSlots(int $resourceId): void
    {
        $weekdays  = $_POST['slot_weekday']   ?? [];
        $starts    = $_POST['slot_start']     ?? [];
        $ends      = $_POST['slot_end']       ?? [];

        foreach ($weekdays as $i => $weekday) {
            $start = $starts[$i] ?? '';
            $end   = $ends[$i]   ?? '';
            if ($start === '' || $end === '' || $start >= $end) {
                continue;
            }
            $weekday = (int) $weekday;
            if ($weekday < 0 || $weekday > 6) {
                continue;
            }
            // Ensure HH:MM:SS format
            $start = substr($start . ':00', 0, 8);
            $end   = substr($end   . ':00', 0, 8);
            $this->slotModel->create($resourceId, $weekday, $start, $end);
        }
    }
}
