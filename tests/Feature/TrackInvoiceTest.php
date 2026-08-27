<?php

namespace Tests\Feature;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeInvoice(array $attributes = []): Invoice
    {
        return Invoice::create(array_merge([
            'user_id' => 1,
            'date' => '2026-08-12',
            'invoice_number' => 903088,
            'status' => 3,
            'detail_status' => 0,
            'sender_name' => 'Иванов Иван',
            'sender_phone' => '+77010000000',
            'sender_address' => 'ул. Абая 1',
            'sender_city' => 'Алматы',
            'recipient_name' => 'Петров Пётр',
            'recipient_phone' => '+77020000000',
            'recipient_address' => 'ул. Достык 5',
            'recipient_city' => 'Астана',
            'sender_country' => 'Казахстан',
            'recipient_country' => 'Казахстан',
            'declared_value' => 0,
            'description' => 'Ноутбуки Apple',
            'quantity' => 3,
            'weight' => 20,
        ], $attributes));
    }

    public function test_known_invoice_number_returns_its_status(): void
    {
        $this->makeInvoice();

        $this->postJson('/ajax/trackInvoice', ['invoice_number' => '903088'])
            ->assertOk()
            ->assertJsonPath('found', true)
            ->assertJsonPath('invoice.number', '903088')
            ->assertJsonPath('invoice.status_label', 'Исполнена')
            ->assertJsonPath('invoice.from', 'Алматы')
            ->assertJsonPath('invoice.to', 'Астана')
            ->assertJsonCount(4, 'invoice.steps');
    }

    public function test_unknown_invoice_number_reports_not_found(): void
    {
        $this->postJson('/ajax/trackInvoice', ['invoice_number' => '999999'])
            ->assertOk()
            ->assertJsonPath('found', false);
    }

    public function test_empty_input_reports_not_found(): void
    {
        $this->postJson('/ajax/trackInvoice', ['invoice_number' => ''])
            ->assertOk()
            ->assertJsonPath('found', false);
    }

    public function test_response_carries_no_personal_data(): void
    {
        $this->makeInvoice();

        $body = $this->postJson('/ajax/trackInvoice', ['invoice_number' => '903088'])
            ->assertOk()
            ->getContent();

        foreach (['Иванов', 'Петров', '77010000000', '77020000000', 'Абая', 'Достык', 'Ноутбуки'] as $secret) {
            $this->assertStringNotContainsString($secret, $body);
        }
    }

    public function test_cancelled_invoice_is_reported_as_cancelled(): void
    {
        $this->makeInvoice(['status' => 4]);

        $this->postJson('/ajax/trackInvoice', ['invoice_number' => '903088'])
            ->assertOk()
            ->assertJsonPath('invoice.cancelled', true)
            ->assertJsonPath('invoice.status_label', 'Отменена')
            ->assertJsonCount(0, 'invoice.steps');
    }
}
