<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DashBoardController extends Controller
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
    public function __construct(private TransactionService $transactionService)
    {
    }
    public function dashboard(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'month' => 'nullable|integer|between:1,12',
                'year' => 'nullable|integer|digits:4',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $transaction = new Transaction();
        $transaction->id_user = $this->getUserId();

        $balance = $this->transactionService->calculateBalance();
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        $revenue = $this->transactionService->totalRevenue();
        $expense = $this->transactionService->totalExpense();

        $expensesByCategory = $this->transactionService->expensesByCategory($month, $year);

        return response()->json([
            'summary' => [
                'balance' => $balance,
            ],
            'revenue' => $revenue,
            'expense' => $expense,
        ], 200);
    }
}
