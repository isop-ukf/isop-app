<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->after('name');
            $table->string('last_name')->nullable(false)->after('first_name');
            $table->string('phone')->nullable(false)->unique()->after('email');
            $table->enum("role", ["STUDENT", "EMPLOYER", "ADMIN"])->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);

            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'role',
            ]);
        });
    }
};
