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
        Schema::table('needs', function (Blueprint $table) {
            $table->unsignedBigInteger("identifier_id")->nullable()->after("user_id");

            $table->foreign("identifier_id")->references("id")->on("identifiers");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('needs', function (Blueprint $table) {
            $table->dropForeign(["identifier_id"]);
            $table->dropColumn("identifier_id");
        });
    }
};
