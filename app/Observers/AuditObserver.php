<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    protected array $hiddenAttributes = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'current_password',
    ];

    protected array $ignoredAttributes = [
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    public function created(Model $model): void
    {
        $this->store($model, 'created');
    }

    public function updated(Model $model): void
    {
        $this->store($model, 'updated');
    }

    public function deleted(Model $model): void
    {
        $this->store($model, 'deleted');
    }

    protected function store(Model $model, string $action): void
    {
        if ($model instanceof AuditLog) {
            return;
        }

        $changes = $this->extractChanges($model, $action);
        if ($action === 'updated' && empty($changes['after'] ?? $changes)) {
            return;
        }

        $user = auth()->user();
        $request = app()->bound('request') ? request() : null;

        AuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'tag' => $this->resolveTag($model, $action),
            'action' => $action,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'description' => $this->buildDescription($model, $action),
            'changes' => $changes,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }

    protected function extractChanges(Model $model, string $action): array
    {
        if ($action === 'created') {
            return [
                'after' => $this->sanitizeAttributes($model->getAttributes()),
            ];
        }

        if ($action === 'deleted') {
            return [
                'before' => $this->sanitizeAttributes($model->getOriginal()),
            ];
        }

        $changes = $model->getChanges();
        $before = [];
        $after = [];

        foreach ($changes as $key => $value) {
            if (in_array($key, $this->ignoredAttributes, true) || in_array($key, $this->hiddenAttributes, true)) {
                continue;
            }

            $before[$key] = $model->getOriginal($key);
            $after[$key] = $value;
        }

        return [
            'before' => $this->sanitizeAttributes($before),
            'after' => $this->sanitizeAttributes($after),
        ];
    }

    protected function sanitizeAttributes(array $attributes): array
    {
        foreach ($this->hiddenAttributes as $key) {
            unset($attributes[$key]);
        }

        foreach ($this->ignoredAttributes as $key) {
            unset($attributes[$key]);
        }

        return $attributes;
    }

    protected function buildDescription(Model $model, string $action): string
    {
        $label = $this->modelLabel($model);
        $verb = match ($action) {
            'created' => 'creado',
            'updated' => 'actualizado',
            'deleted' => 'eliminado',
            default => $action,
        };

        return "{$label} {$verb}";
    }

    protected function modelLabel(Model $model): string
    {
        $class = class_basename($model);

        return match ($class) {
            'Ticket' => "Ticket #{$model->id}",
            'TicketAssignment' => "Asignación ticket #{$model->ticket_id}",
            'TicketAction' => "Acción ticket #{$model->ticket_id}",
            'TicketPart' => "Repuesto ticket #{$model->ticket_id}",
            'TicketResolution' => "Resolución ticket #{$model->ticket_id}",
            'TicketStatusEvent' => "Estado ticket #{$model->ticket_id}",
            'TicketSchedule' => "Agenda ticket #{$model->ticket_id}",
            'User' => "Usuario {$model->email}",
            'Locacion' => "Locación {$model->nombre}",
            'AllowedDomain' => "Dominio {$model->domain}",
            default => "{$class} #{$model->getKey()}",
        };
    }

    protected function resolveTag(Model $model, string $action): string
    {
        $class = class_basename($model);

        return match ($class) {
            'Ticket' => $action === 'created' ? 'OUTPUT' : 'INPUT',
            'TicketAssignment',
            'TicketStatusEvent',
            'TicketResolution',
            'TicketSchedule',
            'User',
            'AllowedDomain',
            'Locacion' => 'ALERTA',
            'TicketAction',
            'TicketPart' => 'INPUT',
            default => 'INPUT',
        };
    }
}
