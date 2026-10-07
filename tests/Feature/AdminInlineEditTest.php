<?php

namespace Tests\Feature;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Правка отправителя/получателя на месте в карточке накладной. */
class AdminInlineEditTest extends TestCase
{
    use RefreshDatabase;

    private Invoice $inv;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inv = Invoice::create([
            'user_id' => 1, 'date' => '2026-10-07', 'invoice_number' => 906001,
            'status' => 0, 'detail_status' => 0,
            'sender_name' => 'Иванов', 'sender_phone' => '+7', 'sender_address' => 'a',
            'sender_city' => 'Алматы', 'sender_country' => 'KZ',
            'recipient_name' => 'Петров', 'recipient_phone' => '+7', 'recipient_address' => 'b',
            'recipient_city' => 'Астана', 'recipient_country' => 'KZ',
            'description' => 'x', 'quantity' => 1, 'weight' => 5, 'declared_value' => 0,
        ]);
    }

    private function admin(): static
    {
        return $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
    }

    public function test_card_shows_pencil_for_every_party_field(): void
    {
        $html = $this->admin()->get("/admin/invoices/view/{$this->inv->id}")->assertOk()->getContent();

        foreach (['name', 'company', 'phone', 'city', 'region', 'district', 'address'] as $f) {
            $this->assertStringContainsString('data-field="sender_' . $f . '"', $html);
            $this->assertStringContainsString('data-field="recipient_' . $f . '"', $html);
        }
    }

    public function test_field_is_saved_and_logged(): void
    {
        $this->admin()->postJson("/admin/invoices/{$this->inv->id}/field", ['field' => 'recipient_city', 'value' => ' Шымкент '])
            ->assertOk()->assertJson(['value' => 'Шымкент']);

        $this->assertSame('Шымкент', $this->inv->refresh()->recipient_city);
        $this->assertDatabaseHas('invoice_events', ['invoice_id' => $this->inv->id, 'event' => 'party_changed']);
        $this->get("/admin/invoices/view/{$this->inv->id}")->assertSee('Город получателя: «Астана» → «Шымкент»', false);
    }

    public function test_required_field_cannot_be_emptied_but_optional_can(): void
    {
        $this->admin()->postJson("/admin/invoices/{$this->inv->id}/field", ['field' => 'sender_name', 'value' => '  '])
            ->assertStatus(422);
        $this->assertSame('Иванов', $this->inv->refresh()->sender_name);

        $this->postJson("/admin/invoices/{$this->inv->id}/field", ['field' => 'sender_company', 'value' => ''])
            ->assertOk();
    }

    public function test_only_party_fields_and_only_admin_or_dispatcher(): void
    {
        $this->admin()->postJson("/admin/invoices/{$this->inv->id}/field", ['field' => 'payment', 'value' => '0'])
            ->assertStatus(422);

        $this->withSession(['role' => 'courier', 'roles' => ['courier'], 'staff_id' => 5])
            ->postJson("/admin/invoices/{$this->inv->id}/field", ['field' => 'sender_name', 'value' => 'X'])
            ->assertStatus(403);
        $this->assertSame('Иванов', $this->inv->refresh()->sender_name);
    }
}
