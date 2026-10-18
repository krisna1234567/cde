<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\PublicContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSalesInquiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PublicContentSeeder::class);
    }

    public function test_detail_links_to_sales_form_with_product_specific_default_message(): void
    {
        $product = Service::query()->products()->active()->firstOrFail();
        $this->get(route('products.show', $product->slug))->assertOk()
            ->assertSee('Contact Sales')->assertSee(route('products.sales', $product->slug), false);
        $this->get(route('products.sales', $product->slug))->assertOk()
            ->assertSee('saya mau tau lebih detail terkait produk '.$product->name.'.')
            ->assertSee(route('products.sales.store', $product->slug), false);
    }

    public function test_sales_inquiry_is_saved_in_existing_contact_messages_and_visible_to_admin(): void
    {
        $product = Service::query()->products()->active()->firstOrFail();
        $url = route('products.sales', $product->slug);
        $message = 'saya mau tau lebih detail terkait produk '.$product->name.'.';
        $this->from($url)->post(route('products.sales.store', $product->slug), $this->payload(['message' => $message]))
            ->assertRedirect($url)->assertSessionHas('success');
        $this->assertDatabaseCount('contact_messages', 1);
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'buyer@example.com', 'message' => $message, 'status' => 'new',
            'subject' => 'Product inquiry: '.$product->name, 'source_url' => $url,
        ]);
        $contact = ContactMessage::query()->firstOrFail();
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $this->actingAs($admin)->get(route('admin.contact-messages.index'))->assertOk()->assertSee('buyer@example.com');
        $this->get(route('admin.contact-messages.show', $contact))->assertOk()->assertSee($message);
    }

    public function test_validation_preserves_edited_message_and_rejects_spam(): void
    {
        $product = Service::query()->products()->active()->firstOrFail();
        $url = route('products.sales', $product->slug);
        $message = 'Please send specifications and a quotation for my project.';
        $this->from($url)->post(route('products.sales.store', $product->slug), $this->payload(['email' => 'invalid', 'message' => $message]))
            ->assertRedirect($url)->assertSessionHasErrors('email')->assertSessionHasInput('message', $message);
        $this->get($url)->assertOk()->assertSee($message);
        $this->from($url)->post(route('products.sales.store', $product->slug), $this->payload(['website' => 'spam']))
            ->assertSessionHasErrors('website');
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_unavailable_products_cannot_receive_sales_inquiries(): void
    {
        $product = Service::query()->products()->firstOrFail();
        $product->update(['is_active' => false]);
        $service = Service::query()->services()->firstOrFail();
        foreach ([$product->slug, $service->slug, 'missing-product'] as $slug) {
            $this->get(route('products.sales', $slug))->assertNotFound();
            $this->post(route('products.sales.store', $slug), $this->payload())->assertNotFound();
        }
        $this->assertDatabaseCount('contact_messages', 0);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Solar', 'last_name' => 'Buyer', 'email' => 'buyer@example.com',
            'phone' => '+62 811 0000 0000', 'message' => 'Please send me more details about this product.', 'website' => '',
        ], $overrides);
    }
}
