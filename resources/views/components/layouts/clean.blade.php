<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function () {
            const stored = localStorage.getItem('sitin-theme') || localStorage.getItem('admin-dashboard-theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = stored ? stored === 'dark' : prefersDark;
            document.documentElement.classList.toggle('dark-mode', isDark);
        })();
    </script>
    @stack('head')
    <style>
        body, html {
            overflow: hidden;
            height: 100%;
            margin: 0;
        }
        html.dark-mode body {
            background: radial-gradient(circle at top, #111a2b 0%, #0b1220 55%, #0b1220 100%) !important;
            color: #e5e7eb;
        }
        html.dark-mode .bg-white { background-color: #0f172a !important; }
        html.dark-mode .bg-gray-50 { background-color: #0b1220 !important; }
        html.dark-mode .bg-gray-100 { background-color: #0b1220 !important; }
        html.dark-mode [class*="bg-[#FAFAF7]"] { background-color: #0b1220 !important; }
        html.dark-mode .border-gray-200,
        html.dark-mode .border-gray-300 { border-color: #1f2a44 !important; }
        html.dark-mode .text-gray-900,
        html.dark-mode .text-gray-800 { color: #f8fafc !important; }
        html.dark-mode .text-gray-700 { color: #e5e7eb !important; }
        html.dark-mode .text-gray-600 { color: #cbd5f5 !important; }
        html.dark-mode .text-gray-500 { color: #9aa6bf !important; }
        html.dark-mode .text-gray-400 { color: #7b879d !important; }
        html.dark-mode .hover\:bg-gray-50:hover { background-color: #1f2a44 !important; }
        html.dark-mode .shadow-xl,
        html.dark-mode .shadow-lg,
        html.dark-mode .shadow-sm { box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45); }
        html.dark-mode input,
        html.dark-mode select,
        html.dark-mode textarea {
            background-color: #0b1220 !important;
            color: #e5e7eb !important;
            border-color: #1f2a44 !important;
        }
        .theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            height: 40px;
            width: 40px;
            border-radius: 9999px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #374151;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            z-index: 9998;
            transition: background 150ms ease, border-color 150ms ease, color 150ms ease;
        }
        .theme-toggle:hover {
            background: #f3f4f6;
        }
        .theme-icon-moon { display: none; }
        html.dark-mode .theme-toggle {
            background: #0b1220;
            border-color: #1f2a44;
            color: #e5e7eb;
        }
        html.dark-mode .theme-toggle:hover {
            background: #1f2a44;
        }
        html.dark-mode .theme-icon-sun { display: none; }
        html.dark-mode .theme-icon-moon { display: block; }
        .theme-logo-dark { display: none; }
        html.dark-mode .theme-logo-light { display: none; }
        html.dark-mode .theme-logo-dark { display: inline; }
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
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        .toast-success {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }
        .toast-error {
            border-color: #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }
        .toast-info {
            border-color: #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
        }
        html.dark-mode .toast {
            background: #0f172a;
            border-color: #1f2a44;
            color: #e5e7eb;
        }
        html.dark-mode .toast-success { border-color: #2f855a; background: #10261d; color: #9ae6b4; }
        html.dark-mode .toast-error { border-color: #9b2c2c; background: #2a1414; color: #feb2b2; }
        html.dark-mode .toast-info { border-color: #2b6cb0; background: #102035; color: #90cdf4; }
    </style>
</head>
<body class="bg-[#FAFAF7] text-gray-800">
    <div class="toast-container" id="toast-root"></div>
    <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Cambiar modo oscuro">
        <svg class="theme-icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M7.05 16.95l-1.414 1.414m0-12.728 1.414 1.414m10.314 10.314 1.414 1.414" />
            <circle cx="12" cy="12" r="4" />
        </svg>
        <svg class="theme-icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
        </svg>
    </button>
    {{ $slot }}
    @stack('scripts')
    <script>
        window.__toastQueue = window.__toastQueue || [];
        @if (session('success'))
            window.__toastQueue.push({ type: 'success', message: @json(session('success')) });
        @endif
        @if ($errors->any())
            window.__toastQueue.push({ type: 'error', message: @json($errors->first()) });
        @endif

        window.showToast = function(type, message) {
            const root = document.getElementById('toast-root');
            if (!root || !message) return;
            const toast = document.createElement('div');
            toast.className = `toast toast-${type || 'info'}`;
            const content = document.createElement('div');
            content.style.flex = '1';
            content.textContent = message;
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.setAttribute('aria-label', 'Cerrar');
            closeBtn.style.marginLeft = 'auto';
            closeBtn.style.color = 'inherit';
            closeBtn.textContent = '✕';
            toast.appendChild(content);
            toast.appendChild(closeBtn);
            closeBtn.addEventListener('click', () => {
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
            const toggle = document.getElementById('theme-toggle');
            if (toggle) {
                toggle.addEventListener('click', () => {
                    const next = !document.documentElement.classList.contains('dark-mode');
                    document.documentElement.classList.toggle('dark-mode', next);
                    localStorage.setItem('sitin-theme', next ? 'dark' : 'light');
                });
            }
        });
    </script>
</body>
</html>
