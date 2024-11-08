<?php

namespace App\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User */
        $user = auth()->user();

        $totalEntry = (float) $user->movements()
            ->where('type', MovementTypeEnum::IN->value)
            ->sum("amount");
        $totalExit = (float) $user->movements()
            ->where('type', MovementTypeEnum::OUT->value)
            ->whereNotNull("closed_date")
            ->sum(DB::raw('amount + fees_amount'));
        $totalDebts = (float) $user->debts()->sum('debts.amount') - $user->movements()->where([
            'movementable_type' => Debt::class,
            "type" => MovementTypeEnum::OUT->value
        ])
            ->sum('movements.amount');
        $totalInOpen = (float) $user->movements()->whereNull("closed_date")->sum(DB::raw("amount + fees_amount"));

        // graficos
        $dataChart1 = $user->movements()
            ->select(DB::raw('
            date_format(closed_date, "%y-%m") as period,
            sum(
                case
                    when movements.type = "in" then amount
                    else 0
                end
            ) as entry_amount,
            sum(
                case
                    when movements.type = "out" then amount + fees_amount
                    else 0
                end
            ) as exit_amount'))
            ->groupBy("period")
            ->orderBy("period")
            ->whereNotNull("closed_date")
            ->get()
            ->toArray();

        $topIdendifiersEntry = $user->identifiers()
            ->select('identifiers.*')
            ->withSum(
                ['movements' => function (Builder $query) {
                    $query->where('movements.type', MovementTypeEnum::IN->value);
                }],
                'amount'
            )
            ->orderBy('movements_sum_amount', 'desc')
            ->limit(5)
            ->get();
        $topIdendifiersExit = $user->identifiers()
            ->select('identifiers.*')
            ->withSum(
                ['movements' => function (Builder $query) {
                    $query->where('movements.type', MovementTypeEnum::OUT->value);
                }],
                'amount'
            )
            ->withSum(
                ['movements' => function (Builder $query) {
                    $query->where('movements.type', MovementTypeEnum::OUT->value);
                }],
                'fees_amount'
            )
            ->orderBy(DB::raw('movements_sum_amount + movements_sum_fees_amount'), 'desc')
            ->limit(5)
            ->get();
        $topDebts = $user->debts()
            ->select('debts.*')
            ->withSum(['movements' => function (Builder $query) {
                $query->where('movements.type', MovementTypeEnum::OUT->value);
            }], 'amount')
            ->orderBy(DB::raw('debts.amount - (case
                when movements_sum_amount is null then 0
                else movements_sum_amount
                    end)'), 'DESC')
            ->orderBy('debts.id', 'ASC')
            ->with('identifier')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalInOpen',
            'totalEntry',
            'totalExit',
            'totalDebts',
            'dataChart1',
            'topIdendifiersEntry',
            'topIdendifiersExit',
            'topDebts'
        ));
    }
}
