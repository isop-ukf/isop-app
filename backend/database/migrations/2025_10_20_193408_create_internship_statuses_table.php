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
        Schema::create('internship_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId("internship_id")->nullable(false)->constrained("internships")->onDelete("cascade");
            $table->enum("status", ["SUBMITTED", "CONFIRMED", "DENIED", "DEFENDED", "NOT_DEFENDED"])->nullable(false)->default("SUBMITTED");
            $table->dateTimeTz("changed")->nullable(false);
            $table->string("note")->nullable(true)->default(null);
            $table->foreignId("modified_by")->nullable(false)->constrained("users")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_statuses');
    }
};
