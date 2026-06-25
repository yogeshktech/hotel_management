<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 50);
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('charge_type', ['per_night', 'per_stay', 'per_guest_per_night'])->default('per_night');
            $table->boolean('is_included_in_package')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['room_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_addons');
    }
};
