<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('event', 64); // pickup, warehouse_receive, warehouse_ship, delivery, courier_assigned, status_changed, detail_changed
            $table->tinyInteger('from_detail_status')->nullable();
            $table->tinyInteger('to_detail_status')->nullable();
            $table->string('actor_type', 20)->nullable(); // admin, staff
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_role', 20)->nullable(); // admin, dispatcher, courier, warehouse
            $table->string('actor_name')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('invoice_id');
            $table->index('event');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_events');
    }
};
