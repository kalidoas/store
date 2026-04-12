@extends('layouts.app')

@section('title', 'Produits')
@section('page_title', 'Produits')
@section('breadcrumb', 'Produits · Inventaire')

@section('content')
@php
    $formatMoney = fn ($value) => number_format((float) $value, 2, ',', ' ') . ' DH';
@endphp

<div class="rounded-[12px] bg-white p-6 shadow">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <form class="flex flex-1 flex-wrap gap-3" method="GET" action="{{ route('products.index') }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher un produit..."
                   class="w-full rounded-lg border border-[#E5E7EB] px-4 py-2 text-sm focus:border-[#2563EB] focus:outline-none lg:w-1/3">
            <select name="category" class="rounded-lg border border-[#E5E7EB] px-4 py-2 text-sm focus:border-[#2563EB] focus:outline-none">
                <option value="">Toutes catégories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected($selected_category === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-[#2563EB] px-4 py-2 text-sm font-semibold text-white">Filtrer</button>
        </form>
        <a href="{{ route('products.create') }}" class="rounded-lg bg-[#2563EB] px-4 py-2 text-sm font-semibold text-white">Ajouter produit</a>
    </div>

    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-left text-[#6B7280]">
            <tr>
                <th class="pb-3">Produit</th>
                <th class="pb-3">Quantité</th>
                <th class="pb-3 hidden md:table-cell">Prix achat</th>
                <th class="pb-3 hidden md:table-cell">Prix revient/unité</th>
                <th class="pb-3">Prix vente</th>
                <th class="pb-3">Marge %</th>
                <th class="pb-3 hidden md:table-cell">Statut</th>
                <th class="pb-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @forelse ($products as $product)
                @php
                    $quantityClass = $product->quantity === 0 ? 'text-red-600' : ($product->quantity <= 3 ? 'text-orange-600' : 'text-green-600');
                    $marginClass = $product->margin_percentage < 10 ? 'bg-red-50 text-red-600' : ($product->margin_percentage <= 25 ? 'bg-orange-50 text-orange-600' : 'bg-green-50 text-green-600');
                    $statusLabel = $product->stock_status === 'available' ? 'Disponible' : ($product->stock_status === 'low_stock' ? 'Stock faible' : 'Épuisé');
                    $statusClass = $product->stock_status === 'available' ? 'bg-green-50 text-green-600' : ($product->stock_status === 'low_stock' ? 'bg-orange-50 text-orange-600' : 'bg-red-50 text-red-600');
                @endphp
                <tr data-product-row="{{ $product->id }}">
                    <td class="py-4">
                        <div class="font-semibold">{{ $product->name }}</div>
                        <div class="mt-1 inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-[#2563EB]">
                            <x-category-icon :category="$product->category" size="14" />
                            <span>{{ $product->category }}</span>
                        </div>
                    </td>
                    <td class="py-4">
                        <span class="font-semibold {{ $quantityClass }}" data-quantity="{{ $product->id }}">{{ $product->quantity }}</span>
                    </td>
                    <td class="py-4 hidden md:table-cell">{{ $formatMoney($product->purchase_price) }}</td>
                    <td class="py-4 hidden md:table-cell">{{ $formatMoney($product->cost_price) }}</td>
                    <td class="py-4">{{ $formatMoney($product->selling_price) }}</td>
                    <td class="py-4">
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $marginClass }}">
                            {{ number_format($product->margin_percentage, 1, ',', ' ') }}%
                        </span>
                    </td>
                    <td class="py-4 hidden md:table-cell">
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusClass }}" data-status="{{ $product->id }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('products.edit', $product->id) }}" class="rounded-lg border border-[#E5E7EB] px-3 py-2 text-xs font-semibold text-[#2563EB]">Modifier</a>
                            <button type="button" class="sell-btn rounded-lg border border-green-200 px-3 py-2 text-xs font-semibold text-green-600" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">Vendre</button>
                            <form method="POST" action="{{ route('products.destroy', $product->id) }}" onsubmit="return confirm('Supprimer ce produit ?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600" type="submit">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="py-10" colspan="8">
                        <div class="flex flex-col items-center justify-center text-center text-sm text-[#6B7280]">
                            <svg class="h-16 w-16 text-blue-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                                <rect x="3" y="4" width="18" height="16" rx="2"/>
                                <path d="M3 10h18"/>
                            </svg>
                            <p class="mt-3">Aucun produit. Ajoutez votre premier produit.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.sell-btn').forEach((button) => {
        button.addEventListener('click', async () => {
            const productId = button.dataset.productId;
            const productName = button.dataset.productName;

            try {
                const response = await fetch(`/products/${productId}/sell`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    window.showToast(data.message || `Erreur lors de la vente de ${productName}.`, data.toast_type || 'error');
                    return;
                }

                const quantityCell = document.querySelector(`[data-quantity="${productId}"]`);
                const statusBadge = document.querySelector(`[data-status="${productId}"]`);

                if (quantityCell) {
                    quantityCell.textContent = data.quantity;
                    quantityCell.className = `font-semibold ${data.quantity === 0 ? 'text-red-600' : (data.quantity <= 3 ? 'text-orange-600' : 'text-green-600')}`;
                }

                if (statusBadge) {
                    let label = 'Disponible';
                    let badgeClass = 'bg-green-50 text-green-600';

                    if (data.stock_status === 'low_stock') {
                        label = 'Stock faible';
                        badgeClass = 'bg-orange-50 text-orange-600';
                    } else if (data.stock_status === 'out_of_stock') {
                        label = 'Épuisé';
                        badgeClass = 'bg-red-50 text-red-600';
                    }

                    statusBadge.textContent = label;
                    statusBadge.className = `rounded-full px-2 py-1 text-xs font-semibold ${badgeClass}`;
                }

                window.showToast(data.message || `Vente enregistrée — ${productName}.`, data.toast_type || 'success');
            } catch (error) {
                window.showToast(`Erreur réseau lors de la vente de ${productName}.`, 'error');
            }
        });
    });
</script>
@endsection

