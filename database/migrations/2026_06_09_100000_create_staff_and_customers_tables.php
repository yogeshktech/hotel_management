<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->string('role')->nullable();
            $table->string('department')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        if (Schema::hasTable('users')) {
            $users = DB::table('users')->get();
            foreach ($users as $user) {
                DB::table('staff')->insert([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                    'password' => $user->password,
                    'avatar' => $user->avatar ?? null,
                    'role' => $user->role ?? null,
                    'is_active' => $user->is_active ?? true,
                    'email_verified_at' => $user->email_verified_at ?? null,
                    'last_login_at' => $user->last_login_at ?? null,
                    'remember_token' => $user->remember_token ?? null,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }

            DB::table('model_has_roles')
                ->where('model_type', 'App\Models\User')
                ->update(['model_type' => 'App\Models\Staff']);

            DB::table('model_has_permissions')
                ->where('model_type', 'App\Models\User')
                ->update(['model_type' => 'App\Models\Staff']);
        }

        Schema::table('homestays', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->after('location_id')->constrained('staff')->nullOnDelete();
        });
        DB::table('homestays')->update(['staff_id' => DB::raw('user_id')]);

        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->after('id')->constrained('staff')->cascadeOnDelete();
        });
        DB::table('vendor_profiles')->update(['staff_id' => DB::raw('user_id')]);

        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        if (Schema::hasColumn('vendor_profiles', 'approved_by')) {
            Schema::table('vendor_profiles', function (Blueprint $table) {
                $table->dropForeign(['approved_by']);
            });
            Schema::table('vendor_profiles', function (Blueprint $table) {
                $table->foreign('approved_by')->references('id')->on('staff')->nullOnDelete();
            });
        }

        Schema::table('homestays', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('homestays', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users');
        });
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users');
        });
        Schema::dropIfExists('customers');
        Schema::dropIfExists('staff');
    }
};
