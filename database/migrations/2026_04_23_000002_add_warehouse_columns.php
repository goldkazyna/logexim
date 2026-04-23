<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('warehouse_location')->nullable()->after('note');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('courier_id');
            $table->timestamp('received_at')->nullable()->after('pickup_signature');
            $table->timestamp('shipped_at')->nullable()->after('received_at');
            $table->index('warehouse_id');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['warehouse_id']);
            $table->dropColumn(['warehouse_id', 'received_at', 'shipped_at']);
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('warehouse_location');
        });
    }
};
