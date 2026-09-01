<?php

namespace App\Services;

use App\Models\Goal;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;

class GoalService
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

    public function progress(int $id): float
    {
        $goal = Goal::where('id', $id)->where('id_user', $this->getUserId())->firstOrFail();
        if($goal->current_amount <= 0){
            return 0;
        }
        $percentage = min(100,($goal->current_amount / $goal->target_amount) * 100);
        return round($percentage, 2);
    }
}
