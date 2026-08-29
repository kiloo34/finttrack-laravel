<?php

namespace App\Modules\Audit\Application\Services;

use App\Modules\Audit\Infrastructure\Persistence\Eloquent\Models\AuditLogModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;

/**
 * Application Service: Records audit trail entries.
 * Lives in Application layer since it coordinates infrastructure persistence.
 */
final class AuditService
{
    /**
     * Record an audit log entry.
     *
     * @param  string  $action  e.g. 'account_created', 'transaction_updated'
     * @param  string  $entityType  e.g. 'account', 'transaction'
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    public function log(
        string $action,
        string $entityType,
        string|int $entityId,
        array $oldValues = [],
        array $newValues = [],
    ): void {
        AuditLogModel::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $entityType,
            'auditable_id' => $entityId,
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'ip_address' => RequestFacade::ip(),
            'user_agent' => RequestFacade::userAgent(),
        ]);
    }
}
