<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="toast-container" id="toast-root"></div>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
        <script>
            window.__toastQueue = window.__toastQueue || [];
            @if (session('success'))
                window.__toastQueue.push({ type: 'success', message: @json(session('success')) });
            @endif
            @if ($errors->any())
                window.__toastQueue.push({ type: 'error', message: @json($errors->first()) });
            @endif
        </script>
        <style>
            .toast-container {
                position: fixed;
                top: 1rem;
                right: 1rem;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                max-width: 360px;
            }
            .toast {
                display: flex;
                align-items: flex-start;
                gap: 0.6rem;
                padding: 0.75rem 0.9rem;
                border-radius: 0.75rem;
                border: 1px solid;
                box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
                font-size: 0.85rem;
                background: #fff;
                opacity: 0;
                transform: translateY(6px);
                transition: all 180ms ease;
            }
            .toast.show { opacity: 1; transform: translateY(0); }
            .toast-success { border-color: #bbf7d0; background: #f0fdf4; color: #166534; }
            .toast-error { border-color: #fecaca; background: #fef2f2; color: #991b1b; }
            .toast-info { border-color: #bfdbfe; background: #eff6ff; color: #1d4ed8; }
        </style>
        <script>
            window.showToast = window.showToast || function(type, message) {
                const root = document.getElementById('toast-root');
                if (!root || !message) return;
                const toast = document.createElement('div');
                toast.className = `toast toast-${type || 'info'}`;
                toast.innerHTML = `<div style="flex:1;">${message}</div><button type="button" aria-label="Cerrar" style="margin-left:auto;color:inherit;">✕</button>`;
                toast.querySelector('button').addEventListener('click', () => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 180);
                });
                root.appendChild(toast);
                requestAnimationFrame(() => toast.classList.add('show'));
                setTimeout(() => {
                    if (!toast.isConnected) return;
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 180);
                }, 4000);
            };

            document.addEventListener('DOMContentLoaded', () => {
                if (Array.isArray(window.__toastQueue)) {
                    window.__toastQueue.forEach((t) => window.showToast(t.type, t.message));
                    window.__toastQueue = [];
                }
            });
        </script>
    </body>
</html>
