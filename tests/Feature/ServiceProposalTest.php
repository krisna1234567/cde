<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Database\Seeders\PublicContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceProposalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_admin_can_upload_pdf_and_public_service_card_opens_its_proposal(): void
    {
        $this->seed(PublicContentSeeder::class);
        $this->actingAs($this->admin())->post(route('admin.services.store'), $this->data([
            'proposal' => $this->pdf(),
        ]))->assertSessionHasNoErrors();
        $service = Service::query()->where('slug', 'proposal-service')->firstOrFail();
        Storage::disk('local')->assertExists($service->proposal_path);
        $this->get(route('admin.services.edit', $service))->assertOk()->assertSee('View Current Proposal');
        $this->get(route('admin.services.proposal', $service))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->get(route('products.services'))->assertOk()->assertSee('serviceProposalModal', false)
            ->assertSee(route('products.proposal', $service->slug), false)->assertSee('View proposal for Proposal Service');
        $this->get(route('products.proposal', $service->slug))->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Disposition', 'inline; filename=proposal-service.pdf')
            ->assertStreamedContent("%PDF-1.4\nTest proposal\n%%EOF");
    }

    public function test_admin_can_replace_preserve_and_remove_proposal(): void
    {
        $this->actingAs($this->admin())->post(route('admin.services.store'), $this->data(['proposal' => $this->pdf()]));
        $service = Service::query()->where('slug', 'proposal-service')->firstOrFail();
        $oldPath = $service->proposal_path;
        $this->put(route('admin.services.update', $service), $this->data())->assertSessionHasNoErrors();
        $this->assertSame($oldPath, $service->fresh()->proposal_path);
        $this->put(route('admin.services.update', $service), $this->data(['proposal' => $this->pdf()]))->assertSessionHasNoErrors();
        $newPath = $service->fresh()->proposal_path;
        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($newPath);
        $this->put(route('admin.services.update', $service), $this->data(['remove_proposal' => true]))->assertSessionHasNoErrors();
        $this->assertNull($service->fresh()->proposal_path);
        Storage::disk('local')->assertMissing($newPath);
        $this->get(route('products.proposal', $service->slug))->assertNotFound();
    }

    public function test_pdf_validation_rejects_other_files_oversized_pdfs_and_product_uploads(): void
    {
        $this->actingAs($this->admin());
        Storage::disk('local')->put('invalid-upload.txt', 'This is plain text, not a PDF.');
        $this->post(route('admin.services.store'), $this->data([
            'proposal' => new UploadedFile(Storage::disk('local')->path('invalid-upload.txt'), 'fake.pdf', null, null, true),
        ]))->assertSessionHasErrors('proposal');
        $this->post(route('admin.services.store'), $this->data([
            'proposal' => UploadedFile::fake()->create('big.pdf', 10241, 'application/pdf'),
        ]))->assertSessionHasErrors('proposal');
        $this->post(route('admin.services.store'), $this->data([
            'item_type' => 'product', 'product_category' => 'Other', 'proposal' => $this->pdf(),
        ]))->assertSessionHasErrors('proposal');
        $this->assertDatabaseCount('services', 0);
    }

    public function test_inactive_deleted_products_and_missing_files_are_not_publicly_accessible(): void
    {
        Storage::disk('local')->put('services/proposals/test.pdf', '%PDF-1.4');
        $service = Service::query()->create($this->data(['proposal_path' => 'services/proposals/test.pdf']));
        $service->update(['is_active' => false]);
        $this->get(route('products.proposal', $service->slug))->assertNotFound();
        $service->update(['is_active' => true, 'item_type' => 'product', 'product_category' => 'Other']);
        $this->get(route('products.proposal', $service->slug))->assertNotFound();
        $service->update(['item_type' => 'service', 'proposal_path' => 'services/proposals/missing.pdf']);
        $this->get(route('products.proposal', $service->slug))->assertNotFound();
        $service->delete();
        $this->get(route('products.proposal', $service->slug))->assertNotFound();
    }

    public function test_changing_service_to_product_removes_old_proposal_and_non_admin_cannot_upload(): void
    {
        Storage::disk('local')->put('services/proposals/test.pdf', '%PDF-1.4');
        $service = Service::query()->create($this->data(['proposal_path' => 'services/proposals/test.pdf']));
        $this->actingAs(User::factory()->create(['is_admin' => false, 'is_active' => true]))
            ->put(route('admin.services.update', $service), $this->data(['proposal' => $this->pdf()]))->assertForbidden();
        $this->get(route('admin.services.proposal', $service))->assertForbidden();
        $this->actingAs($this->admin())->put(route('admin.services.update', $service), $this->data([
            'item_type' => 'product', 'product_category' => 'Other',
        ]))->assertSessionHasNoErrors();
        $this->assertNull($service->fresh()->proposal_path);
        Storage::disk('local')->assertMissing('services/proposals/test.pdf');
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true, 'is_active' => true]);
    }

    private function pdf(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('proposal.pdf', "%PDF-1.4\nTest proposal\n%%EOF");
    }

    private function data(array $overrides = []): array
    {
        return array_merge([
            'item_type' => 'service', 'name' => 'Proposal Service', 'slug' => 'proposal-service',
            'currency' => 'IDR', 'sort_order' => 1, 'is_active' => true, 'is_featured' => false,
        ], $overrides);
    }
}
