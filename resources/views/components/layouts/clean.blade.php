<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <style>
        body, html {
            overflow: hidden;
            height: 100%;
            margin: 0;
        }
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
    </style>
</head>
<body class="bg-[#FAFAF7] text-gray-800">
    <div class="toast-container" id="toast-root"></div>
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
        });
    </script>
</body>
</html>
