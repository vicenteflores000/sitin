<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Solicitud Soporte TI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
        }

        h1 {
            font-size: 20px;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        select,
        textarea,
        button {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        textarea {
            resize: none;
        }

        button {
            margin-top: 20px;
            background: #1e88e5;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #1565c0;
        }

        .nota {
            font-size: 12px;
            color: #555;
            margin-top: 10px;
        }

        .error {
            background: #ffe5e5;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ffb3b3;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Solicitud de Soporte TI</h1>

        @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="/ticket">
            @csrf

            <label>Tipo de solicitud</label>
            <select name="tipo" required>
                <option value="">Seleccione...</option>
                <option value="Soporte">Soporte</option>
                <option value="Administrativo">Administrativo</option>
                <option value="Mejora">Mejora</option>
            </select>

            <label>Área</label>
            <select name="area" required>
                <option value="">Seleccione...</option>
                <option value="CESFAM">CESFAM</option>
                <option value="Urgencia">Urgencia</option>
                <option value="Registro Civil">Registro Civil</option>
                <option value="Administracion">Administración</option>
            </select>

            <label>Ubicación</label>
            <select name="glpi_location_id" required>
                <option value="">Seleccione...</option>
                @foreach ($locations as $location)
                <option value="{{ $location->glpi_id }}">
                    {{ $location->name }}
                </option>
                @endforeach
            </select>

            <label>Categoría</label>
            <select name="categoria" required>
                <option value="">Seleccione...</option>
                <option value="Computador">Computador</option>
                <option value="Impresora">Impresora</option>
                <option value="Internet">Internet</option>
                <option value="Sistema">Sistema</option>
                <option value="Correo">Correo</option>
                <option value="Telefonia">Telefonía</option>
                <option value="Otro">Otro</option>
            </select>

            <label>Impacto en el trabajo</label>
            <select name="impacto" required>
                <option value="">Seleccione...</option>
                <option value="No impide trabajar">No impide trabajar</option>
                <option value="Dificulta el trabajo">Dificulta el trabajo</option>
                <option value="Impide atender usuarios">Impide atender usuarios</option>
            </select>

            <label>Descripción breve del problema</label>
            <textarea name="descripcion" maxlength="300" rows="4" required
                placeholder="Describa el problema de forma breve y clara"></textarea>

            <div class="nota">
                • No indique prioridad ni urgencia.<br>
                • Su solicitud quedará registrada para trazabilidad.<br>
                • Tiempo estimado de respuesta depende de criticidad técnica.
            </div>

            <button type="submit">Enviar solicitud</button>
        </form>
    </div>

</body>

</html>