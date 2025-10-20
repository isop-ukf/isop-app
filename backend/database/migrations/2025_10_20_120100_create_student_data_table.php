<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('address')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('study_field')->nullable(); 
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('student_data');
    }
};
