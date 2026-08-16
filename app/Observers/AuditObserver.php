<?php

namespace App\Observers;

use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function created(Model $model): void
    {
        AuditLogger::model('created', $model);
    }

    public function updated(Model $model): void
    {
        $old = $model->getOriginal();
        $new = $model->getChanges();

        AuditLogger::model('updated', $model, $old, $new);
    }

    public function deleted(Model $model): void
    {
        AuditLogger::model('deleted', $model, $model->getAttributes(), null);
    }

    public function restored(Model $model): void
    {
        AuditLogger::model('restored', $model);
    }

    public function forceDeleted(Model $model): void
    {
        AuditLogger::model('force-deleted', $model, $model->getAttributes(), null);
    }
}
