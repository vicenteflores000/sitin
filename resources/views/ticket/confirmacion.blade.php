<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud registrada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }
        .box {
            max-width: 500px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 6px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
        h1 {
            color: #2e7d32;
        }
        .id {
            margin-top: 15px;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>
<body>

<div class="box">
    <h1>Solicitud registrada correctamente</h1>

    <p>Su solicitud fue ingresada al sistema de soporte TI.</p>

    <p class="id">
        Nº de registro interno: {{ $ticket_id }}
    </p>

    <p style="margin-top: 20px; font-size: 13px; color: #555;">
        Este número permite trazabilidad interna.<br>
        El equipo TI evaluará la solicitud según criticidad técnica.
    </p>
</div>

</body>
</html>
