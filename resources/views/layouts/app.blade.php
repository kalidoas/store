<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') | Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F9FAFB] text-[#111827] font-[system-ui,-apple-system,sans-serif]">
<div class="min-h-screen">
    <div id="sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-black/40 md:hidden"></div>
    <div id="sidebar" class="fixed top-0 left-0 h-full w-60 bg-gray-900 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 flex flex-col">
        <div class="px-6 py-5 border-b border-gray-800">
            <span class="text-white font-bold text-xl tracking-tight">Store</span>
        </div>
        <nav class="px-4 py-5 space-y-2 text-sm">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'flex items-center gap-3 px-4 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium' : 'flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition text-sm font-medium' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 12l9-9 9 9"/>
                    <path d="M9 21V9h6v12"/>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'flex items-center gap-3 px-4 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium' : 'flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition text-sm font-medium' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M3 10h18"/>
                </svg>
                Produits
            </a>
            <a href="{{ route('calculator.index') }}" class="{{ request()->routeIs('calculator.*') ? 'flex items-center gap-3 px-4 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium' : 'flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition text-sm font-medium' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                    <path d="M8 6h8M8 10h8M8 14h2M12 14h2M16 14h2"/>
                </svg>
                Calculateur
            </a>
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'flex items-center gap-3 px-4 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium' : 'flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition text-sm font-medium' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 19h16"/>
                    <path d="M6 16V8"/>
                    <path d="M12 16V4"/>
                    <path d="M18 16v-6"/>
                </svg>
                Rapport
            </a>
        </nav>
        <div class="px-6 py-4 border-t border-gray-800 mt-auto">
            <p class="text-gray-500 text-xs">{{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    <main class="ml-0 min-h-screen md:ml-60">
        <div class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm flex items-center gap-4 px-4 py-3 md:ml-60">
            <button id="sidebarToggle" class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="22" height="22">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <div>
                <h1 class="font-semibold text-gray-800 text-base leading-tight">@yield('title', 'Dashboard')</h1>
                <p class="text-gray-400 text-xs">@yield('breadcrumb', '')</p>
            </div>
        </div>

        <div class="px-6 py-6 md:px-8 md:py-8">
            @if (session('success'))
                <div class="flash-message mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="flash-message mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="flash-message mb-4 rounded-xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800">
                    {{ session('warning') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<div id="toast-container" class="fixed right-6 top-6 z-50 space-y-3"></div>

<script>
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach((message) => {
        setTimeout(() => message.remove(), 4000);
    });

    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const toggleButton = document.getElementById('sidebarToggle');

    const isDesktop = () => window.innerWidth >= 768;

    const openSidebar = () => {
        if (!sidebar || !backdrop || isDesktop()) {
            return;
        }
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        toggleButton?.setAttribute('aria-expanded', 'true');
    };

    const closeSidebar = () => {
        if (!sidebar || !backdrop) {
            return;
        }
        if (!isDesktop()) {
            sidebar.classList.add('-translate-x-full');
        } else {
            sidebar.classList.remove('-translate-x-full');
        }
        backdrop.classList.add('hidden');
        toggleButton?.setAttribute('aria-expanded', 'false');
    };

    toggleButton?.addEventListener('click', () => {
        if (sidebar?.classList.contains('-translate-x-full')) {
            openSidebar();
        } else {
            closeSidebar();
        }
    });

    backdrop?.addEventListener('click', closeSidebar);
    window.addEventListener('resize', closeSidebar);

    window.showToast = function (message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const colors = {
            success: 'border-green-200 bg-green-50 text-green-800',
            warning: 'border-orange-200 bg-orange-50 text-orange-800',
            error: 'border-red-200 bg-red-50 text-red-800',
        };

        toast.className = `rounded-xl border px-4 py-3 text-sm shadow ${colors[type] ?? colors.success}`;
        toast.textContent = message;
        container.appendChild(toast);

        setTimeout(() => toast.remove(), 4000);
    };
</script>
</body>
</html>

