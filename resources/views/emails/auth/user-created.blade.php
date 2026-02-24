<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tu acceso a Tickets TI</title>
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
                            <div style="font-size:12px; color:#6b7280;">Acceso de usuario</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px; font-weight:600; padding-bottom:8px;">
                            Hola{{ isset($user->name) ? ' ' . $user->name : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; color:#4b5563; padding-bottom:16px;">
                            Se creó tu acceso al sistema de Tickets TI. Tus credenciales iniciales son:
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; padding-bottom:4px;"><strong>Correo:</strong> {{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; padding-bottom:16px;"><strong>Clave provisoria:</strong> {{ $plainPassword }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px; color:#6b7280;">
                            Al ingresar por primera vez se te pedirá cambiarla.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
