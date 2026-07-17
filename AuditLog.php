<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'old_values', 'new_values', 'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Static helper to log an action ─────────────────────────
    public static function log(string $action, $model, array $oldValues = [], array $newValues = []): void
    {
        static::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model_type' => class_basename($model),
            'model_id'   => $model->id ?? null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
        ]);
    }
}
