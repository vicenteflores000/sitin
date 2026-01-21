<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body, html {
            overflow: hidden;
            height: 100%;
            margin: 0;
        }
    </style>
</head>
<body class="bg-[#FAFAF7] text-gray-800">
    {{ $slot }}
</body>
</html>