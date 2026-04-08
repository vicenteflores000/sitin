<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo ticket</title>
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
                            <div style="font-size:12px; color:#6b7280;">Nuevo ticket registrado</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px; font-weight:600; padding-bottom:8px;">Ticket #{{ $ticket->display_id }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; color:#4b5563; padding-bottom:16px;">
                            Se ha registrado un nuevo ticket en el sistema.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table style="border-collapse: collapse; width: 100%;">
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold; width: 120px;">Usuario</td>
                                    <td style="padding: 6px 0;">{{ $ticket->usuario_mail }}</td>
                                </tr>
                                @if($ticket->assisted_by)
                                    <tr>
                                        <td style="padding: 6px 0; font-weight: bold;">Ticket asistido</td>
                                        <td style="padding: 6px 0;">
                                            Creado por {{ $ticket->assistedBy?->name ?? $ticket->assistedBy?->email ?? 'Equipo TI' }}
                                            @if($ticket->assisted_channel)
                                                · {{ ucfirst($ticket->assisted_channel) }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if($ticket->assisted_reason)
                                        <tr>
                                            <td style="padding: 6px 0; font-weight: bold;">Motivo</td>
                                            <td style="padding: 6px 0;">{{ $ticket->assisted_reason }}</td>
                                        </tr>
                                    @endif
                                @endif
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold;">Ubicación</td>
                                    <td style="padding: 6px 0;">
                                        {{ \App\Support\TicketView::locationLabel($ticket, 'No especificado') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold;">Categoría</td>
                                    <td style="padding: 6px 0;">{{ $ticket->categoria }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold;">Tipo</td>
                                    <td style="padding: 6px 0;">{{ $ticket->tipo }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-weight: bold;">Impacto laboral</td>
                                    <td style="padding: 6px 0;">{{ $ticket->impacto }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:16px; font-size:14px; font-weight:600;">Descripción</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; color:#4b5563; white-space:pre-line;">{{ $ticket->descripcion }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
