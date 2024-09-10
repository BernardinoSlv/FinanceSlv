<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->date('closed_date')
                ->nullable()
                ->default(new Expression('(CURRENT_DATE)'))
                ->after('effetive_at');
            $table->decimal('fees_amount')->default(0)->after('closed_date');
            $table->date("effetive_at")->nullable()->change();
            $table->renameColumn('effetive_at', 'effetive_date');
        });
        DB::table("movements")->update([
            "closed_date" => DB::raw("created_at")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropColumn(['closed_date', 'fees_amount']);
            $table->renameColumn("effetive_date", "effetive_at");
        });
    }
};
