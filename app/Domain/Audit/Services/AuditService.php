<?php

namespace App\Domain\Audit\Services;

use App\Domain\Audit\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;

class AuditService
{
    /**
     * Record an audit log entry.
     *
     * @param  string  $action  e.g. 'transaction.created', 'account.updated'
     * @param  Model   $model   The model being audited
     * @param  array<string, mixed>   $oldValues  Previous state (empty for creates)
     * @param  array<string, mixed>   $newValues  New state (empty for deletes)
     */
    public function log(
        string $action,
        Model $model,
        array $oldValues = [],
        array $newValues = [],
    ): AuditLog {
        return AuditLog::create([
            'user_id'        => Auth::id(),
            'action'         => $action,
            'auditable_type' => $model::class,
            'auditable_id'   => $model->getKey(),
            'old_values'     => empty($oldValues) ? null : $oldValues,
            'new_values'     => empty($newValues) ? null : $newValues,
            'ip_address'     => RequestFacade::ip(),
            'user_agent'     => RequestFacade::userAgent(),
        ]);
    }
}
