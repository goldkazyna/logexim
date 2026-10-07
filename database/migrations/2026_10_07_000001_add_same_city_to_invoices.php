<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Галочка «Получатель в этом же городе» при создании накладной —
 * признак доставки по городу без склада.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('invoices', 'same_city')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->boolean('same_city')->default(false);
            });
        }
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('same_city');
        });
    }
};
