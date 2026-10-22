@extends('layouts.admin')

@section('title', 'Product Categories')
@section('page_heading', 'Product Categories')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Product Categories</h2>
                <p>Kelola kategori untuk form produk dan filter katalog publik.</p>
            </div>
            <a href="{{ route('admin.product-categories.create') }}" class="btn admin-primary-button"><i class="bi bi-plus-lg me-1"></i>Add Category</a>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Category</th><th>Products</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td><strong>{{ $category->name }}</strong></td>
                            <td>{{ $category->products_count }}</td>
                            <td>
                                <div class="admin-row-actions justify-content-end">
                                    <a href="{{ route('admin.product-categories.edit', $category) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                                    <form method="POST" action="{{ route('admin.product-categories.destroy', $category) }}" data-confirm-delete="kategori ini">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" @disabled($category->products_count > 0) title="{{ $category->products_count > 0 ? 'Pindahkan produk ke kategori lain sebelum menghapus.' : 'Hapus kategori' }}"><i class="bi bi-trash me-1"></i>Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="admin-empty-state">Belum ada kategori produk. Tambahkan kategori sebelum membuat produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-muted small mt-3 mb-0">Jumlah produk termasuk produk nonaktif dan produk yang dihapus. Kategori yang masih digunakan tidak dapat dihapus.</p>
        @if($categories->hasPages())<div class="mt-4">{{ $categories->links() }}</div>@endif
    </div>
@endsection
