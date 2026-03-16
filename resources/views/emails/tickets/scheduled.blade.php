<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket agendado</title>
</head>
<body style="margin:0; padding:0; background-color:#FAFAF7; font-family: Arial, sans-serif; color:#1f2937;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#FAFAF7; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; padding:24px;">
                    <tr>
                        <td style="text-align:center; padding-bottom:12px;">
                            <img src="{{ $logoUrl ?? asset('images/logo.png') }}" alt="Logo SITIN" style="height:40px; width:auto; display:block; margin:0 auto;">
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; padding-bottom:12px;">
                            <div style="font-size:18px; font-weight:600; color:#111827;">Notificación SITIN</div>
                            <div style="font-size:12px; color:#6b7280;">Ticket agendado</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px; font-weight:600; padding-bottom:8px;">
                            Ticket #{{ $ticket->id }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; color:#4b5563; padding-bottom:16px;">
                            @if(($mode ?? 'created') === 'updated')
                                El horario del ticket fue reprogramado.
                            @elseif(($mode ?? 'created') === 'deleted')
                                El bloque agendado para este ticket fue cancelado.
                            @else
                                El técnico asignado revisará tu ticket en el día y horario indicado.
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table style="border-collapse: collapse; width: 100%;">
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold; width: 140px;">Técnico</td>
                                    <td style="padding: 6px 0;">{{ $ticket->currentAssignment?->technician?->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold;">Fecha y hora</td>
                                    <td style="padding: 6px 0;">
                                        {{ $schedule->start_at->timezone(config('app.timezone'))->format('d-m-Y H:i') }}
                                        — {{ $schedule->end_at->timezone(config('app.timezone'))->format('d-m-Y H:i') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold;">Modalidad</td>
                                    <td style="padding: 6px 0;">
                                        {{ $schedule->modality === 'terreno' ? 'Visita en terreno' : 'Atención remota' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold;">Ubicación</td>
                                    <td style="padding: 6px 0;">
                                        {{ $ticket->locacion?->nombre ?? 'No especificado' }}@if($ticket->locacion_hija_texto) - {{ $ticket->locacion_hija_texto }}@endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold;">Categoría</td>
                                    <td style="padding: 6px 0;">{{ $ticket->categoria }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:16px; font-size:12px; color:#6b7280;">
                            Si necesitas reagendar, responde a este correo o comunícate con SITIN.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
