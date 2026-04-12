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
    <aside id="sidebar" class="fixed left-0 top-0 z-50 h-full w-60 -translate-x-full bg-gray-900 border-r border-gray-800 px-6 py-6 transition-transform duration-200 md:translate-x-0">
        <div class="text-xl font-bold text-white">Store</div>
        <nav class="mt-10 space-y-2 text-sm">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 12l9-9 9 9"/>
                    <path d="M9 21V9h6v12"/>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M3 10h18"/>
                </svg>
                Produits
            </a>
            <a href="{{ route('calculator.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('calculator.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                    <path d="M8 6h8M8 10h8M8 14h2M12 14h2M16 14h2"/>
                </svg>
                Calculateur
            </a>
            <a href="{{ route('reports.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 19h16"/>
                    <path d="M6 16V8"/>
                    <path d="M12 16V4"/>
                    <path d="M18 16v-6"/>
                </svg>
                Rapport
            </a>
        </nav>
        <div class="absolute bottom-6 left-6 text-xs text-gray-500">
            {{ now()->format('d/m/Y') }}
        </div>
    </aside>

    <main class="ml-0 min-h-screen md:ml-60">
        <header class="sticky top-0 z-40 bg-white border-b border-[#E5E7EB] px-6 py-4 shadow-sm md:px-8 md:py-6">
            <div class="flex items-center justify-between">
                <div class="flex min-w-0 items-center gap-3">
                    <button id="sidebar-toggle" type="button" class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white shadow-sm transition hover:bg-gray-50 md:hidden" aria-controls="sidebar" aria-expanded="false">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="6" x2="20" y2="6" />
                            <line x1="4" y1="12" x2="20" y2="12" />
                            <line x1="4" y1="18" x2="20" y2="18" />
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <h1 class="truncate text-2xl font-medium text-gray-800">@yield('page_title', 'Tableau de bord')</h1>
                        <p class="text-sm text-[#6B7280]">@yield('breadcrumb', 'Berrechid · Casablanca')</p>
                    </div>
                </div>
            </div>
        </header>

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
    const toggleButton = document.getElementById('sidebar-toggle');

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

