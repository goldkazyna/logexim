<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'invoice_id',
        'event',
        'from_detail_status',
        'to_detail_status',
        'actor_type',
        'actor_id',
        'actor_role',
        'actor_name',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public const EVENT_LABELS = [
        'courier_assigned' => 'Назначен курьер',
        'pickup' => 'Курьер забрал груз',
        'warehouse_receive' => 'Принято на склад',
        'warehouse_ship' => 'Отправлено со склада',
        'delivery' => 'Доставлено получателю',
        'status_changed' => 'Изменён основной статус',
        'detail_changed' => 'Изменён этап доставки',
    ];

    public function label(): string
    {
        return self::EVENT_LABELS[$this->event] ?? $this->event;
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
