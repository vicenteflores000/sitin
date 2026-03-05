<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AuditLog;

class AdminErrorLogController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        $from = $request->query('from');
        $to = $request->query('to');

        $entries = [];
        $logPath = storage_path('logs/laravel.log');

        if (is_file($logPath) && is_readable($logPath)) {
            $file = new \SplFileObject($logPath, 'r');
            $file->setFlags(\SplFileObject::DROP_NEW_LINE);

            foreach ($file as $line) {
                if (! is_string($line) || $line === '') {
                    continue;
                }

                if (! Str::startsWith($line, '[')) {
                    continue;
                }

                if (! preg_match('/^\[(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2})\] ([^:]+): (.*)$/', $line, $matches)) {
                    continue;
                }

                [$full, $lineDate, $lineTime, $level, $message] = $matches;

                if ($date && $lineDate !== $date) {
                    continue;
                }

                if ($from && substr($lineTime, 0, 5) < $from) {
                    continue;
                }

                if ($to && substr($lineTime, 0, 5) > $to) {
                    continue;
                }

                $entries[] = [
                    'date' => $lineDate,
                    'time' => $lineTime,
                    'level' => $level,
                    'message' => $message,
                    'type' => 'error',
                    'tag' => 'ERROR',
                ];
            }
        }

        $auditQuery = AuditLog::query();

        if ($date) {
            $auditQuery->whereDate('created_at', $date);
        }

        if ($from) {
            $auditQuery->whereTime('created_at', '>=', $from);
        }

        if ($to) {
            $auditQuery->whereTime('created_at', '<=', $to);
        }

        $auditEntries = $auditQuery
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        foreach ($auditEntries as $audit) {
            $entries[] = [
                'date' => $audit->created_at?->format('Y-m-d') ?? '',
                'time' => $audit->created_at?->format('H:i:s') ?? '',
                'level' => 'CAMBIO',
                'message' => $audit->description,
                'type' => 'audit',
                'user' => $audit->user_name ?? $audit->user_email,
                'tag' => $audit->tag ?? 'INPUT',
            ];
        }

        usort($entries, function ($a, $b) {
            $aKey = ($a['date'] ?? '') . ' ' . ($a['time'] ?? '');
            $bKey = ($b['date'] ?? '') . ' ' . ($b['time'] ?? '');
            return strcmp($bKey, $aKey);
        });

        $entries = array_slice($entries, 0, 200);

        return view('admin.logs.index', [
            'entries' => $entries,
            'date' => $date,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
