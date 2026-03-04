<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
                ];
            }
        }

        $entries = array_slice(array_reverse($entries), 0, 200);

        return view('admin.logs.index', [
            'entries' => $entries,
            'date' => $date,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
