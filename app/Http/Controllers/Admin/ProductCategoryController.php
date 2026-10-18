<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.product-categories.index', [
            'categories' => ProductCategory::query()->withCount('products')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.product-categories.form', ['category' => new ProductCategory]);
    }

    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        ProductCategory::query()->create($request->validated());

        return redirect()->route('admin.product-categories.index')->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    public function edit(ProductCategory $productCategory): View
    {
        return view('admin.product-categories.form', ['category' => $productCategory]);
    }

    public function update(StoreProductCategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        $productCategory->update($request->validated());

        return redirect()->route('admin.product-categories.index')->with('success', 'Kategori produk berhasil diperbarui.');
    }

    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        return DB::transaction(function () use ($productCategory): RedirectResponse {
            $category = ProductCategory::query()->lockForUpdate()->findOrFail($productCategory->id);
            if ($category->products()->exists()) {
                return back()->withErrors(['category' => 'Kategori masih digunakan produk (termasuk produk yang dihapus). Ubah kategori produk tersebut terlebih dahulu.']);
            }
            $category->delete();

            return redirect()->route('admin.product-categories.index')->with('success', 'Kategori produk berhasil dihapus.');
        });
    }
}
