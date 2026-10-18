<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ServiceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ServiceController extends Controller
{
    public function __construct(private readonly ImageUploadService $images)
    {
    }

    public function index(Request $request): View
    {
        $type = $request->query('type');
        $status = $request->query('status');
        $search = trim((string) $request->query('search'));

        $services = Service::query()
            ->when(in_array($type, array_column(ServiceType::cases(), 'value'), true), fn ($query) => $query->where('item_type', $type))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('brand', 'like', '%'.$search.'%')
                        ->orWhere('slug', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('item_type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'products' => Service::query()->products()->count(),
            'services' => Service::query()->services()->count(),
            'active' => Service::query()->active()->count(),
        ];

        return view('admin.services.index', [
            'services' => $services,
            'types' => ServiceType::cases(),
            'summary' => $summary,
            'filters' => compact('type', 'status', 'search'),
        ]);
    }

    public function create(Request $request): View
    {
        $requestedType = $request->query('type');
        $type = ServiceType::tryFrom((string) $requestedType) ?? ServiceType::Product;
        $service = new Service([
            'item_type' => $type->value,
            'currency' => $type === ServiceType::Product ? 'USD' : 'IDR',
            'sort_order' => (Service::query()->where('item_type', $type->value)->max('sort_order') ?? 0) + 1,
            'is_featured' => true,
            'is_active' => true,
        ]);

        return view('admin.services.form', [
            'service' => $service,
            'types' => ServiceType::cases(),
            'media' => ['image' => null],
        ]);
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $data = $this->data($request);
        $newFiles = [];
        $newProposal = null;

        try {
            $this->prepareNewFile($request, 'image', 'image_path', 'services/images', $data, $newFiles);
            $newProposal = $this->storeProposal($request, $data);

            $service = DB::transaction(function () use ($data): Service {
                $service = Service::query()->create($data);
                $this->syncAutomaticSeo($service);

                return $service;
            });
        } catch (Throwable $exception) {
            if ($newProposal) {
                Storage::disk('local')->delete($newProposal);
            }
            foreach ($newFiles as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }

        return redirect()->route('admin.services.edit', $service)->with('success', 'Product & Service berhasil ditambahkan.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', [
            'service' => $service,
            'types' => ServiceType::cases(),
            'media' => [
                'image' => $this->images->url($service->image_path),
            ],
        ]);
    }

    public function update(StoreServiceRequest $request, Service $service): RedirectResponse
    {
        $data = $this->data($request);
        $newFiles = [];
        $oldFilesToDelete = [];
        $oldOgImagePath = $service->og_image_path;
        $oldProposal = $service->proposal_path;
        $newProposal = null;

        try {
            $this->prepareFileChange($request, 'image', 'remove_image', 'image_path', 'services/images', $service, $data, $newFiles, $oldFilesToDelete);
            $newProposal = $this->storeProposal($request, $data);
            if (! $newProposal && ($request->boolean('remove_proposal') || $request->input('item_type') !== ServiceType::Service->value)) {
                $data['proposal_path'] = null;
            }

            DB::transaction(function () use ($service, $data): void {
                $service->update($data);
                $service->refresh();
                $this->syncAutomaticSeo($service);
            });
        } catch (Throwable $exception) {
            if ($newProposal) {
                Storage::disk('local')->delete($newProposal);
            }
            foreach ($newFiles as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }

        if (filled($oldOgImagePath) && $oldOgImagePath !== $service->og_image_path) {
            $oldFilesToDelete[] = $oldOgImagePath;
        }
        if ($oldProposal && $oldProposal !== $service->proposal_path) {
            Storage::disk('local')->delete($oldProposal);
        }

        foreach (array_unique($oldFilesToDelete) as $path) {
            $this->images->delete($path);
        }

        return redirect()->route('admin.services.edit', $service)->with('success', 'Product & Service berhasil diperbarui.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        DB::transaction(fn () => $service->delete());

        return redirect()->route('admin.services.index')->with('success', 'Product & Service berhasil dihapus.');
    }

    private function data(StoreServiceRequest $request): array
    {
        return Arr::except($request->validated(), ['image', 'remove_image', 'proposal', 'remove_proposal']);
    }

    private function storeProposal(StoreServiceRequest $request, array &$data): ?string
    {
        if (! $request->hasFile('proposal')) {
            return null;
        }
        $path = $request->file('proposal')->store('services/proposals', 'local');
        if (! $path) {
            throw new \RuntimeException('Proposal PDF gagal disimpan.');
        }
        $data['proposal_path'] = $path;

        return $path;
    }

    public function proposal(Service $service): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        abort_unless($service->item_type === ServiceType::Service && $service->hasProposal(), 404);

        return Storage::disk('local')->response($service->proposal_path, $service->slug.'.pdf', [
            'Content-Type' => 'application/pdf', 'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ], 'inline');
    }

    private function syncAutomaticSeo(Service $service): void
    {
        $service->forceFill([
            'meta_title' => $this->automaticMetaTitle($service),
            'meta_description' => $this->automaticMetaDescription($service),
            'canonical_url' => $service->item_type === ServiceType::Product
                ? route('products.show', $service->slug)
                : route('products.index'),
            'og_image_path' => null,
        ])->saveQuietly();
    }

    private function automaticMetaTitle(Service $service): string
    {
        return Str::limit($service->name.' - '.$this->siteShortName(), 255, '');
    }

    private function automaticMetaDescription(Service $service): string
    {
        $description = $service->short_description ?: $this->plainText($service->description);

        if (blank($description)) {
            $description = $service->item_type === ServiceType::Product
                ? 'Product information for '.$service->name.($service->brand ? ' by '.$service->brand : '').'.'
                : 'Service information for '.$service->name.'.';
        }

        return Str::limit($description, 500, '');
    }

    private function siteShortName(): string
    {
        $setting = SiteSetting::query()->where('key', SiteSetting::MAIN_KEY)->first();

        return trim((string) ($setting?->short_name ?: $setting?->company_name ?: config('app.name')));
    }

    private function plainText(?string $content): string
    {
        $content = html_entity_decode(strip_tags((string) $content), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/', ' ', $content));
    }

    private function prepareNewFile(FormRequest $request, string $input, string $column, string $directory, array &$data, array &$newFiles): void
    {
        if (! $request->hasFile($input)) {
            return;
        }

        $path = $this->images->store($request->file($input), $directory);
        $newFiles[] = $path;
        $data[$column] = $path;
    }

    private function prepareFileChange(
        FormRequest $request,
        string $input,
        string $removeInput,
        string $column,
        string $directory,
        Service $service,
        array &$data,
        array &$newFiles,
        array &$oldFilesToDelete,
    ): void {
        $oldPath = $service->{$column};

        if ($request->hasFile($input)) {
            $newPath = $this->images->store($request->file($input), $directory);
            $newFiles[] = $newPath;
            $data[$column] = $newPath;

            if (filled($oldPath)) {
                $oldFilesToDelete[] = $oldPath;
            }

            return;
        }

        if ($request->boolean($removeInput)) {
            $data[$column] = null;

            if (filled($oldPath)) {
                $oldFilesToDelete[] = $oldPath;
            }
        }
    }
}
