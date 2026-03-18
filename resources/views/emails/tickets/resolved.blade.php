<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket resuelto</title>
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
                            <div style="font-size:12px; color:#6b7280;">Ticket resuelto</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px; font-weight:600; padding-bottom:8px;">Ticket #{{ $ticket->display_id }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; color:#4b5563; padding-bottom:16px;">
                            El equipo técnico asignado resolvió tu ticket.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table style="border-collapse: collapse; width: 100%;">
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold; width: 140px;">Equipo técnico</td>
                                    <td style="padding: 6px 0;">{{ $assignedNames ?: 'SITIN' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold; width: 140px;">Ubicación</td>
                                    <td style="padding: 6px 0;">
                                        {{ $ticket->locacion?->nombre ?? 'No especificado' }}@if($ticket->locacion_hija_texto) - {{ $ticket->locacion_hija_texto }}@endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold;">Tipo</td>
                                    <td style="padding: 6px 0;">{{ $ticket->tipo }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:16px; font-size:14px; font-weight:600;">Resolución</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; color:#4b5563; white-space:pre-line;">{{ $resolution->resolution_text }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
