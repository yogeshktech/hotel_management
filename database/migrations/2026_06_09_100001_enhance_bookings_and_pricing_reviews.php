<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('homestay_id')->constrained()->nullOnDelete();
            $table->foreignId('room_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            $table->enum('booking_channel', ['online', 'offline'])->default('online')->after('room_id');
            $table->enum('guest_package', ['adult', 'couple', 'family', 'child'])->default('adult')->after('booking_channel');
            $table->unsignedTinyInteger('adults_count')->default(1)->after('guest_package');
            $table->unsignedTinyInteger('children_count')->default(0)->after('adults_count');
            $table->timestamp('booked_at')->nullable()->after('children_count');
            $table->timestamp('checked_in_at')->nullable()->after('booked_at');
            $table->timestamp('checked_out_at')->nullable()->after('checked_in_at');
            $table->date('vacant_from')->nullable()->after('checked_out_at');
            $table->foreignId('created_by_staff_id')->nullable()->after('vacant_from')->constrained('staff')->nullOnDelete();
            $table->string('booking_reference')->nullable()->unique()->after('created_by_staff_id');
        });

        if (Schema::hasColumn('bookings', 'user_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        Schema::create('room_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->enum('package_type', ['adult', 'couple', 'family', 'child']);
            $table->unsignedTinyInteger('child_count')->default(0);
            $table->unsignedTinyInteger('adult_count')->default(1);
            $table->decimal('price_per_night', 12, 2);
            $table->timestamps();
            $table->unique(['room_id', 'package_type', 'child_count'], 'room_pricing_unique');
        });

        Schema::create('booking_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('homestay_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('service_rating');
            $table->unsignedTinyInteger('food_rating');
            $table->unsignedTinyInteger('overall_rating');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_reviews');
        Schema::dropIfExists('room_pricings');
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['room_id']);
            $table->dropForeign(['created_by_staff_id']);
            $table->dropColumn([
                'customer_id', 'room_id', 'booking_channel', 'guest_package',
                'adults_count', 'children_count', 'booked_at', 'checked_in_at',
                'checked_out_at', 'vacant_from', 'created_by_staff_id', 'booking_reference',
            ]);
        });
    }
};
