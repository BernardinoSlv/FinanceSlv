<?php

use App\Enums\MovementTypeEnum;
use App\Models\Debt;
use Illuminate\Database\Eloquent\Builder;
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
        Schema::table('debts', function (Blueprint $table) {
            $table->boolean("to_balance")->default(0)->after("due_date");
        });

        Debt::query()->whereHas(
            "movements",
            fn(Builder $query) => $query->where("type", MovementTypeEnum::IN->value)
        )->update(["to_balance" => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn("to_balance");
        });
    }
};
