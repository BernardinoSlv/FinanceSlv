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
        Schema::table('leaves', function (Blueprint $table) {
            $table->renameColumn("leaveble_type", "leaveable_type");
            $table->renameColumn("leaveble_id", "leaveable_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->renameColumn("leaveable_type", "leaveble_type");
            $table->renameColumn("leaveable_id", "leaveble_id");
        });
    }
};
