<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeamMemberRequest;
use App\Models\TeamMember;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class TeamMemberController extends Controller
{
    public function __construct(private readonly ImageUploadService $images)
    {
    }

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('search'));

        $teamMembers = TeamMember::query()
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('position', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => TeamMember::query()->count(),
            'active' => TeamMember::query()->active()->count(),
            'inactive' => TeamMember::query()->where('is_active', false)->count(),
        ];

        return view('admin.teams.index', [
            'teamMembers' => $teamMembers,
            'summary' => $summary,
            'filters' => compact('status', 'search'),
        ]);
    }

    public function create(): View
    {
        $teamMember = new TeamMember([
            'sort_order' => (TeamMember::query()->max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return view('admin.teams.form', [
            'teamMember' => $teamMember,
            'media' => ['photo' => null],
        ]);
    }

    public function store(StoreTeamMemberRequest $request): RedirectResponse
    {
        $data = $this->data($request);
        $newFiles = [];

        try {
            $this->prepareNewFile($request, 'photo', 'photo_path', 'teams/photos', $data, $newFiles);

            $teamMember = DB::transaction(fn (): TeamMember => TeamMember::query()->create($data));
        } catch (Throwable $exception) {
            foreach ($newFiles as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }

        return redirect()->route('admin.teams.edit', $teamMember)->with('success', 'Team member berhasil ditambahkan.');
    }

    public function edit(TeamMember $teamMember): View
    {
        return view('admin.teams.form', [
            'teamMember' => $teamMember,
            'media' => ['photo' => $this->images->url($teamMember->photo_path)],
        ]);
    }

    public function update(StoreTeamMemberRequest $request, TeamMember $teamMember): RedirectResponse
    {
        $data = $this->data($request);
        $newFiles = [];
        $oldFilesToDelete = [];

        try {
            $this->prepareFileChange($request, 'photo', 'photo_path', 'teams/photos', $teamMember, $data, $newFiles, $oldFilesToDelete);

            DB::transaction(fn (): bool => $teamMember->update($data));
        } catch (Throwable $exception) {
            foreach ($newFiles as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }

        foreach (array_unique($oldFilesToDelete) as $path) {
            $this->images->delete($path);
        }

        return redirect()->route('admin.teams.edit', $teamMember)->with('success', 'Team member berhasil diperbarui.');
    }

    public function destroy(TeamMember $teamMember): RedirectResponse
    {
        DB::transaction(fn () => $teamMember->delete());

        return redirect()->route('admin.teams.index')->with('success', 'Team member berhasil dihapus.');
    }

    private function data(StoreTeamMemberRequest $request): array
    {
        return Arr::except($request->validated(), ['photo']);
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
        string $column,
        string $directory,
        TeamMember $teamMember,
        array &$data,
        array &$newFiles,
        array &$oldFilesToDelete,
    ): void {
        if (! $request->hasFile($input)) {
            return;
        }

        $oldPath = $teamMember->{$column};
        $newPath = $this->images->store($request->file($input), $directory);
        $newFiles[] = $newPath;
        $data[$column] = $newPath;

        if (filled($oldPath)) {
            $oldFilesToDelete[] = $oldPath;
        }
    }
}
