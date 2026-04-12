<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function index()
    {
        $products = Product::all()->sortByDesc('margin_percentage')->values();

        $potentialRevenue = (float) $products->sum(fn (Product $product) => $product->selling_price * $product->quantity);
        $stockCost = (float) $products->sum(fn (Product $product) => ($product->purchase_price * $product->quantity) + $product->transport_fees + $product->other_fees);
        $potentialProfit = $potentialRevenue - $stockCost;
        $averageMargin = (float) $products->avg('margin_percentage');

        $categoryBreakdown = $products
            ->groupBy('category')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'average_margin' => (float) $group->avg('margin_percentage'),
                ];
            });

        return view('reports.index', [
            'products' => $products,
            'potential_revenue' => $potentialRevenue,
            'stock_cost' => $stockCost,
            'potential_profit' => $potentialProfit,
            'average_margin' => $averageMargin,
            'category_breakdown' => $categoryBreakdown,
        ]);
    }

    public function exportCsv(): Response
    {
        $products = Product::all();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="rapport-produits.csv"',
        ];

        $callback = function () use ($products): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Nom',
                'Categorie',
                'Quantite',
                'Prix Achat',
                'Frais Transport',
                'Autres Frais',
                'Prix Vente',
                'Frais/Unite',
                'Prix Revient/Unite',
                'Marge Brute',
                'Marge %',
                'Statut',
                'Notes',
            ], ';');

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->id,
                    $product->name,
                    $product->category,
                    $product->quantity,
                    number_format((float) $product->purchase_price, 2, '.', ''),
                    number_format((float) $product->transport_fees, 2, '.', ''),
                    number_format((float) $product->other_fees, 2, '.', ''),
                    number_format((float) $product->selling_price, 2, '.', ''),
                    number_format((float) $product->fees_per_unit, 2, '.', ''),
                    number_format((float) $product->cost_price, 2, '.', ''),
                    number_format((float) $product->gross_margin, 2, '.', ''),
                    number_format((float) $product->margin_percentage, 2, '.', ''),
                    $product->stock_status,
                    $product->notes,
                ], ';');
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, 'rapport-produits.csv', $headers);
    }
}

