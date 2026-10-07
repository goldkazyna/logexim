<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceEvent;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/** Отчёт по курьерам: кто сколько забрал/доставил за период, PDF. */
class CourierReportTest extends TestCase
{
    use RefreshDatabase;

    private Staff $ivan;
    private Staff $petr;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-10-07 15:00:00');
        $this->ivan = $this->staff('Иван Курьеров', 'cour_i');
        $this->petr = $this->staff('Пётр Доставкин', 'cour_p');
    }

    private function staff(string $name, string $login): Staff
    {
        return Staff::create([
            'full_name' => $name, 'login' => $login, 'password' => sha1(md5('x')),
            'role' => 'courier', 'roles' => ['courier'], 'active' => 1,
        ]);
    }

    private function invoice(int $n, float $weight = 5, int $places = 1): Invoice
    {
        return Invoice::create([
            'user_id' => 1, 'date' => '2026-10-07', 'invoice_number' => $n,
            'status' => 0, 'detail_status' => 0,
            'sender_name' => 'О', 'sender_phone' => '+7', 'sender_address' => 'a',
            'sender_city' => 'Алматы', 'sender_country' => 'KZ',
            'recipient_name' => 'Получатель ' . $n, 'recipient_phone' => '+7', 'recipient_address' => 'b',
            'recipient_city' => 'Астана', 'recipient_country' => 'KZ',
            'description' => 'x', 'quantity' => $places, 'weight' => $weight, 'declared_value' => 0,
        ]);
    }

    private function event(Invoice $inv, Staff $s, string $event, string $at): void
    {
        InvoiceEvent::create([
            'invoice_id' => $inv->id, 'event' => $event, 'actor_type' => 'staff',
            'actor_id' => $s->id, 'actor_role' => 'courier', 'actor_name' => $s->full_name,
            'created_at' => $at,
        ]);
    }

    private function admin(): static
    {
        return $this->withSession(['admin' => 'admin', 'role' => 'admin', 'roles' => ['admin']]);
    }

    public function test_counts_deliveries_and_pickups_per_courier_for_today(): void
    {
        $a = $this->invoice(907001, 10, 2);
        $b = $this->invoice(907002, 4.5, 1);
        $c = $this->invoice(907003);
        $this->event($a, $this->ivan, 'pickup', '2026-10-07 09:00:00');
        $this->event($a, $this->ivan, 'delivery', '2026-10-07 12:00:00');
        $this->event($b, $this->ivan, 'delivery', '2026-10-07 13:00:00');
        $this->event($c, $this->petr, 'pickup', '2026-10-07 10:00:00');
        $this->event($c, $this->petr, 'delivery', '2026-10-06 18:00:00'); // вчера — не сегодня

        $html = $this->admin()->get('/admin/reports/couriers')->assertOk()->getContent();

        $this->assertStringContainsString('Иван Курьеров', $html);
        $this->assertStringContainsString('№907002', $html, 'детализация по накладным');
        $data = app(\App\Support\CourierReport::class)->build(Carbon::today(), Carbon::today());
        $this->assertSame('Иван Курьеров', $data['rows'][0]['name'], 'больше доставок — выше');
        $this->assertSame(2, $data['rows'][0]['counts']['delivery']);
        $this->assertEquals(14.5, $data['rows'][0]['delivered_weight']);
        $this->assertSame(3, $data['rows'][0]['delivered_places']);
        $this->assertSame(0, $data['rows'][1]['counts']['delivery']);
        $this->assertSame(1, $data['rows'][1]['counts']['pickup']);
        $this->assertSame(2, $data['totals']['counts']['delivery']);
    }

    public function test_custom_period_and_courier_filter(): void
    {
        $c = $this->invoice(907003);
        $this->event($c, $this->petr, 'delivery', '2026-10-06 18:00:00');
        $d = $this->invoice(907004);
        $this->event($d, $this->ivan, 'delivery', '2026-10-06 11:00:00');

        $this->admin()->get('/admin/reports/couriers?from=2026-10-06&to=2026-10-06&courier=' . $this->petr->id)
            ->assertOk()
            ->assertSee('Пётр Доставкин')
            ->assertSee('№907003')
            ->assertDontSee('№907004');
    }

    public function test_pdf_download(): void
    {
        $a = $this->invoice(907001);
        $this->event($a, $this->ivan, 'delivery', '2026-10-07 12:00:00');

        $res = $this->admin()->get('/admin/reports/couriers/pdf?preset=today')->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('content-type'));
        $this->assertStringContainsString('otchet_kurery_2026-10-07.pdf', $res->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_couriers_cannot_open_the_report(): void
    {
        $this->withSession(['role' => 'courier', 'roles' => ['courier'], 'staff_id' => $this->ivan->id])
            ->get('/admin/reports/couriers')->assertRedirect('/admin');
    }

    public function test_deleted_invoices_are_skipped(): void
    {
        $a = $this->invoice(907001);
        $this->event($a, $this->ivan, 'delivery', '2026-10-07 12:00:00');
        $a->delete();

        $data = app(\App\Support\CourierReport::class)->build(Carbon::today(), Carbon::today());
        $this->assertSame([], $data['rows']);
    }
}
