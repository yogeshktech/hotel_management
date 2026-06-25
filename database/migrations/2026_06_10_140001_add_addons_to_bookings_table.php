<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('addons_total', 12, 2)->default(0)->after('base_price');
            $table->boolean('full_package_addons')->default(false)->after('addons_total');
            $table->json('addons_snapshot')->nullable()->after('full_package_addons');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['addons_total', 'full_package_addons', 'addons_snapshot']);
        });
    }
};
