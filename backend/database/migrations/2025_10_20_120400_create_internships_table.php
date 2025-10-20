<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('internships', function (Blueprint $table) {
            $table->id();

           
            $table->foreignId('user_id')->constrained('users');          
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('employer_id')->nullable()->constrained('employers')->nullOnDelete(); 

            
            $table->string('agreement')->nullable();     
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('semester', ['ZS','LS']);
            $table->smallInteger('year_of_study');
            $table->text('position_description')->nullable();
            $table->boolean('is_paid')->default(false);

            
            $table->unsignedBigInteger('status_id')->nullable();

            $table->timestamps();

            $table->index(['semester','company_id','user_id','status_id']);
           
        });
    }
    public function down(): void {
        Schema::dropIfExists('internships');
    }
};
