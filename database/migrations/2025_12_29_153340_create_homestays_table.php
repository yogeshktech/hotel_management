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
        Schema::create('homestays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // owner/host
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->integer('max_guests');
            $table->integer('bedrooms');
            $table->integer('beds');
            $table->integer('bathrooms');
            $table->decimal('price_per_night', 12, 2);
            $table->decimal('cleaning_fee', 10, 2)->default(0);
            $table->decimal('service_fee_percentage', 5, 2)->default(12.5);
            $table->string('currency')->default('IDR');
            $table->json('amenities')->nullable();           // ["wifi","pool","ac","kitchen",...]
            $table->json('house_rules')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['draft', 'pending', 'active', 'inactive', 'rejected'])->default('draft');
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homestays');
    }
};
