<?php

namespace Tests\Unit;

use App\Models\Invoice;
use PHPUnit\Framework\TestCase;

class InvoicePublicTrackingTest extends TestCase
{
    private function invoice(array $attributes = []): Invoice
    {
        return new Invoice(array_merge([
            'invoice_number' => 903088,
            'status' => 0,
            'detail_status' => 0,
            'sender_city' => 'Алматы',
            'recipient_city' => 'Астана',
            'quantity' => 3,
            'weight' => 20,
        ], $attributes));
    }

    /** @return list<string> */
    private function states(array $steps): array
    {
        return array_column($steps, 'state');
    }

    public function test_new_invoice_is_on_the_first_step(): void
    {
        $tracking = $this->invoice(['status' => 0])->publicTracking();

        $this->assertSame('Заявка создана', $tracking['status_label']);
        $this->assertSame(['current', 'pending', 'pending', 'pending'], $this->states($tracking['steps']));
    }

    public function test_in_progress_invoice_marks_earlier_steps_done(): void
    {
        $tracking = $this->invoice(['status' => 2])->publicTracking();

        $this->assertSame('Отправлено', $tracking['status_label']);
        $this->assertSame(['done', 'done', 'current', 'pending'], $this->states($tracking['steps']));
    }

    public function test_delivered_invoice_has_the_whole_chain_done(): void
    {
        $tracking = $this->invoice(['status' => 3])->publicTracking();

        $this->assertSame('Исполнена', $tracking['status_label']);
        $this->assertSame(['done', 'done', 'done', 'done'], $this->states($tracking['steps']));
    }

    /**
     * 2866 из 3088 накладных в базе: административный статус «Исполнена»,
     * а detail_status так и остался нулём. Показывать по нему «Заявка создана»
     * нельзя — это главный кейс всей фичи.
     */
    public function test_delivered_invoice_without_detail_status_still_reads_as_delivered(): void
    {
        $tracking = $this->invoice(['status' => 3, 'detail_status' => 0])->publicTracking();

        $this->assertSame('Исполнена', $tracking['status_label']);
        $this->assertSame([], $tracking['detail_steps']);
    }

    public function test_cancelled_invoice_has_no_chain(): void
    {
        $tracking = $this->invoice(['status' => 4])->publicTracking();

        $this->assertTrue($tracking['cancelled']);
        $this->assertSame('Отменена', $tracking['status_label']);
        $this->assertSame([], $tracking['steps']);
        $this->assertSame([], $tracking['detail_steps']);
    }

    public function test_detail_chain_appears_once_the_courier_flow_started(): void
    {
        $tracking = $this->invoice(['status' => 1, 'detail_status' => 3])->publicTracking();

        $this->assertCount(7, $tracking['detail_steps']);
        $this->assertSame('На складе', $tracking['detail_steps'][3]['title']);
        $this->assertSame(
            ['done', 'done', 'done', 'current', 'pending', 'pending', 'pending'],
            $this->states($tracking['detail_steps']),
        );
    }

    public function test_finished_detail_chain_is_fully_done(): void
    {
        $tracking = $this->invoice(['status' => 3, 'detail_status' => 6])->publicTracking();

        $this->assertSame(
            array_fill(0, 7, 'done'),
            $this->states($tracking['detail_steps']),
        );
    }

    public function test_unknown_status_does_not_break_the_page(): void
    {
        $tracking = $this->invoice(['status' => 99])->publicTracking();

        $this->assertSame('—', $tracking['status_label']);
        $this->assertSame([], $tracking['steps']);
    }

    public function test_route_and_cargo_are_exposed(): void
    {
        $tracking = $this->invoice()->publicTracking();

        $this->assertSame('903088', $tracking['number']);
        $this->assertSame('Алматы', $tracking['from']);
        $this->assertSame('Астана', $tracking['to']);
        $this->assertSame('3', $tracking['quantity']);
        $this->assertSame('20', $tracking['weight']);
    }

    public function test_tracking_exposes_names_but_not_contacts_or_cargo(): void
    {
        // По решению владельца имена сторон показываются. Но телефоны, улицы
        // и описание груза в JSON трекинга не отдаём — их нет и в макете.
        $tracking = $this->invoice([
            'sender_name' => 'Иванов Иван',
            'sender_phone' => '+77010000000',
            'sender_address' => 'ул. Абая 1',
            'recipient_name' => 'Петров Пётр',
            'recipient_phone' => '+77020000000',
            'recipient_address' => 'ул. Достык 5',
            'description' => 'Ноутбуки Apple',
        ])->publicTracking();

        $leaked = json_encode($tracking, JSON_UNESCAPED_UNICODE);
        foreach (['77010000000', '77020000000', 'Абая', 'Достык', 'Ноутбуки'] as $secret) {
            $this->assertStringNotContainsString($secret, $leaked);
        }
    }

    public function test_exposes_sender_and_recipient_with_city(): void
    {
        $tracking = $this->invoice([
            'sender_company' => 'ТОО Хилти',
            'sender_name' => 'Иванов',
            'sender_city' => 'Алматы',
            'recipient_company' => '',
            'recipient_name' => 'Петров Пётр',
            'recipient_city' => 'Астана',
        ])->publicTracking();

        $this->assertSame('ТОО Хилти', $tracking['sender']['name']);
        $this->assertSame('Алматы', $tracking['sender']['city']);
        $this->assertSame('Петров Пётр', $tracking['recipient']['name'], 'без компании берём ФИО');
        $this->assertSame('Астана', $tracking['recipient']['city']);
    }

    public function test_insured_flag_reflects_declared_value(): void
    {
        $this->assertTrue($this->invoice(['declared_value' => 50000])->publicTracking()['insured']);
        $this->assertFalse($this->invoice(['declared_value' => 0])->publicTracking()['insured']);
    }

    public function test_stage_times_are_empty_without_loaded_events(): void
    {
        $tracking = $this->invoice(['status' => 1, 'detail_status' => 3])->publicTracking();

        // Без подгруженных событий времена этапов не выдумываются.
        $this->assertIsArray($tracking['stage_times']);
        $this->assertArrayNotHasKey(3, $tracking['stage_times']);
    }
}
