<?php

namespace App\Services;

use App\Models\Investiment;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

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
    public function createInvestiment(array $data, float $currentAmount): Investiment
    {
        $investiment = new Investiment();
        $investiment->id_user = $this->getUserId();
        $investiment->title = $data['title'];
        $investiment->amount_invested = $data['amount_invested'];
        $investiment->current_amount = $currentAmount;

        $investiment->save();

        return $investiment;
    }
    public function updateInvestiment(int $idInvestiment, array $data): Investiment
    {

        $investiment = Investiment::where('id', $idInvestiment)
            ->where('id_user', $this->getUserId())
            ->firstOrFail();

        $investiment->update($data);

        return $investiment;
    }
    public function deleteInvestiment(int $idInvestiment): Investiment
    {
        $investiment = Investiment::where('id', $idInvestiment)
            ->where('id_user', $this->getUserId())
            ->firstOrFail();

        $investiment->delete();

        return $investiment;
    }
    public function listInvestiment(): Collection
    {
        $investiment = Investiment::where('id_user', $this->getUserId())
            ->get();

        return $investiment;
    }
    public function profitability(int $idInvestiment)
    {
        $investiment = Investiment::where('id', $idInvestiment)
            ->where('id_user', $this->getUserId())
            ->first();

        if (!$investiment || $investiment->amount_invested <= 0) {
            return [
                'investiment_id' => $investiment->id,
                'profitability' => 0.0
            ];
        }
        $profitability = ((
            $investiment->current_amount - $investiment->amount_invested) /
            $investiment->amount_invested) * 100;
        return [
            'investiment_id' => $investiment->id,
            'profitability' => round($profitability, 2),
        ];
    }
    public function withDraw(int $idInvestiment, float $devolutionAmount)
    {
        $investiment = Investiment::where('id', $idInvestiment)
            ->where('id_user', $this->getUserId())
            ->firstOrFail();

        if ($devolutionAmount > $investiment->current_amount) {
            return false;
        }

        $investiment->current_amount -= $devolutionAmount;
        $investiment->save();

        $transaction  = Transaction::create([
            'id_user' => $this->getUserId(),
            'type' => 'revenue',
            'amount' => $devolutionAmount,
            'category' => 'other',
            'description' => 'Investiment',
        ]);

        return [
            'investiment' => $investiment,
            'transaction' => $transaction
        ];
    }
}
