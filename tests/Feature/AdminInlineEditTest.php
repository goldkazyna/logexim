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
        $this->assertDatabaseHas('invoice_events', ['invoice_id' => $this->inv->id, 'event' => 'field_changed']);
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
        $this->admin()->postJson("/admin/invoices/{$this->inv->id}/field", ['field' => 'status', 'value' => '4'])
            ->assertStatus(422);

        $this->withSession(['role' => 'courier', 'roles' => ['courier'], 'staff_id' => 5])
            ->postJson("/admin/invoices/{$this->inv->id}/field", ['field' => 'sender_name', 'value' => 'X'])
            ->assertStatus(403);
        $this->assertSame('Иванов', $this->inv->refresh()->sender_name);
    }

    public function test_cargo_fields_are_editable_with_type_checks(): void
    {
        $id = $this->inv->id;
        $this->admin()->postJson("/admin/invoices/{$id}/field", ['field' => 'quantity', 'value' => '0'])->assertStatus(422);
        $this->postJson("/admin/invoices/{$id}/field", ['field' => 'weight', 'value' => 'abc'])->assertStatus(422);

        $this->postJson("/admin/invoices/{$id}/field", ['field' => 'quantity', 'value' => '3'])->assertOk()->assertJson(['display' => '3']);
        $this->postJson("/admin/invoices/{$id}/field", ['field' => 'weight', 'value' => '12,5'])->assertOk()->assertJson(['value' => '12.5']);
        $this->postJson("/admin/invoices/{$id}/field", ['field' => 'volume_weight', 'value' => ''])->assertOk()->assertJson(['display' => '—']);
        $this->postJson("/admin/invoices/{$id}/field", ['field' => 'fragile', 'value' => '1'])->assertOk()->assertJson(['display' => 'Да']);
        $this->postJson("/admin/invoices/{$id}/field", ['field' => 'description', 'value' => "Документы\nи образцы"])->assertOk();

        $inv = $this->inv->refresh();
        $this->assertSame(3, (int) $inv->quantity);
        $this->assertEquals(12.5, (float) $inv->weight);
        $this->assertNull($inv->volume_weight);
        $this->assertTrue((bool) $inv->fragile);
    }

    public function test_payment_fields_are_admin_only(): void
    {
        $id = $this->inv->id;
        $this->withSession(['role' => 'dispatcher', 'roles' => ['dispatcher'], 'staff_id' => 7])
            ->postJson("/admin/invoices/{$id}/field", ['field' => 'payment', 'value' => '5000'])
            ->assertStatus(403);

        $this->admin()->postJson("/admin/invoices/{$id}/field", ['field' => 'payment', 'value' => '5000'])
            ->assertOk()->assertJson(['display' => '5000 KZT']);
        $this->postJson("/admin/invoices/{$id}/field", ['field' => 'payment_methods', 'value' => ['payment_recipient', 'payment_cash']])
            ->assertOk()->assertJson(['display' => 'Оплата получателем, Оплата наличными']);
        $this->postJson("/admin/invoices/{$id}/field", ['field' => 'special', 'value' => 'Звонить заранее'])->assertOk();

        $inv = $this->inv->refresh();
        $this->assertEquals(5000, (float) $inv->payment);
        $this->assertTrue((bool) $inv->payment_recipient);
        $this->assertTrue((bool) $inv->payment_cash);
        $this->assertFalse((bool) $inv->payment_sender);
        $this->assertSame('Звонить заранее', $inv->special);
        $this->get("/admin/invoices/view/{$id}")->assertSee('Способ оплаты: «—» → «Оплата получателем, Оплата наличными»', false);
    }

    public function test_bulk_save_no_longer_wipes_inline_fields(): void
    {
        $this->inv->forceFill(['volume_weight' => 7, 'payment' => 1500])->saveQuietly();

        $this->admin()->post("/admin/invoices/update/{$this->inv->id}", ['date' => '2026-10-07', 'detail_status' => '0'])
            ->assertRedirect();

        $inv = $this->inv->refresh();
        $this->assertEquals(7, (float) $inv->volume_weight);
        $this->assertEquals(1500, (float) $inv->payment);
    }
}
