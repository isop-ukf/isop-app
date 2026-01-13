<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable(false)->unique();
            $table->string("address")->nullable(false);
            $table->unsignedInteger("ico")->nullable(false)->unique();
            $table->foreignId("contact")->nullable(false)->constrained("users")->onDelete("cascade");
            $table->boolean("hiring")->nullable(false)->default(false);
            $table->boolean("verified")->nullable(false)->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
