<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Без склада теперь решает только галочка same_city. Накладным, которые
 * по старому правилу (совпадение городов или city_links) уже шли без склада,
 * ставим галочку, чтобы их путь не поменялся на полдороге.
 */
return new class extends Migration
{
    public function up(): void
    {
        $norm = fn ($v) => mb_strtolower(trim((string) $v));

        $ids = [];
        foreach (DB::table((new \App\Models\CityDelivery)->getTable())->get(['id', 'title']) as $c) {
            $ids[$norm($c->title)] = $c->id;
        }
        $links = [];
        foreach (DB::table('city_links')->get() as $l) {
            $links[$l->city_id . ':' . $l->linked_city_id] = true;
        }

        DB::table('invoices')->where('same_city', 0)
            ->select('id', 'sender_city', 'recipient_city')
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($norm, $ids, $links) {
                $local = [];
                foreach ($rows as $r) {
                    $a = $norm($r->sender_city);
                    $b = $norm($r->recipient_city);
                    if ($a === '') {
                        continue;
                    }
                    if ($a === $b || isset($links[($ids[$a] ?? 'x') . ':' . ($ids[$b] ?? 'y')])) {
                        $local[] = $r->id;
                    }
                }
                if ($local) {
                    DB::table('invoices')->whereIn('id', $local)->update(['same_city' => 1]);
                }
            });
    }

    public function down(): void
    {
    }
};
