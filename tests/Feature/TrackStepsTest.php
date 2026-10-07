<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/** Путь накладной (сайт, мобилка, админка) — шаги с реальным временем. */
class TrackStepsTest extends TestCase
{
    use RefreshDatabase;

    private function courier(): Staff
    {
        return Staff::create([
            'full_name' => 'К', 'login' => 'k1', 'password' => sha1(md5('x')),
            'role' => 'courier', 'roles' => ['courier'], 'active' => 1,
        ]);
    }

    private function invoice(bool $sameCity): Invoice
    {
        return Invoice::create([
            'user_id' => 1, 'date' => '2026-10-07', 'invoice_number' => 904001,
            'status' => 0, 'detail_status' => 0, 'same_city' => $sameCity,
            'sender_name' => 'О', 'sender_phone' => '+7', 'sender_address' => 'a',
            'sender_city' => 'Байсерке', 'sender_country' => 'KZ',
            'recipient_name' => 'П', 'recipient_phone' => '+7', 'recipient_address' => 'b',
            'recipient_city' => 'Алматы', 'recipient_country' => 'KZ',
            'description' => 'x', 'quantity' => 1, 'weight' => 5, 'declared_value' => 0,
        ]);
    }

    private function sig(): string
    {
        return 'data:image/png;base64,' . base64_encode(str_repeat('sig', 8));
    }

    public function test_local_path_on_site_has_times_for_each_passed_step(): void
    {
        $inv = $this->invoice(true);
        Sanctum::actingAs($this->courier());

        Carbon::setTestNow('2026-10-07 10:15:00');
        $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])->assertOk();

        $steps = $this->postJson('/ajax/trackInvoice', ['invoice_number' => '904001'])
            ->assertOk()->json('invoice.detail_steps');

        $this->assertSame(['Заявка создана', 'Курьер забрал груз', 'Доставлен'], array_column($steps, 'title'));
        $this->assertSame(['done', 'current', 'pending'], array_column($steps, 'state'));
        $this->assertSame('07.10.2026 10:15', $steps[1]['at'], 'время забора у шага «Курьер забрал»');
        $this->assertNull($steps[2]['at']);

        Carbon::setTestNow('2026-10-07 14:40:00');
        $this->postJson("/api/staff/invoices/{$inv->id}/deliver", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])->assertOk();

        $steps = $this->postJson('/ajax/trackInvoice', ['invoice_number' => '904001'])->json('invoice.detail_steps');
        $this->assertSame(['done', 'done', 'done'], array_column($steps, 'state'));
        $this->assertSame('07.10.2026 10:15', $steps[1]['at']);
        $this->assertSame('07.10.2026 14:40', $steps[2]['at']);
    }

    public function test_staff_api_returns_ready_timeline_for_mobile(): void
    {
        $inv = $this->invoice(true);
        Sanctum::actingAs($this->courier());

        Carbon::setTestNow('2026-10-07 09:00:00');
        $res = $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])
            ->assertOk();

        $this->assertTrue($res->json('invoice.local'));
        $this->assertSame('Курьер забрал', $res->json('invoice.detail_status_label'));
        $timeline = $res->json('invoice.timeline');
        $this->assertSame(['Заявка создана', 'Курьер забрал', 'Доставлено'], array_column($timeline, 'title'));
        $this->assertSame('07.10.2026 09:00', $timeline[1]['at']);
    }

    public function test_regular_path_keeps_seven_steps_with_times(): void
    {
        $inv = $this->invoice(false);
        Sanctum::actingAs($this->courier());

        Carbon::setTestNow('2026-10-07 11:30:00');
        $res = $this->postJson("/api/staff/invoices/{$inv->id}/pickup", ['signature' => $this->sig()], ['X-Staff-Role' => 'courier'])->assertOk();

        $timeline = $res->json('invoice.timeline');
        $this->assertCount(7, $timeline);
        $this->assertSame('current', $timeline[2]['state']);
        $this->assertSame('07.10.2026 11:30', $timeline[2]['at']);
    }

    public function test_admin_can_switch_invoice_to_path_without_warehouse(): void
    {
        $inv = $this->invoice(false);
        $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);

        $this->post("/admin/invoices/update/{$inv->id}", [
            'status' => 0, 'detail_status' => 0, 'date' => '2026-10-07', 'payment' => 0, 'same_city' => '1',
        ])->assertRedirect();

        $this->assertTrue($inv->refresh()->isLocalDelivery());
        $this->get("/admin/invoices/view/{$inv->id}")->assertOk()->assertSee('получатель в том же городе');
    }
}
