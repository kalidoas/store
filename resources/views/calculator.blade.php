@extends('layouts.app')

@section('title', 'Calculateur')
@section('page_title', 'Calculateur de marge')
@section('breadcrumb', 'Calculateur · Prévision')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-[12px] bg-white p-6 shadow lg:col-span-2">
        <h2 class="text-lg font-semibold">Simulateur</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold">Prix achat (par pièce)</label>
                <input type="number" step="0.01" min="0" id="calc_purchase" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm">
            </div>
            <div>
                <label class="text-sm font-semibold">Quantité</label>
                <input type="number" min="0" id="calc_quantity" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm" value="1">
            </div>
            <div>
                <label class="text-sm font-semibold">Transport Casa→Berrechid</label>
                <input type="number" step="0.01" min="0" id="calc_transport" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm" value="0">
            </div>
            <div>
                <label class="text-sm font-semibold">Autres frais</label>
                <input type="number" step="0.01" min="0" id="calc_other" class="mt-1 w-full rounded-lg border border-[#E5E7EB] px-3 py-2 text-sm" value="0">
            </div>
            <div class="md:col-span-2">
                <label class="text-sm font-semibold">Marge désirée (%)</label>
                <div class="mt-2 flex items-center gap-4">
                    <input type="range" min="0" max="60" value="20" id="calc_margin_slider" class="w-full">
                    <input type="number" min="0" max="60" id="calc_margin" class="w-20 rounded-lg border border-[#E5E7EB] px-2 py-1 text-sm" value="20">
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-[12px] bg-white p-6 shadow">
        <h3 class="text-lg font-semibold">Résultats</h3>
        <div class="mt-4 space-y-3 text-sm">
            <div class="flex justify-between"><span>Prix de revient/unité</span><span id="calc_cost">-</span></div>
            <div class="flex justify-between"><span>Prix de vente recommandé</span><span id="calc_selling">-</span></div>
            <div class="flex justify-between"><span>Bénéfice/pièce</span><span id="calc_profit_unit">-</span></div>
            <div class="flex justify-between"><span>Bénéfice total (100%)</span><span id="calc_profit_full">-</span></div>
            <div class="flex justify-between"><span>Bénéfice total (75%)</span><span id="calc_profit_75">-</span></div>
            <div class="flex justify-between"><span>Bénéfice total (50%)</span><span id="calc_profit_50">-</span></div>
        </div>
    </div>
</div>

<script>
    const fields = {
        purchase: document.getElementById('calc_purchase'),
        quantity: document.getElementById('calc_quantity'),
        transport: document.getElementById('calc_transport'),
        other: document.getElementById('calc_other'),
        margin: document.getElementById('calc_margin'),
        slider: document.getElementById('calc_margin_slider'),
    };

    const outputs = {
        cost: document.getElementById('calc_cost'),
        selling: document.getElementById('calc_selling'),
        profitUnit: document.getElementById('calc_profit_unit'),
        profitFull: document.getElementById('calc_profit_full'),
        profit75: document.getElementById('calc_profit_75'),
        profit50: document.getElementById('calc_profit_50'),
    };

    const formatMoney = (value) => `${value.toLocaleString('fr-MA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} DH`;

    function updateCalculator() {
        const purchase = parseFloat(fields.purchase.value) || 0;
        const quantity = parseInt(fields.quantity.value || 0, 10);
        const transport = parseFloat(fields.transport.value) || 0;
        const other = parseFloat(fields.other.value) || 0;
        const marginPercent = parseFloat(fields.margin.value) || 0;

        const feesPerUnit = quantity > 0 ? (transport + other) / quantity : 0;
        const costPrice = purchase + feesPerUnit;
        const sellingPrice = costPrice * (1 + marginPercent / 100);
        const profitUnit = sellingPrice - costPrice;
        const profitFull = profitUnit * quantity;

        outputs.cost.textContent = formatMoney(costPrice);
        outputs.selling.textContent = formatMoney(sellingPrice);
        outputs.profitUnit.textContent = formatMoney(profitUnit);
        outputs.profitFull.textContent = formatMoney(profitFull);
        outputs.profit75.textContent = formatMoney(profitUnit * quantity * 0.75);
        outputs.profit50.textContent = formatMoney(profitUnit * quantity * 0.5);
    }

    fields.slider.addEventListener('input', () => {
        fields.margin.value = fields.slider.value;
        updateCalculator();
    });

    fields.margin.addEventListener('input', () => {
        fields.slider.value = fields.margin.value;
        updateCalculator();
    });

    Object.values(fields).forEach((field) => {
        if (field !== fields.slider) {
            field.addEventListener('input', updateCalculator);
        }
    });

    updateCalculator();
</script>
@endsection

