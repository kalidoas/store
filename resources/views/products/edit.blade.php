@extends('layouts.app')

@section('title', 'Modifier produit')
@section('page_title', 'Modifier produit')
@section('breadcrumb', 'Produits · Modifier')

@section('content')
@php
    $formatMoney = fn ($value) => number_format((float) $value, 2, ',', ' ') . ' DH';
@endphp

<div class="grid gap-6 lg:grid-cols-3">
    <form method="POST" action="{{ secure_url('/products/' . $product->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold">Nom du produit</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold">Catégorie</label>
                <select name="category" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm">
                    <option value="">Choisir</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(old('category', $product->category) === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold">Quantité</label>
                <input type="number" name="quantity" min="0" step="1" oninput="this.value=this.value.replace(/^0+(?=\d)/, '')" value="{{ old('quantity', $product->quantity) }}" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm" id="quantity">
                @error('quantity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold">Prix achat (par pièce)</label>
                <input type="number" step="any" min="0" oninput="this.value=this.value.replace(/^0+(?=\d)/, '')" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm" id="purchase_price">
                @error('purchase_price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold">Frais transport Casa→Berrechid</label>
                <input type="number" step="any" min="0" oninput="this.value=this.value.replace(/^0+(?=\d)/, '')" name="transport_fees" value="{{ old('transport_fees', $product->transport_fees) }}" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm" id="transport_fees">
                @error('transport_fees')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold">Autres frais</label>
                <input type="number" step="any" min="0" oninput="this.value=this.value.replace(/^0+(?=\d)/, '')" name="other_fees" value="{{ old('other_fees', $product->other_fees) }}" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm" id="other_fees">
                @error('other_fees')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold">Prix de vente</label>
                <input type="number" step="any" min="0" oninput="this.value=this.value.replace(/^0+(?=\d)/, '')" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm" id="selling_price">
                @error('selling_price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="text-sm font-semibold">Notes</label>
                <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm">{{ old('notes', $product->notes) }}</textarea>
                @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-4">
            <label class="text-sm font-semibold">Photo du produit</label>
            <div class="mt-1 border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-blue-400 transition" onclick="document.getElementById('imageInput').click()">
                @if($product->image)
                    <img id="imagePreview" src="{{ $product->image_url }}" class="mx-auto mb-3 rounded-lg object-cover" style="max-height:160px">
                @else
                    <img id="imagePreview" src="" alt="" class="hidden mx-auto mb-3 rounded-lg object-cover" style="max-height:160px">
                @endif
                <svg id="uploadIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto mb-2 text-gray-400" width="40" height="40">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                <p id="uploadText" class="text-sm text-gray-500">Cliquez pour ajouter une photo</p>
                <p class="text-xs text-gray-400 mt-1">PNG, JPG jusqu'à 2MB</p>
            </div>
            <input type="file" id="imageInput" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
        </div>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('products.index') }}" class="rounded-lg border border-[#E5E7EB] px-4 py-2 text-sm font-semibold">Annuler</a>
            <button type="submit" class="rounded-lg bg-[#2563EB] px-4 py-2 text-sm font-semibold text-white">Mettre à jour</button>
        </div>
    </form>

    <div class="rounded-[12px] bg-white p-6 shadow">
        <h3 class="text-lg font-semibold">Aperçu des calculs</h3>
        <div class="mt-4 space-y-3 text-sm">
            <div class="flex justify-between"><span>Frais/unité</span><span id="fees_per_unit">-</span></div>
            <div class="flex justify-between"><span>Prix de revient/unité</span><span id="cost_price">-</span></div>
            <div class="flex justify-between"><span>Marge brute/pièce</span><span id="gross_margin">-</span></div>
            <div class="flex justify-between"><span>Marge %</span><span id="margin_percentage" class="font-semibold">-</span></div>
            <div class="flex justify-between"><span>Bénéfice total (100%)</span><span id="total_profit">-</span></div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('uploadIcon').classList.add('hidden');
                document.getElementById('uploadText').textContent = input.files[0].name;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    const fields = {
        purchase_price: document.getElementById('purchase_price'),
        quantity: document.getElementById('quantity'),
        transport_fees: document.getElementById('transport_fees'),
        other_fees: document.getElementById('other_fees'),
        selling_price: document.getElementById('selling_price'),
    };

    const outputs = {
        fees_per_unit: document.getElementById('fees_per_unit'),
        cost_price: document.getElementById('cost_price'),
        gross_margin: document.getElementById('gross_margin'),
        margin_percentage: document.getElementById('margin_percentage'),
        total_profit: document.getElementById('total_profit'),
    };

    const formatMoney = (value) => `${value.toLocaleString('fr-MA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} DH`;

    function updatePreview() {
        const purchase = parseFloat(fields.purchase_price.value) || 0;
        const quantity = parseInt(fields.quantity.value || 0, 10);
        const transport = parseFloat(fields.transport_fees.value) || 0;
        const other = parseFloat(fields.other_fees.value) || 0;
        const selling = parseFloat(fields.selling_price.value) || 0;

        const feesPerUnit = quantity > 0 ? (transport + other) / quantity : 0;
        const costPrice = purchase + feesPerUnit;
        const grossMargin = selling - costPrice;
        const marginPercentage = selling > 0 ? (grossMargin / selling) * 100 : 0;
        const totalProfit = grossMargin * quantity;

        outputs.fees_per_unit.textContent = formatMoney(feesPerUnit);
        outputs.cost_price.textContent = formatMoney(costPrice);
        outputs.gross_margin.textContent = formatMoney(grossMargin);
        outputs.margin_percentage.textContent = `${marginPercentage.toFixed(1)}%`;
        outputs.total_profit.textContent = formatMoney(totalProfit);

        const marginColor = marginPercentage < 10 ? 'text-red-600' : (marginPercentage <= 25 ? 'text-orange-600' : 'text-green-600');
        outputs.margin_percentage.className = `font-semibold ${marginColor}`;
    }

    Object.values(fields).forEach((field) => field.addEventListener('input', updatePreview));
    updatePreview();
</script>
@endsection

