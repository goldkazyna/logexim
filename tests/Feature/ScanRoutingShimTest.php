<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Приложение (без пересборки) выбирает экран после сканирования по полю
 * courier_id / receiving_courier_id. Раз курьеров заранее не назначают,
 * findByNumber подставляет сканирующего курьера в ответ, чтобы приложение
 * сразу вело на «Забрал + подпись». В БД при этом ничего не меняется —
 * закрепление происходит при подтверждении забора.
 */
class ScanRoutingShimTest extends TestCase
{
    use RefreshDatabase;

    private function staff(string $role = 'courier'): Staff
    {
        static $n = 0;
        $n++;

        return Staff::create([
            'full_name' => "С $n", 'login' => "s$n", 'password' => sha1(md5('x')),
            'role' => $role, 'roles' => [$role], 'active' => 1,
        ]);
    }

    private function invoice(array $a = []): Invoice
    {
        static $n = 903000;
        $n++;

        return Invoice::create(array_merge([
            'user_id' => 1, 'date' => '2026-09-14', 'invoice_number' => $n,
            'status' => 0, 'detail_status' => 0,
            'sender_name' => 'О', 'sender_phone' => '+7', 'sender_address' => 'a',
            'sender_city' => 'Алматы', 'sender_country' => 'KZ',
            'recipient_name' => 'П', 'recipient_phone' => '+7', 'recipient_address' => 'b',
            'recipient_city' => 'Астана', 'recipient_country' => 'KZ',
            'description' => 'x', 'quantity' => 1, 'weight' => 5, 'declared_value' => 0,
        ], $a));
    }

    private function scan(Invoice $inv, string $role = 'courier')
    {
        return $this->getJson("/api/staff/invoices/by-number/{$inv->invoice_number}", ['X-Staff-Role' => $role]);
    }

    public function test_pickup_stage_unassigned_returns_scanner_as_courier(): void
    {
        $courier = $this->staff('courier');
        $inv = $this->invoice(['detail_status' => 0]);
        Sanctum::actingAs($courier);

        $this->scan($inv)->assertOk()->assertJsonPath('invoice.courier_id', $courier->id);

        // В БД по-прежнему пусто — только ответ.
        $this->assertNull($inv->refresh()->courier_id);
    }

    public function test_destination_stage_unassigned_returns_scanner_as_receiving(): void
    {
        $agent = $this->staff('agent');
        $inv = $this->invoice(['detail_status' => 4]);
        Sanctum::actingAs($agent);

        $this->scan($inv, 'agent')->assertOk()->assertJsonPath('invoice.receiving_courier_id', $agent->id);
        $this->assertNull($inv->refresh()->receiving_courier_id);
    }

    public function test_does_not_override_when_assigned_to_another(): void
    {
        $courier = $this->staff('courier');
        $other = $this->staff('courier');
        $inv = $this->invoice(['detail_status' => 0, 'courier_id' => $other->id]);
        Sanctum::actingAs($courier);

        $this->scan($inv)->assertOk()->assertJsonPath('invoice.courier_id', $other->id);
    }

    public function test_does_not_override_on_other_stages(): void
    {
        $courier = $this->staff('courier');
        $inv = $this->invoice(['detail_status' => 3]); // на складе — не забор
        Sanctum::actingAs($courier);

        $this->scan($inv)->assertOk()->assertJsonPath('invoice.courier_id', null);
    }
}
