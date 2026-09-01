<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceEvent;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WarehouseCargoUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function staff(string $role, array $attributes = []): Staff
    {
        static $n = 0;
        $n++;

        return Staff::create(array_merge([
            'full_name' => "Сотрудник $n",
            'login' => "staff$n",
            'password' => sha1(md5('secret')),
            'role' => $role,
            'phone' => '+77010000000',
            'email' => "staff$n@example.com",
            'active' => 1,
        ], $attributes));
    }

    private function invoice(array $attributes = []): Invoice
    {
        return Invoice::create(array_merge([
            'user_id' => 1,
            'date' => '2026-08-12',
            'invoice_number' => 903088,
            'status' => 1,
            'detail_status' => 3,
            'sender_name' => 'Иванов',
            'sender_phone' => '+77010000000',
            'sender_address' => 'ул. Абая 1',
            'sender_city' => 'Алматы',
            'sender_country' => 'Казахстан',
            'recipient_name' => 'Петров',
            'recipient_phone' => '+77020000000',
            'recipient_address' => 'ул. Достык 5',
            'recipient_city' => 'Астана',
            'recipient_country' => 'Казахстан',
            'description' => 'Запчасти',
            'quantity' => 3,
            'weight' => 20,
            'volume_weight' => 25,
            'declared_value' => 0,
        ], $attributes));
    }

    private function update(Invoice $invoice, array $payload)
    {
        return $this->postJson("/api/staff/invoices/{$invoice->id}/cargo", $payload);
    }

    public function test_warehouse_updates_cargo_of_an_invoice_on_its_own_stock(): void
    {
        $warehouse = $this->staff('warehouse');
        $invoice = $this->invoice(['detail_status' => 3, 'warehouse_id' => $warehouse->id]);
        Sanctum::actingAs($warehouse);

        $this->update($invoice, ['quantity' => 5, 'weight' => 41.5, 'volume_weight' => 60])
            ->assertOk()
            ->assertJsonPath('invoice.cargo.quantity_raw', '5')
            ->assertJsonPath('invoice.cargo.weight_raw', '41.5');

        $invoice->refresh();
        $this->assertSame(5, (int) $invoice->quantity);
        $this->assertSame(41.5, (float) $invoice->weight);
        $this->assertSame(60.0, (float) $invoice->volume_weight);
    }

    public function test_warehouse_updates_cargo_before_receiving_it(): void
    {
        $warehouse = $this->staff('warehouse');
        $invoice = $this->invoice(['detail_status' => 2, 'warehouse_id' => null]);
        Sanctum::actingAs($warehouse);

        $this->update($invoice, ['quantity' => 4, 'weight' => 10])->assertOk();

        $this->assertSame(4, (int) $invoice->refresh()->quantity);
    }

    public function test_volume_weight_is_optional(): void
    {
        $warehouse = $this->staff('warehouse');
        $invoice = $this->invoice(['warehouse_id' => $warehouse->id]);
        Sanctum::actingAs($warehouse);

        $this->update($invoice, ['quantity' => 2, 'weight' => 8])->assertOk();

        $this->assertNull($invoice->refresh()->volume_weight);
    }

    public function test_courier_cannot_update_cargo(): void
    {
        $courier = $this->staff('courier');
        $invoice = $this->invoice(['courier_id' => $courier->id]);
        Sanctum::actingAs($courier);

        $this->update($invoice, ['quantity' => 5, 'weight' => 41.5])->assertForbidden();

        $this->assertSame(3, (int) $invoice->refresh()->quantity);
    }

    public function test_another_warehouse_cannot_touch_stock_it_did_not_receive(): void
    {
        $owner = $this->staff('warehouse');
        $stranger = $this->staff('warehouse');
        $invoice = $this->invoice(['detail_status' => 3, 'warehouse_id' => $owner->id]);
        Sanctum::actingAs($stranger);

        $this->update($invoice, ['quantity' => 5, 'weight' => 41.5])->assertForbidden();
    }

    /**
     * На проде накладную часто переводят на этап «На складе» из админки —
     * тогда warehouse_id остаётся пустым и склад её как бы ничей. Блокировать
     * правку в этом случае нельзя: на боевых данных таких большинство.
     */
    public function test_warehouse_updates_stock_that_nobody_claimed(): void
    {
        $warehouse = $this->staff('warehouse');
        $invoice = $this->invoice(['detail_status' => 3, 'warehouse_id' => null]);
        Sanctum::actingAs($warehouse);

        $this->update($invoice, ['quantity' => 5, 'weight' => 41.5])->assertOk();

        $this->assertSame(5, (int) $invoice->refresh()->quantity);
    }

    public function test_cargo_is_frozen_once_shipped(): void
    {
        $warehouse = $this->staff('warehouse');
        $invoice = $this->invoice(['detail_status' => 4, 'warehouse_id' => $warehouse->id]);
        Sanctum::actingAs($warehouse);

        $this->update($invoice, ['quantity' => 5, 'weight' => 41.5])
            ->assertStatus(422);

        $this->assertSame(3, (int) $invoice->refresh()->quantity);
    }

    /** @return list<array{0: array<string, mixed>}> */
    public static function invalidPayloads(): array
    {
        return [
            'мест меньше одного' => [['quantity' => 0, 'weight' => 10]],
            'отрицательный вес' => [['quantity' => 1, 'weight' => -5]],
            'нулевой вес' => [['quantity' => 1, 'weight' => 0]],
            'вес не число' => [['quantity' => 1, 'weight' => 'тяжёлый']],
            'без веса' => [['quantity' => 1]],
            'без мест' => [['weight' => 10]],
            'отрицательный объёмный вес' => [['quantity' => 1, 'weight' => 10, 'volume_weight' => -1]],
        ];
    }

    #[DataProvider('invalidPayloads')]
    public function test_invalid_payload_is_rejected(array $payload): void
    {
        $warehouse = $this->staff('warehouse');
        $invoice = $this->invoice(['warehouse_id' => $warehouse->id]);
        Sanctum::actingAs($warehouse);

        $this->update($invoice, $payload)->assertStatus(422);

        $this->assertSame(3, (int) $invoice->refresh()->quantity);
    }

    public function test_change_is_written_to_the_invoice_journal(): void
    {
        $warehouse = $this->staff('warehouse');
        $invoice = $this->invoice(['warehouse_id' => $warehouse->id]);
        Sanctum::actingAs($warehouse);

        $this->update($invoice, ['quantity' => 5, 'weight' => 41.5, 'volume_weight' => 60])->assertOk();

        $event = InvoiceEvent::where('invoice_id', $invoice->id)->where('event', 'cargo_changed')->first();

        $this->assertNotNull($event, 'событие cargo_changed не записано');
        $this->assertSame($warehouse->id, (int) $event->actor_id);
        $this->assertSame('warehouse', $event->actor_role);
        $this->assertSame('Изменён вес и места', $event->label());
        $this->assertSame('3', (string) $event->meta['quantity']['from']);
        $this->assertSame('5', (string) $event->meta['quantity']['to']);
        $this->assertSame('20', (string) (float) $event->meta['weight']['from']);
        $this->assertSame('41.5', (string) (float) $event->meta['weight']['to']);
    }

    public function test_invoice_response_exposes_warehouse_id(): void
    {
        $warehouse = $this->staff('warehouse');
        $invoice = $this->invoice(['warehouse_id' => $warehouse->id]);
        Sanctum::actingAs($warehouse);

        $this->getJson("/api/staff/invoices/{$invoice->id}")
            ->assertOk()
            ->assertJsonPath('invoice.warehouse_id', $warehouse->id);
    }
}
