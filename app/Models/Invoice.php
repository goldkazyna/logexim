<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    public $timestamps = false;

    public const DETAIL_STATUSES = [
        0 => 'Заявка создана',
        1 => 'Назначен курьер',
        2 => 'Курьер забрал',
        3 => 'На складе',
        4 => 'Отправлено в пункт назначения',
        5 => 'У курьера в пункте назначения',
        6 => 'Доставлено',
    ];

    public function detailStatusLabel(): string
    {
        return self::DETAIL_STATUSES[$this->detail_status] ?? '—';
    }

    protected $fillable = [
        'invoice_number', 'status', 'detail_status', 'pickup_signature',
        'user_id', 'courier_id', 'warehouse_id', 'receiving_courier_id',
        'received_at', 'shipped_at', 'date',
        'sender_name', 'sender_phone', 'sender_company',
        'sender_city', 'sender_country', 'sender_region', 'sender_district', 'sender_address',
        'recipient_name', 'recipient_phone', 'recipient_company',
        'recipient_city', 'recipient_country', 'recipient_region', 'recipient_district', 'recipient_address',
        'description', 'quantity', 'weight', 'volume_weight',
        'fragile', 'declared_value', 'payment',
        'payment_sender', 'payment_recipient', 'payment_contract', 'payment_invoice', 'payment_cash',
        'fact_date', 'plan_date', 'special', 'printed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courier()
    {
        return $this->belongsTo(Staff::class, 'courier_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Staff::class, 'warehouse_id');
    }

    public function receivingCourier()
    {
        return $this->belongsTo(Staff::class, 'receiving_courier_id');
    }

    public function events()
    {
        return $this->hasMany(InvoiceEvent::class)->orderBy('id');
    }
}
