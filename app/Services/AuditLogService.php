<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Catat aktivitas audit ke database.
     *
     * @param string $action
     * @param Model|null $model
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param int|null $userId
     * @return AuditLog
     */
    public function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): AuditLog {
        $resolvedUserId = $userId ?? Auth::id();

        $ipAddress = request()?->ip();
        $userAgent = request()?->userAgent();

        return AuditLog::create([
            'user_id'        => $resolvedUserId,
            'action'         => strtoupper($action),
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id'   => $model?->getKey(),
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => $ipAddress,
            'user_agent'     => $userAgent,
        ]);
    }
}
