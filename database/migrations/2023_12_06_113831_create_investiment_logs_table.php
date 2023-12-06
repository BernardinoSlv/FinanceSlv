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
        Schema::create('investiment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("investiment_id");
            $table->decimal("amount");
            $table->text("description")->nullable();
            $table->timestamps();

            $table->foreign("investiment_id")->references("id")->on("investiments");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investiment_logs');
    }
};
