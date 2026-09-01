<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Многоролевость сотрудников: один человек может быть и курьером, и кладовщиком.
 *
 * Строковая role остаётся — на неё опирается код, написанный до этой правки,
 * и выпущенные сборки мобильного приложения. Она всегда равна первой роли
 * из набора.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->json('roles')->nullable()->after('role');
        });

        // У всех существующих сотрудников набор — их единственная текущая роль.
        foreach (DB::table('staff')->select('id', 'role')->get() as $staff) {
            DB::table('staff')
                ->where('id', $staff->id)
                ->update(['roles' => json_encode([$staff->role])]);
        }
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('roles');
        });
    }
};
