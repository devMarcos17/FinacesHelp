<?php

namespace App\Services;

use App\Models\Investiment;
use Illuminate\Support\Facades\Auth;

class InvestimentService
{
    /**
     *
     * @return int
     */
    private function getUserId(): int
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return (int) ($user ? $user->id : Auth::id());
    }
    public function profitability(int $id): float
    {
        $investiment = Investiment::where('id', $id)->where('id_user', $this->getUserId())->first();

        if(!$investiment || $investiment->amount_invested <=0){
            return 0.0;
        }

        $profitability = (($investiment->current_amount - $investiment->amount_invested) / $investiment->amount_invested) * 100;
        return $profitability;
    }
}
