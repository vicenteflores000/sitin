<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Actions\SyncTicketStatusFromGlpi;
use Illuminate\Http\RedirectResponse;
use App\Actions\ReenviarTicketsPendientesAGlpi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        return $this->renderDashboard('dashboard');
    }

    public function admin()
    {
        $calendarTickets = Ticket::with('locacion.padre', 'currentAssignment.technician', 'currentAssignments.technician', 'requester')
            ->whereHas('currentAssignments', function ($query) {
                $query->where('technician_id', auth()->id());
            })
            ->orderByDesc('created_at')
            ->get();

        $adminTickets = Ticket::with([
            'locacion.padre',
            'requester',
            'latestStatusEvent',
            'currentAssignment.technician',
            'currentAssignments.technician',
            'resolution',
            'parts',
            'actions.creator',
            'attachments',
        ])->orderByDesc('created_at')->get();

        $admins = User::where('role', 'admin')->orderBy('name')->get();

        $domainCards = [
            'salud' => [
                'label' => 'Salud',
                'style' => 'background-color: #2F7FA3;',
                'text' => 'text-white',
            ],
            'educacion' => [
                'label' => 'Educación',
                'style' => 'background-color: #B53A3A;',
                'text' => 'text-white',
            ],
            'municipal' => [
                'label' => 'Municipal',
                'style' => 'background-color: #2E7A57;',
                'text' => 'text-white',
            ],
        ];

        $domainMap = [
            'salud.mdonihue.cl' => 'salud',
            'edudonihue.cl' => 'educacion',
            'mdonihue.cl' => 'municipal',
        ];

        $domainStats = [];
        foreach ($domainCards as $key => $card) {
            $domainStats[$key] = [
                'total' => 0,
                'nuevo' => 0,
                'asignado' => 0,
                'resuelto' => 0,
            ];
        }

        $totalStats = [
            'total' => 0,
            'nuevo' => 0,
            'asignado' => 0,
            'resuelto' => 0,
        ];

        $techCards = [];
        foreach ($admins as $adminUser) {
            $techCards[$adminUser->id] = [
                'id' => $adminUser->id,
                'name' => $adminUser->name,
                'assigned' => 0,
                'resolved' => 0,
            ];
        }

        $newStatuses = ['nuevo', 'recibido'];
        $assignedStatuses = ['asignado', 'en_progreso', 'en_proceso', 'standby', 'en_espera'];
        $resolvedStatuses = ['resuelto', 'cerrado'];

        $normalizeDomain = function (?string $value): string {
            if (!$value) {
                return '';
            }
            return Str::of($value)
                ->lower()
                ->replace('@', '')
                ->replace(' ', '')
                ->ascii()
                ->__toString();
        };

        $resolveDomainKeyFromEmail = function (?string $email) use ($domainMap, $normalizeDomain): ?string {
            if (! $email || ! str_contains($email, '@')) {
                return null;
            }
            $domain = $normalizeDomain(substr(strrchr($email, '@'), 1) ?: '');
            return $domainMap[$domain] ?? null;
        };

        foreach ($adminTickets as $ticket) {
            $rawStatus = $ticket->latestStatusEvent?->to_status ?? $ticket->estado_glpi ?? 'nuevo';
            $status = Str::of($rawStatus)->lower()->__toString();

            $totalStats['total']++;
            if (in_array($status, $newStatuses, true)) {
                $totalStats['nuevo']++;
            }
            if (in_array($status, $assignedStatuses, true)) {
                $totalStats['asignado']++;
            }
            if (in_array($status, $resolvedStatuses, true)) {
                $totalStats['resuelto']++;
            }

            $ticketDomainKey = $resolveDomainKeyFromEmail($ticket->usuario_mail);
            $ticket->domain_keys = $ticketDomainKey ? [$ticketDomainKey] : [];
            if ($ticketDomainKey) {
                $key = $ticketDomainKey;
                if (!array_key_exists($key, $domainStats)) {
                    continue;
                }
                $domainStats[$key]['total']++;
                if (in_array($status, $newStatuses, true)) {
                    $domainStats[$key]['nuevo']++;
                }
                if (in_array($status, $assignedStatuses, true)) {
                    $domainStats[$key]['asignado']++;
                }
                if (in_array($status, $resolvedStatuses, true)) {
                    $domainStats[$key]['resuelto']++;
                }
            }

            $assignedIds = $ticket->currentAssignments?->pluck('technician_id')->filter()->values() ?? collect();
            foreach ($assignedIds as $technicianId) {
                if (! isset($techCards[$technicianId])) {
                    continue;
                }
                if (in_array($status, $assignedStatuses, true)) {
                    $techCards[$technicianId]['assigned']++;
                }
                if (in_array($status, $resolvedStatuses, true)) {
                    $techCards[$technicianId]['resolved']++;
                }
            }
        }

        $techCards = array_values($techCards);
        usort($techCards, function ($a, $b) {
            $byAssigned = ($b['assigned'] ?? 0) <=> ($a['assigned'] ?? 0);
            if ($byAssigned !== 0) {
                return $byAssigned;
            }
            return ($b['resolved'] ?? 0) <=> ($a['resolved'] ?? 0);
        });

        return view('dashboard-admin', compact(
            'calendarTickets',
            'adminTickets',
            'admins',
            'domainCards',
            'domainStats',
            'totalStats',
            'techCards'
        ));
    }

    public function adminTech(Request $request, User $technician)
    {
        if ($technician->role !== 'admin') {
            abort(404);
        }

        $days = (int) $request->query('days', 30);
        $allowedDays = [7, 14, 30, 60, 90];
        if (! in_array($days, $allowedDays, true)) {
            $days = 30;
        }

        $assignedTickets = Ticket::with([
            'locacion.padre',
            'latestStatusEvent',
            'currentAssignments.technician',
            'requester',
        ])
            ->whereHas('currentAssignments', function ($query) use ($technician) {
                $query->where('technician_id', $technician->id);
            })
            ->orderByDesc('created_at')
            ->get();

        $activeAssigned = $assignedTickets->filter(function (Ticket $ticket) {
            $status = $ticket->latestStatusEvent?->to_status ?? $ticket->estado_glpi ?? 'nuevo';
            return ! in_array($status, ['resuelto', 'cerrado'], true);
        })->values();

        $resolvedTickets = Ticket::with([
            'locacion.padre',
            'latestStatusEvent',
            'requester',
        ])
            ->where('resolved_by', $technician->id)
            ->whereNotNull('resolved_at')
            ->orderByDesc('resolved_at')
            ->get();

        $periodStart = now()->subDays($days - 1)->startOfDay();
        $resolvedPeriod = $resolvedTickets->filter(function (Ticket $ticket) use ($periodStart) {
            return $ticket->resolved_at && $ticket->resolved_at->gte($periodStart);
        })->values();

        $resolutionMinutes = $resolvedTickets->map(function (Ticket $ticket) {
            if (! $ticket->resolved_at) {
                return null;
            }
            return $ticket->created_at?->diffInMinutes($ticket->resolved_at);
        })->filter()->values();

        $resolutionMinutesPeriod = $resolvedPeriod->map(function (Ticket $ticket) {
            if (! $ticket->resolved_at) {
                return null;
            }
            return $ticket->created_at?->diffInMinutes($ticket->resolved_at);
        })->filter()->values();

        $avgResolutionMinutes = $resolutionMinutesPeriod->isNotEmpty()
            ? $resolutionMinutesPeriod->avg()
            : null;

        $medianResolutionMinutes = null;
        if ($resolutionMinutesPeriod->isNotEmpty()) {
            $sorted = $resolutionMinutesPeriod->sort()->values();
            $count = $sorted->count();
            $mid = (int) floor($count / 2);
            $medianResolutionMinutes = $count % 2 === 0
                ? ($sorted[$mid - 1] + $sorted[$mid]) / 2
                : $sorted[$mid];
        }

        $resolvedInPeriod = $resolvedPeriod->count();
        $speedPerDay = $days > 0 ? $resolvedInPeriod / $days : 0;

        $chartData = [];
        $maxResolved = 0;
        $maxAvgMinutes = 0;
        $byDay = $resolvedPeriod->groupBy(function (Ticket $ticket) {
            return $ticket->resolved_at?->format('Y-m-d') ?? '';
        });

        for ($i = 0; $i < $days; $i++) {
            $date = $periodStart->copy()->addDays($i);
            $key = $date->format('Y-m-d');
            $dayTickets = $byDay->get($key, collect());
            $resolvedCount = $dayTickets->count();
            $avgMinutesDay = null;
            if ($resolvedCount > 0) {
                $avgMinutesDay = $dayTickets->map(function (Ticket $ticket) {
                    return $ticket->created_at?->diffInMinutes($ticket->resolved_at);
                })->filter()->avg();
            }

            $maxResolved = max($maxResolved, $resolvedCount);
            $maxAvgMinutes = max($maxAvgMinutes, (int) round($avgMinutesDay ?? 0));

            $chartData[] = [
                'label' => $date->format('d/m'),
                'resolved' => $resolvedCount,
                'avg_minutes' => $avgMinutesDay ? (int) round($avgMinutesDay) : 0,
            ];
        }

        $stats = [
            'resolved_total' => $resolvedTickets->count(),
            'resolved_period' => $resolvedInPeriod,
            'avg_resolution' => $this->formatMinutes($avgResolutionMinutes),
            'median_resolution' => $this->formatMinutes($medianResolutionMinutes),
            'speed_per_day' => number_format($speedPerDay, 1),
            'assigned_active' => $activeAssigned->count(),
            'assigned_total' => $assignedTickets->count(),
        ];

        return view('dashboard-admin-tech', [
            'technician' => $technician,
            'stats' => $stats,
            'days' => $days,
            'chartData' => $chartData,
            'chartMaxResolved' => $maxResolved,
            'chartMaxAvgMinutes' => $maxAvgMinutes,
            'assignedTickets' => $activeAssigned,
            'resolvedTickets' => $resolvedTickets,
        ]);
    }

    protected function formatMinutes(?float $minutes): string
    {
        if (! $minutes || $minutes <= 0) {
            return '—';
        }

        $total = (int) round($minutes);
        $days = intdiv($total, 1440);
        $hours = intdiv($total % 1440, 60);
        $mins = $total % 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = $days . 'd';
        }
        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }
        if ($mins > 0 && $days === 0) {
            $parts[] = $mins . 'm';
        }

        return implode(' ', $parts) ?: '—';
    }

    protected function renderDashboard(string $view)
    {
        $userMail = auth()->user()->email;

        $tickets = Ticket::where('usuario_mail', $userMail)
            ->with([
                'assignments.technician',
                'schedules',
                'actions.creator',
                'resolution',
                'latestStatusEvent',
                'attachments',
                'requester',
            ])
            ->orderByDesc('created_at')
            ->get();

        $total = $tickets->count();

        $abiertos = $tickets->whereIn('estado_glpi', [
            'recibido',
            'en_proceso',
            'en_espera',
        ])->count();

        $cerrados = $tickets->where('estado_glpi', 'cerrado')->count();

        $ultimos = $tickets;

            $glpiStatus = 'online';

        return view($view, compact(
            'total',
            'abiertos',
            'cerrados',
            'ultimos',
            'glpiStatus'
        ));
    }

    public function reenviarPendientes(
        ReenviarTicketsPendientesAGlpi $action
    ) {
        $result = $action->execute();

        return redirect()
            ->route('dashboard')
            ->with('reenviar_result', $result);
    }

    public function syncEstados(
        SyncTicketStatusFromGlpi $sync
    ): RedirectResponse {
        $result = $sync->execute();

        return redirect()
            ->route('dashboard')
            ->with('sync_result', $result);
    }
}
