<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('internship_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained('internships')->cascadeOnDelete();
            $table->enum('status', ['SUBMITTED','CONFIRMED','DENIED','DEFENDED','NOT_DEFENDED']);
            $table->timestamp('changed')->useCurrent();
            $table->foreignId('modified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->string('source', 30)->default('UI'); 
            $table->timestamps();

            $table->index(['internship_id','status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('internship_statuses');
    }
};
