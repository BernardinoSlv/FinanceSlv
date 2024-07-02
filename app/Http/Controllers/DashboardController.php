<?php

namespace App\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User */
        $user = auth()->user();

        $totalEntry = (float) $user->movements()
            ->where("type", MovementTypeEnum::IN->value)
            ->sum('amount');
        $totalExit = (float) $user->movements()
            ->where("type", MovementTypeEnum::OUT->value)
            ->sum('amount');
        $totalDebts = (float) $user->debts()->sum("debts.amount") - $user->movements()->where("movementable_type", Debt::class)
            ->sum("movements.amount");

        // graficos
        $dataChart1 = $user->movements()
            ->select(DB::raw('
                date_format(movements.created_at, "%m/%Y") as period,
                sum(movements.amount) as amount'))
            ->groupBy(DB::raw('date_format(movements.created_at, "%m/%Y")'))
            ->orderBy(DB::raw('date_format(movements.created_at, "%m/%Y")'))
            ->get()
            ->map(
                fn ($movementGroup) => ["period" => $movementGroup->period, "amount" => $movementGroup->amount]
            )
            ->toArray();

        $topIdendifiersEntry = $user->identifiers()
            ->select("identifiers.*")
            ->withSum(
                ["movements" => function (Builder $query) {
                    $query->where("movements.type", MovementTypeEnum::IN->value);
                }],
                "amount"
            )
            ->orderBy("movements_sum_amount", "desc")
            ->limit(5)
            ->get();
        $topIdendifiersExit = $user->identifiers()
            ->select("identifiers.*")
            ->withSum(
                ["movements" => function (Builder $query) {
                    $query->where("movements.type", MovementTypeEnum::OUT->value);
                }],
                "amount"
            )
            ->orderBy("movements_sum_amount", "desc")
            ->limit(5)
            ->get();
        $topDebts = $user->debts()
        ->select("debts.*")
                ->withSum(["movements" => function(Builder $query) {
                    $query->where("movements.type", MovementTypeEnum::OUT->value)
                }], )
            ->


        return view("dashboard.index", compact(
            "totalEntry",
            "totalExit",
            "totalDebts",
            "dataChart1",
            "topIdendifiersEntry",
            "topIdendifiersExit",
        ));
    }
}
