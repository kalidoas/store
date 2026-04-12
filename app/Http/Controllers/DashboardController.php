<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::all();
        $totalProducts = $products->count();
        $totalStockValue = (float) Product::sum(DB::raw('purchase_price * quantity'));
        $averageMargin = (float) $products->avg('margin_percentage');
        $outOfStock = Product::where('quantity', 0)->get();
        $lowStock = Product::where('quantity', '<=', 3)->where('quantity', '>', 0)->get();
        $topProduct = $products->sortByDesc('margin_percentage')->first();
        $recentSales = Sale::with('product')->latest()->take(5)->get();
        $chartProducts = $products->sortByDesc('margin_percentage')->take(6)->values();
        $categories = Product::selectRaw('category, count(*) as count, AVG((selling_price - purchase_price - ((transport_fees + other_fees) / NULLIF(quantity,1))) / NULLIF(selling_price,0) * 100) as avg_margin')
            ->groupBy('category')
            ->get();

        return view('dashboard', [
            'total_products' => $totalProducts,
            'total_stock_value' => $totalStockValue,
            'average_margin' => $averageMargin,
            'out_of_stock' => $outOfStock,
            'low_stock' => $lowStock,
            'top_product' => $topProduct,
            'recent_sales' => $recentSales,
            'chart_products' => $chartProducts,
            'categories' => $categories,
        ]);
    }
}
