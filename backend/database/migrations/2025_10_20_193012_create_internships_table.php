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
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable(false)->constrained("users")->onDelete("cascade");
            $table->foreignId("company_id")->nullable(false)->constrained("companies")->onDelete("cascade");
            $table->dateTimeTz("start")->nullable(false);
            $table->dateTimeTz("end")->nullable(false);
            $table->unsignedSmallInteger("year_of_study")->nullable(false);
            $table->enum("semester", ["WINTER", "SUMMER"])->nullable(false);
            $table->string("position_description")->nullable(false);
            $table->binary("agreement")->nullable(true);
            $table->binary("report")->nullable(true);
            $table->boolean("report_confirmed")->nullable(false)->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};
