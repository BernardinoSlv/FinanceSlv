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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('identifier_id')->nullable()->references('id')->on('identifiers');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->morphs('movementable');
            $table->enum('type', ['in', 'out']);
            $table->decimal('amount');
            $table->date('effetive_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
