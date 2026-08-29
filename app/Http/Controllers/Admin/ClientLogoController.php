<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientLogoRequest;
use App\Models\ClientLogo;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class ClientLogoController extends Controller
{
    public function __construct(private readonly ImageUploadService $images)
    {
    }

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('search'));

        $clients = ClientLogo::query()
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('website_url', 'like', '%'.$search.'%');
                });
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => ClientLogo::query()->count(),
            'active' => ClientLogo::query()->active()->count(),
            'inactive' => ClientLogo::query()->where('is_active', false)->count(),
        ];

        return view('admin.clients.index', [
            'clients' => $clients,
            'summary' => $summary,
            'filters' => compact('status', 'search'),
        ]);
    }

    public function create(): View
    {
        $client = new ClientLogo([
            'sort_order' => (ClientLogo::query()->max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return view('admin.clients.form', [
            'client' => $client,
            'media' => ['logo' => null],
        ]);
    }

    public function store(StoreClientLogoRequest $request): RedirectResponse
    {
        $data = $this->data($request);
        $newFiles = [];

        try {
            $this->prepareNewFile($request, 'logo', 'logo_path', 'clients/logos', $data, $newFiles);

            $client = DB::transaction(fn (): ClientLogo => ClientLogo::query()->create($data));
        } catch (Throwable $exception) {
            foreach ($newFiles as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }

        return redirect()->route('admin.clients.edit', $client)->with('success', 'Client logo berhasil ditambahkan.');
    }

    public function edit(ClientLogo $clientLogo): View
    {
        return view('admin.clients.form', [
            'client' => $clientLogo,
            'media' => ['logo' => $this->images->url($clientLogo->logo_path)],
        ]);
    }

    public function update(StoreClientLogoRequest $request, ClientLogo $clientLogo): RedirectResponse
    {
        $data = $this->data($request);
        $newFiles = [];
        $oldFilesToDelete = [];

        try {
            $this->prepareFileChange($request, 'logo', 'remove_logo', 'logo_path', 'clients/logos', $clientLogo, $data, $newFiles, $oldFilesToDelete);

            DB::transaction(fn (): bool => $clientLogo->update($data));
        } catch (Throwable $exception) {
            foreach ($newFiles as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }

        foreach (array_unique($oldFilesToDelete) as $path) {
            $this->images->delete($path);
        }

        return redirect()->route('admin.clients.edit', $clientLogo)->with('success', 'Client logo berhasil diperbarui.');
    }

    public function destroy(ClientLogo $clientLogo): RedirectResponse
    {
        DB::transaction(fn () => $clientLogo->delete());

        return redirect()->route('admin.clients.index')->with('success', 'Client logo berhasil dihapus.');
    }

    private function data(StoreClientLogoRequest $request): array
    {
        return Arr::except($request->validated(), ['logo', 'remove_logo']);
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
        ClientLogo $clientLogo,
        array &$data,
        array &$newFiles,
        array &$oldFilesToDelete,
    ): void {
        $oldPath = $clientLogo->{$column};

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
