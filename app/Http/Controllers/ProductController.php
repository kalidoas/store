<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();
        $category = $request->string('category')->toString();

        $query = Product::query();

        if ($search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        $products = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Product::categories(),
            'selected_category' => $category,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('products.create', [
            'categories' => Product::categories(),
        ]);
    }

    public function store(Request $request)
    {
        \URL::forceScheme('https');
        $validated = $this->validateProduct($request);

        $validated['transport_fees'] = $validated['transport_fees'] ?? 0;
        $validated['other_fees'] = $validated['other_fees'] ?? 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produit ajouté avec succès.');
    }

    public function edit(int $id)
    {
        $product = Product::findOrFail($id);

        return view('products.edit', [
            'product' => $product,
            'categories' => Product::categories(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        \URL::forceScheme('https');
        $product = Product::findOrFail($id);
        $validated = $this->validateProduct($request);

        $validated['transport_fees'] = $validated['transport_fees'] ?? 0;
        $validated['other_fees'] = $validated['other_fees'] ?? 0;

        if ($request->hasFile('image')) {
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produit supprimé.');
    }

    public function sell(int $id)
    {
        $product = Product::findOrFail($id);

        if ($product->quantity <= 0) {
            return response()->json([
                'quantity' => $product->quantity,
                'stock_status' => $product->stock_status,
                'message' => "RUPTURE DE STOCK — {$product->name} est épuisé.",
                'toast_type' => 'error',
            ], 409);
        }

        DB::transaction(function () use ($product): void {
            $product->decrement('quantity', 1);
            $product->refresh();

            Sale::create([
                'product_id' => $product->id,
                'quantity_sold' => 1,
                'selling_price_at_sale' => $product->selling_price,
                'total_amount' => $product->selling_price,
            ]);
        });

        $product->refresh();
        $toastType = 'success';
        $message = "Vente enregistrée — {$product->name}.";

        if ($product->quantity === 0) {
            $toastType = 'error';
            $message = "RUPTURE DE STOCK — {$product->name} est épuisé.";
        } elseif ($product->quantity <= 3) {
            $toastType = 'warning';
            $message = "Stock faible — Il reste {$product->quantity} pièce(s) de {$product->name}.";
        }

        return response()->json([
            'quantity' => $product->quantity,
            'stock_status' => $product->stock_status,
            'message' => $message,
            'toast_type' => $toastType,
        ]);
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(Product::categories())],
            'quantity' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'transport_fees' => ['nullable', 'numeric', 'min:0'],
            'other_fees' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
