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
    <aside class="fixed left-0 top-0 h-full w-60 bg-white border-r border-[#E5E7EB] px-6 py-6">
        <div class="text-2xl font-bold text-[#2563EB]">Store</div>
        <nav class="mt-10 space-y-2 text-sm">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-[#2563EB]' : 'text-[#6B7280] hover:text-[#2563EB]' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 12l9-9 9 9"/>
                    <path d="M9 21V9h6v12"/>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('products.*') ? 'bg-blue-50 text-[#2563EB]' : 'text-[#6B7280] hover:text-[#2563EB]' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M3 10h18"/>
                </svg>
                Produits
            </a>
            <a href="{{ route('calculator.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('calculator.*') ? 'bg-blue-50 text-[#2563EB]' : 'text-[#6B7280] hover:text-[#2563EB]' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                    <path d="M8 6h8M8 10h8M8 14h2M12 14h2M16 14h2"/>
                </svg>
                Calculateur
            </a>
            <a href="{{ route('reports.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-[#2563EB]' : 'text-[#6B7280] hover:text-[#2563EB]' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 19h16"/>
                    <path d="M6 16V8"/>
                    <path d="M12 16V4"/>
                    <path d="M18 16v-6"/>
                </svg>
                Rapport
            </a>
        </nav>
        <div class="absolute bottom-6 left-6 text-xs text-[#6B7280]">
            {{ now()->format('d/m/Y') }}
        </div>
    </aside>

    <main class="ml-60 min-h-screen">
        <header class="sticky top-0 z-10 bg-[#F9FAFB]/80 backdrop-blur border-b border-[#E5E7EB] px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">@yield('page_title', 'Tableau de bord')</h1>
                    <p class="text-sm text-[#6B7280]">@yield('breadcrumb', 'Berrechid · Casablanca')</p>
                </div>
            </div>
        </header>

        <div class="px-8 py-8">
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

