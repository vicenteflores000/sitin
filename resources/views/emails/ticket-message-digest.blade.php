<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mensajes nuevos</title>
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
                            <div style="font-size:12px; color:#6b7280;">Mensajes nuevos</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px; font-weight:600; padding-bottom:8px;">
                            Ticket #{{ $ticket->display_id }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; color:#4b5563; padding-bottom:16px;">
                            Tienes {{ $messages->count() }} mensajes nuevos en este ticket.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table style="border-collapse: collapse; width: 100%;">
                                @foreach ($messages as $message)
                                    <tr>
                                        <td style="padding: 10px 0; border-top: 1px solid #e5e7eb;">
                                            <div style="font-size:13px; font-weight:600; color:#111827;">
                                                {{ $message->user?->name ?? 'Usuario' }}
                                                <span style="font-size:11px; color:#6b7280; font-weight:400;">
                                                    · {{ $message->created_at?->format('d-m-Y H:i') ?? '' }}
                                                </span>
                                            </div>
                                            <div style="font-size:14px; color:#4b5563; white-space:pre-line; padding-top:4px;">
                                                {{ $message->body }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:18px; text-align:center;">
                            <a href="{{ $link }}"
                               style="display:inline-block; padding:10px 18px; background:#F4F7EE; color:#6B8E23; border:1px solid #6B8E23; border-radius:999px; text-decoration:none; font-size:13px; font-weight:600;">
                                Ver conversación
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:16px; font-size:12px; color:#6b7280;">
                            Responde a este mensaje desde SITIN para mantener el historial del ticket.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
