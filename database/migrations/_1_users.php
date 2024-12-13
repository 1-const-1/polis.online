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
        Schema::create("users", function (Blueprint $table) {
            $table->bigInteger("id", true, true)->nullable(false);
            $table->string("login", 255)->nullable(false);
            $table->string("password", 255)->nullable(false);
            $table->timestamp("created_at")->nullable(false);
            $table->timestamp("updated_at")->nullable(false);
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("users");
    }
};
