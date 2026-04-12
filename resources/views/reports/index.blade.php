@extends('layouts.app')

@section('title', 'Rapport')
@section('page_title', 'Rapport de rentabilité')
@section('breadcrumb', 'Rapport · Analyse')

@section('content')
@php
    $formatMoney = fn ($value) => number_format((float) $value, 2, ',', ' ') . ' DH';
    $topProductId = $products->first()?->id;
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-[12px] bg-white p-5 shadow">
        <p class="text-sm text-[#6B7280]">CA potentiel</p>
        <p class="text-2xl font-semibold">{{ $formatMoney($potential_revenue) }}</p>
    </div>
    <div class="rounded-[12px] bg-white p-5 shadow">
        <p class="text-sm text-[#6B7280]">Coût stock</p>
        <p class="text-2xl font-semibold">{{ $formatMoney($stock_cost) }}</p>
    </div>
    <div class="rounded-[12px] bg-white p-5 shadow">
        <p class="text-sm text-[#6B7280]">Bénéfice potentiel</p>
        <p class="text-2xl font-semibold">{{ $formatMoney($potential_profit) }}</p>
    </div>
    <div class="rounded-[12px] bg-white p-5 shadow">
        <p class="text-sm text-[#6B7280]">Marge moyenne</p>
        <p class="text-2xl font-semibold">{{ number_format($average_margin, 1, ',', ' ') }}%</p>
    </div>
</div>

<div class="mt-8 rounded-[12px] bg-white p-6 shadow">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Produits par rentabilité</h2>
        <a href="{{ route('reports.export') }}" class="rounded-lg bg-[#2563EB] px-4 py-2 text-sm font-semibold text-white">Exporter CSV</a>
    </div>

    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-left text-[#6B7280]">
            <tr>
                <th class="pb-3">Produit</th>
                <th class="pb-3">Catégorie</th>
                <th class="pb-3">Marge %</th>
                <th class="pb-3">Stock</th>
                <th class="pb-3">Badges</th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @forelse ($products as $product)
                <tr>
                    <td class="py-3 font-semibold">{{ $product->name }}</td>
                    <td class="py-3">{{ $product->category }}</td>
                    <td class="py-3">{{ number_format($product->margin_percentage, 1, ',', ' ') }}%</td>
                    <td class="py-3">{{ $product->quantity }}</td>
                    <td class="py-3">
                        <div class="flex flex-wrap gap-2 text-xs">
                            @if ($product->id === $topProductId)
                                <span class="rounded-full bg-blue-50 px-2 py-1 font-semibold text-[#2563EB]">🏆 Top rentabilité</span>
                            @endif
                            @if ($product->stock_status !== 'available')
                                <span class="rounded-full bg-orange-50 px-2 py-1 font-semibold text-orange-600">⚠️ Réapprovisionner</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="py-4 text-[#6B7280]" colspan="5">Aucune donnée disponible.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8 rounded-[12px] bg-white p-6 shadow">
    <h2 class="text-lg font-semibold">Répartition par catégorie</h2>
    <div class="mt-4 grid gap-4 md:grid-cols-2">
        @forelse ($category_breakdown as $category => $data)
            <div class="rounded-lg border border-[#E5E7EB] px-4 py-3">
                <div class="flex items-center justify-between">
                    <span class="font-semibold">{{ $category }}</span>
                    <span class="text-xs text-[#6B7280]">{{ $data['count'] }} produit(s)</span>
                </div>
                <p class="mt-2 text-sm text-[#6B7280]">Marge moyenne: {{ number_format($data['average_margin'], 1, ',', ' ') }}%</p>
            </div>
        @empty
            <p class="text-sm text-[#6B7280]">Aucune catégorie disponible.</p>
        @endforelse
    </div>
</div>
@endsection

