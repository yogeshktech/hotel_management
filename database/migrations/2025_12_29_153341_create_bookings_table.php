<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homestay_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // guest
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('guests');
            $table->decimal('total_price', 12, 2);
            $table->decimal('base_price', 12, 2);
            $table->decimal('cleaning_fee', 10, 2);
            $table->decimal('service_fee', 10, 2);
            $table->string('currency')->default('IDR');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('status')->default('pending');         // pending, confirmed, cancelled, completed
            $table->text('guest_notes')->nullable();
            $table->string('promo_code')->nullable();
            $table->decimal('promo_discount', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
