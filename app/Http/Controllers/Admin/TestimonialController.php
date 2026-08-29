<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $rating = $request->query('rating');
        $search = trim((string) $request->query('search'));

        if (! $this->tableExists()) {
            return view('admin.testimonials.index', [
                'testimonials' => new LengthAwarePaginator([], 0, 15, 1, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]),
                'summary' => ['total' => 0, 'active' => 0, 'inactive' => 0],
                'filters' => compact('status', 'rating', 'search'),
                'migrationMissing' => true,
            ]);
        }

        $testimonials = Testimonial::query()
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when(in_array((string) $rating, ['1', '2', '3', '4', '5'], true), fn ($query) => $query->where('rating', $rating))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('role', 'like', '%'.$search.'%')
                        ->orWhere('quote', 'like', '%'.$search.'%');
                });
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => Testimonial::query()->count(),
            'active' => Testimonial::query()->active()->count(),
            'inactive' => Testimonial::query()->where('is_active', false)->count(),
        ];

        return view('admin.testimonials.index', [
            'testimonials' => $testimonials,
            'summary' => $summary,
            'filters' => compact('status', 'rating', 'search'),
        ]);
    }

    public function create(): View
    {
        abort_if(! $this->tableExists(), 503, 'Tabel testimonials belum tersedia. Aktifkan write mode database lalu jalankan php artisan migrate.');

        $testimonial = new Testimonial([
            'rating' => 5,
            'sort_order' => (Testimonial::query()->max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        abort_if(! $this->tableExists(), 503, 'Tabel testimonials belum tersedia. Aktifkan write mode database lalu jalankan php artisan migrate.');

        $testimonial = DB::transaction(fn (): Testimonial => Testimonial::query()->create($request->validated()));

        return redirect()->route('admin.testimonials.edit', $testimonial)->with('success', 'Testimonial berhasil ditambahkan.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(StoreTestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        DB::transaction(fn (): bool => $testimonial->update($request->validated()));

        return redirect()->route('admin.testimonials.edit', $testimonial)->with('success', 'Testimonial berhasil diperbarui.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        DB::transaction(fn () => $testimonial->delete());

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial berhasil dihapus.');
    }

    private function tableExists(): bool
    {
        return Schema::hasTable('testimonials');
    }
}
