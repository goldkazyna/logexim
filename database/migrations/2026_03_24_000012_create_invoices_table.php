<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_number');
            $table->integer('status')->default(0);
            $table->integer('user_id');
            $table->date('date');
            $table->string('sender_name');
            $table->string('sender_phone', 20);
            $table->string('sender_company')->nullable();
            $table->string('sender_city', 100);
            $table->string('sender_country', 100);
            $table->string('sender_region', 100)->nullable();
            $table->string('sender_district', 100)->nullable();
            $table->text('sender_address');
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);
            $table->string('recipient_company')->nullable();
            $table->string('recipient_city', 100);
            $table->string('recipient_country', 100);
            $table->string('recipient_region', 100)->nullable();
            $table->string('recipient_district', 100)->nullable();
            $table->text('recipient_address');
            $table->text('description');
            $table->integer('quantity');
            $table->decimal('weight', 10, 2);
            $table->decimal('volume_weight', 10, 2)->nullable();
            $table->boolean('fragile')->default(false);
            $table->decimal('declared_value', 15, 2);
            $table->decimal('payment', 15, 2)->default(0);
            $table->boolean('payment_sender')->default(false);
            $table->boolean('payment_recipient')->default(false);
            $table->boolean('payment_contract')->default(false);
            $table->boolean('payment_invoice')->default(false);
            $table->boolean('payment_cash')->default(false);
            $table->date('fact_date')->nullable();
            $table->date('plan_date')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->text('special')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
