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
}
