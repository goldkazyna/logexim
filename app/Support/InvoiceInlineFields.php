<?php

namespace App\Support;

use App\Models\Invoice;

/**
 * Поля карточки накладной, которые админка правит на месте (карандаш → дискетка).
 * Одно место, где описано: название, тип, обязательность, кому доступно,
 * как проверить ввод и как показать значение.
 */
class InvoiceInlineFields
{
    /** Способы оплаты — отдельные булевы колонки, правятся одной строкой галочками. */
    public const PAYMENT_METHODS = [
        'payment_sender' => 'Оплата отправителем',
        'payment_recipient' => 'Оплата получателем',
        'payment_contract' => 'Оплата по договору',
        'payment_invoice' => 'Оплата по счету',
        'payment_cash' => 'Оплата наличными',
    ];

    /**
     * field => [название, тип, обязательное, только админ, макс. длина].
     * Типы: text, textarea, int, decimal, bool, methods.
     */
    public const FIELDS = [
        'sender_name' => ['ФИО отправителя', 'text', true, false, 255],
        'sender_company' => ['Компания отправителя', 'text', false, false, 255],
        'sender_phone' => ['Телефон отправителя', 'text', true, false, 100],
        'sender_city' => ['Город отправителя', 'text', true, false, 100],
        'sender_region' => ['Область отправителя', 'text', false, false, 100],
        'sender_district' => ['Район отправителя', 'text', false, false, 100],
        'sender_address' => ['Адрес отправителя', 'text', true, false, 1000],
        'recipient_name' => ['ФИО получателя', 'text', true, false, 255],
        'recipient_company' => ['Компания получателя', 'text', false, false, 255],
        'recipient_phone' => ['Телефон получателя', 'text', true, false, 100],
        'recipient_city' => ['Город получателя', 'text', true, false, 100],
        'recipient_region' => ['Область получателя', 'text', false, false, 100],
        'recipient_district' => ['Район получателя', 'text', false, false, 100],
        'recipient_address' => ['Адрес получателя', 'text', true, false, 1000],

        'description' => ['Описание вложения', 'textarea', true, false, 2000],
        'quantity' => ['Количество мест', 'int', true, false, null],
        'weight' => ['Вес (кг)', 'decimal', true, false, null],
        'volume_weight' => ['Объёмный вес (кг)', 'decimal', false, false, null],
        'fragile' => ['Хрупкий груз', 'bool', false, false, null],

        'declared_value' => ['Объявленная ценность', 'decimal', true, true, null],
        'payment' => ['Сумма оплаты', 'decimal', true, true, null],
        'payment_methods' => ['Способ оплаты', 'methods', false, true, null],
        'special' => ['Особые инструкции', 'textarea', false, true, 2000],
    ];

    public static function label(string $field): string
    {
        return self::FIELDS[$field][0];
    }

    public static function type(string $field): string
    {
        return self::FIELDS[$field][1];
    }

    public static function adminOnly(string $field): bool
    {
        return self::FIELDS[$field][3];
    }

    /** Текущее значение поля: для methods — список выбранных колонок. */
    public static function current(Invoice $inv, string $field): mixed
    {
        if ($field === 'payment_methods') {
            return array_values(array_filter(array_keys(self::PAYMENT_METHODS), fn ($c) => (bool) $inv->{$c}));
        }

        return $inv->{$field};
    }

    /** Значение для поля ввода (строкой; для methods — список). */
    public static function inputValue(Invoice $inv, string $field): mixed
    {
        $v = self::current($inv, $field);

        return match (self::type($field)) {
            'methods' => $v,
            'bool' => $v ? '1' : '0',
            'decimal' => self::plain($v),
            default => (string) ($v ?? ''),
        };
    }

    /** Как значение показывается в карточке. */
    public static function display(Invoice $inv, string $field): string
    {
        $v = self::current($inv, $field);

        $text = match (self::type($field)) {
            'methods' => implode(', ', array_map(fn ($c) => self::PAYMENT_METHODS[$c], $v)),
            'bool' => $v ? 'Да' : 'Нет',
            'decimal' => self::plain($v),
            default => trim((string) ($v ?? '')),
        };
        if ($text !== '' && in_array($field, ['declared_value', 'payment'], true)) {
            $text .= ' KZT';
        }

        return $text !== '' ? $text : '—';
    }

    /**
     * Проверяет ввод и возвращает [колонка => значение] для сохранения
     * или строку с ошибкой.
     *
     * @return array<string, mixed>|string
     */
    public static function parse(string $field, mixed $raw): array|string
    {
        [, $type, $required, , $max] = self::FIELDS[$field];

        if ($type === 'methods') {
            $picked = is_array($raw) ? $raw : [];
            $out = [];
            foreach (array_keys(self::PAYMENT_METHODS) as $col) {
                $out[$col] = in_array($col, $picked, true);
            }

            return $out;
        }
        if ($type === 'bool') {
            return [$field => in_array((string) $raw, ['1', 'true', 'on'], true)];
        }

        $value = trim((string) $raw);
        if ($value === '') {
            if ($required) {
                return 'Поле не может быть пустым';
            }

            return [$field => $type === 'decimal' ? null : ''];
        }

        if ($type === 'int') {
            if (! ctype_digit($value) || (int) $value < 1) {
                return 'Нужно целое число от 1';
            }

            return [$field => (int) $value];
        }
        if ($type === 'decimal') {
            $value = str_replace([' ', ','], ['', '.'], $value);
            if (! is_numeric($value) || (float) $value < 0) {
                return 'Нужно число от 0';
            }

            return [$field => round((float) $value, 2)];
        }

        if ($max !== null && mb_strlen($value) > $max) {
            return 'Слишком длинное значение';
        }

        return [$field => $value];
    }

    /** "41.50" → "41.5", "20.00" → "20". */
    private static function plain(mixed $v): string
    {
        if ($v === null || $v === '') {
            return '';
        }

        return rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
    }
}
