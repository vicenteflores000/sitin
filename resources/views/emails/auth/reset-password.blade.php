<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
</head>
<body style="margin:0; padding:0; background-color:#FAFAF7; font-family: Arial, sans-serif; color:#1f2937;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#FAFAF7; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:520px; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; padding:24px;">
                    <tr>
                        <td style="text-align:center; padding-bottom:12px;">
                            <img src="{{ $logoUrl ?? asset('images/logo.png') }}" alt="Logo Tickets TI" style="height:40px; width:auto; display:block; margin:0 auto;">
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; padding-bottom:12px;">
                            <div style="font-size:18px; font-weight:600; color:#111827;">Soporte TI</div>
                            <div style="font-size:12px; color:#6b7280;">Restablecimiento de contraseña</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px; font-weight:600; padding-bottom:8px;">Hola{{ isset($user->name) ? ' ' . $user->name : '' }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; color:#4b5563; padding-bottom:16px;">
                            Recibimos una solicitud para restablecer tu contraseña. Haz clic en el botón para continuar.
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-bottom:16px;">
                            <a href="{{ $url }}" style="display:inline-block; background:#6B8E23; color:#ffffff; text-decoration:none; padding:12px 20px; border-radius:8px; font-size:14px; font-weight:600;">
                                Restablecer contraseña
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:12px; color:#6b7280; padding-bottom:8px;">
                            Si no solicitaste este cambio, puedes ignorar este mensaje.
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:12px; color:#9ca3af;">
                            Si el botón no funciona, copia y pega este enlace en tu navegador:
                            <div style="word-break:break-all; color:#6b7280;">{{ $url }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
