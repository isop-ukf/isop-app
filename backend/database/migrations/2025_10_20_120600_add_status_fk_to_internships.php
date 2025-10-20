<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('internships', function (Blueprint $table) {
            $table->foreign('status_id')
                  ->references('id')->on('internship_statuses')
                  ->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('internships', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
        });
    }
};
