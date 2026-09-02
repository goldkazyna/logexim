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

    /**
     * Клиентские названия этапов для публичного отслеживания на сайте.
     * Отличаются от операционных DETAIL_STATUSES (их видят курьер, кладовщик
     * и админка) — здесь язык для получателя груза.
     */
    public const PUBLIC_DETAIL_STATUSES = [
        0 => 'Заявка создана',
        1 => 'Выведен на доставку',
        2 => 'Груз находится у курьера на маршруте',
        3 => 'Прибыл в терминал RKC',
        4 => 'Груз прибыл в город назначения',
        5 => 'Груз находится у курьера на маршруте',
        6 => 'Доставлен',
    ];

    /** Административный статус — то, что видно в колонке «Статус» в админке. */
    public const STATUSES = [
        0 => 'Заявка создана',
        1 => 'Принята в работу',
        2 => 'Отправлено',
        3 => 'Исполнена',
        4 => 'Отменена',
    ];

    /** Цвета бейджей — те же, что в админке, чтобы статус читался одинаково. */
    public const STATUS_COLORS = [
        0 => '#00056d',
        1 => '#ffcc00',
        2 => '#00aaff',
        3 => '#28a745',
        4 => '#dc3545',
    ];

    private const STATUS_CANCELLED = 4;

    /**
     * Данные для публичного отслеживания накладной по номеру.
     *
     * Основа — административный статус: detail_status ведётся только для
     * накладных, прошедших через мобильное приложение, у остальных он равен
     * нулю даже когда накладная давно исполнена. Детальная цепочка поэтому
     * показывается только когда в ней есть смысл.
     *
     * Персональные данные (ФИО, телефоны, адреса, описание груза) сюда не
     * попадают: номера последовательные, и перебор не должен превращаться
     * в выгрузку базы контрагентов.
     */
    public function publicTracking(): array
    {
        $status = (int) $this->status;
        $detail = (int) $this->detail_status;
        $cancelled = $status === self::STATUS_CANCELLED;
        $known = array_key_exists($status, self::STATUSES);

        return [
            'number' => (string) $this->invoice_number,
            'status' => $status,
            'status_label' => self::STATUSES[$status] ?? '—',
            'status_color' => self::STATUS_COLORS[$status] ?? '#999999',
            'cancelled' => $cancelled,
            'from' => (string) $this->sender_city,
            'to' => (string) $this->recipient_city,
            'quantity' => (string) ($this->quantity ?? ''),
            'weight' => (string) ($this->weight ?? ''),
            'created_at' => $this->formatDate($this->created_at ?? $this->date),
            'plan_date' => $this->formatDate($this->plan_date),
            'fact_date' => $this->formatDate($this->fact_date),
            'delivered_at' => $this->formatDate($this->delivered_at),
            'insured' => (float) ($this->declared_value ?? 0) > 0,
            'sender' => [
                'name' => $this->partyName($this->sender_company, $this->sender_name),
                'city' => (string) $this->sender_city,
            ],
            'recipient' => [
                'name' => $this->partyName($this->recipient_company, $this->recipient_name),
                'city' => (string) $this->recipient_city,
            ],
            // Время достижения каждого этапа — из журнала, где он есть.
            'stage_times' => $this->stageTimes(),
            'steps' => $cancelled || ! $known
                ? []
                : $this->buildSteps(array_slice(self::STATUSES, 0, 4, true), $status),
            'detail_steps' => $cancelled || $detail <= 0
                ? []
                : $this->buildSteps(self::PUBLIC_DETAIL_STATUSES, $detail),
        ];
    }

    /**
     * Раскрашивает цепочку этапов: пройденные — done, текущий — current,
     * будущие — pending. На последнем этапе цепочка закрыта целиком.
     *
     * @param  array<int, string>  $titles
     * @return list<array{title: string, state: string}>
     */
    private function buildSteps(array $titles, int $current): array
    {
        $last = array_key_last($titles);
        $steps = [];

        foreach ($titles as $index => $title) {
            $state = match (true) {
                $current >= $last => 'done',
                $index < $current => 'done',
                $index === $current => 'current',
                default => 'pending',
            };
            $steps[] = ['title' => $title, 'state' => $state];
        }

        return $steps;
    }

    private function partyName(?string $company, ?string $name): string
    {
        $company = trim((string) $company);
        return $company !== '' ? $company : trim((string) $name);
    }

    /**
     * Время достижения этапов детальной цепочки — из журнала накладной.
     * Ключ — номер этапа (to_detail_status), значение — «дд.мм.гггг чч:мм».
     * Работает только если события подгружены (with('events')).
     *
     * @return array<int, string>
     */
    private function stageTimes(): array
    {
        $times = [];

        $created = $this->created_at ?? $this->date;
        if (!empty($created)) {
            $times[0] = $this->formatDateTime($created);
        }

        if ($this->relationLoaded('events')) {
            foreach ($this->events as $event) {
                $stage = $event->to_detail_status;
                if ($stage !== null && !empty($event->created_at)) {
                    $times[(int) $stage] = $this->formatDateTime($event->created_at);
                }
            }
        }

        return $times;
    }

    private function formatDateTime($value): string
    {
        try {
            return \Carbon\Carbon::parse($value)->format('d.m.Y H:i');
        } catch (\Throwable) {
            return '';
        }
    }

    private function formatDate($value): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d.m.Y');
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * Накладные, доступные сотруднику в панели управления.
     *
     * Администратор и диспетчер видят все; курьер и агент — только свои,
     * причём и те, что забирают у отправителя, и те, что принимают в пункте
     * назначения. У многоролевого сотрудника переключателя в панели нет,
     * поэтому правило такое: диспетчерская роль открывает всё, иначе полевая
     * роль сужает до своих. Добавление роли никогда не расширяет доступ молча.
     *
     * @param  array<int, string>|string|null  $roles
     */
    public function scopeVisibleToStaff($query, array|string|null $roles, ?int $staffId)
    {
        $roles = array_filter((array) $roles);

        if (in_array('dispatcher', $roles, true)) {
            return $query;
        }

        $isFieldStaff = array_filter($roles, fn ($role) => Staff::isCourierRoleName($role)) !== [];
        if (!$isFieldStaff) {
            return $query;
        }

        return $query->where(function ($q) use ($staffId) {
            $q->where('courier_id', $staffId)
              ->orWhere('receiving_courier_id', $staffId);
        });
    }

    protected $fillable = [
        'invoice_number', 'status', 'detail_status', 'pickup_signature', 'delivery_signature',
        'user_id', 'courier_id', 'warehouse_id', 'receiving_courier_id',
        'received_at', 'shipped_at', 'delivered_at', 'date',
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
