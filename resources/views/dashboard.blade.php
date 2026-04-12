@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('breadcrumb', 'Dashboard · Vue globale')

@section('content')
@php
    $formatMoney = fn ($value) => number_format((float) $value, 2, ',', ' ') . ' DH';
    $alertCount = $out_of_stock->count() + $low_stock->count();
    $chartDataJson = $chart_products->map(fn ($product) => [
        'name' => $product->name,
        'margin' => round($product->margin_percentage, 1),
    ])->values()->toJson();
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-[12px] bg-white p-5 shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#6B7280]">Total produits</p>
                <p class="text-2xl font-semibold">{{ $total_products }}</p>
            </div>
            <div class="rounded-full bg-blue-50 p-3 text-[#2563EB]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M3 10h18"/>
                </svg>
            </div>
        </div>
    </div>
    <div class="rounded-[12px] bg-white p-5 shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#6B7280]">Valeur stock total</p>
                <p class="text-2xl font-semibold">{{ $formatMoney($total_stock_value) }}</p>
            </div>
            <div class="rounded-full bg-green-50 p-3 text-green-600">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 2v20"/>
                    <path d="M5 9l7-7 7 7"/>
                </svg>
            </div>
        </div>
    </div>
    <div class="rounded-[12px] bg-white p-5 shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#6B7280]">Marge moyenne</p>
                <p class="text-2xl font-semibold">{{ number_format($average_margin, 1, ',', ' ') }}%</p>
            </div>
            <div class="rounded-full bg-purple-50 p-3 text-purple-600">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 20V10"/>
                    <path d="M18 20V4"/>
                    <path d="M6 20v-6"/>
                </svg>
            </div>
        </div>
    </div>
    <div class="rounded-[12px] bg-white p-5 shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#6B7280]">Alertes actives</p>
                <p class="text-2xl font-semibold">{{ $alertCount }}</p>
            </div>
            <div class="rounded-full bg-red-50 p-3 text-red-600">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M10 2h4l6 10-6 10h-4L4 12z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

@if ($out_of_stock->isNotEmpty() || $low_stock->isNotEmpty())
    <div class="mt-8">
        <h2 class="text-lg font-semibold">Alertes stock</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            @foreach ($out_of_stock as $product)
                <div class="rounded-[12px] border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    ⚠️ {{ $product->name }} — Épuisé. Réapprovisionner depuis Casa.
                </div>
            @endforeach
            @foreach ($low_stock as $product)
                <div class="rounded-[12px] border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800">
                    📦 {{ $product->name }} — Stock faible: {{ $product->quantity }} pièces restantes.
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="mt-8 grid gap-6 xl:grid-cols-3">
    <div class="rounded-[12px] bg-white p-6 shadow xl:col-span-1">
        <h2 class="text-lg font-semibold">Top rentabilité</h2>
        @if ($top_product)
            <div class="mt-4">
                <p class="text-xl font-semibold">{{ $top_product->name }}</p>
                <p class="mt-2 text-sm text-[#6B7280]">Catégorie</p>
                <span class="mt-1 inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-[#2563EB]">
                    {{ $top_product->category }}
                </span>
                <p class="mt-4 text-3xl font-semibold text-[#2563EB]">{{ number_format($top_product->margin_percentage, 1, ',', ' ') }}%</p>
                <p class="text-sm text-[#6B7280]">Marge brute</p>
            </div>
        @else
            <p class="mt-4 text-sm text-[#6B7280]">Aucun produit disponible.</p>
        @endif
    </div>

    <div class="rounded-[12px] bg-white p-6 shadow xl:col-span-2">
        <h2 class="text-lg font-semibold">Ventes récentes</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-[#6B7280]">
                <tr>
                    <th class="pb-2">Produit</th>
                    <th class="pb-2">Quantité</th>
                    <th class="pb-2">Montant</th>
                    <th class="pb-2">Date</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @forelse ($recent_sales as $sale)
                    <tr>
                        <td class="py-3 font-medium">{{ $sale->product?->name ?? 'Produit supprimé' }}</td>
                        <td class="py-3">{{ $sale->quantity_sold }}</td>
                        <td class="py-3">{{ $formatMoney($sale->total_amount) }}</td>
                        <td class="py-3 text-[#6B7280]">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="py-3 text-[#6B7280]" colspan="4">Aucune vente enregistrée.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-8 rounded-[12px] bg-white p-6 shadow">
    <h2 class="text-lg font-semibold">Top 6 marges par produit</h2>
    <canvas id="marginChart" class="mt-6 h-56 w-full"></canvas>
</div>

<script>
    const chartData = {!! $chartDataJson !!};

    const canvas = document.getElementById('marginChart');
    const ctx = canvas.getContext('2d');
    const padding = 30;

    function drawChart() {
        const { width, height } = canvas.getBoundingClientRect();
        canvas.width = width;
        canvas.height = height;
        ctx.clearRect(0, 0, width, height);

        if (!chartData.length) {
            ctx.fillStyle = '#6B7280';
            ctx.font = '14px system-ui';
            ctx.fillText('Aucune donnée disponible.', padding, height / 2);
            return;
        }

        const maxMargin = Math.max(...chartData.map((item) => item.margin)) || 1;
        const barWidth = (width - padding * 2) / chartData.length - 12;

        chartData.forEach((item, index) => {
            const barHeight = ((height - padding * 2) * item.margin) / maxMargin;
            const x = padding + index * (barWidth + 12);
            const y = height - padding - barHeight;

            ctx.fillStyle = '#2563EB';
            ctx.fillRect(x, y, barWidth, barHeight);

            ctx.fillStyle = '#111827';
            ctx.font = '12px system-ui';
            ctx.fillText(`${item.margin}%`, x, y - 6);

            ctx.save();
            ctx.translate(x + barWidth / 2, height - 8);
            ctx.rotate(-Math.PI / 6);
            ctx.textAlign = 'center';
            ctx.fillStyle = '#6B7280';
            ctx.fillText(item.name, 0, 0);
            ctx.restore();
        });
    }

    window.addEventListener('resize', drawChart);
    drawChart();
</script>
@endsection

