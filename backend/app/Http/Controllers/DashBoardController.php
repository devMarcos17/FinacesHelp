<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashBoardController extends Controller
{
    public function __construct(private TransactionService $transactionService)
    {
        $this->transactionService = new TransactionService();
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


        $balance = $this->transactionService->calculateBalance();
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        $revenue = $this->transactionService->monthlyRevenue($month, $year);
        $expense = $this->transactionService->monthlyExpense($month, $year);

        $expensesByCategory = $this->transactionService->expensesByCategory($month, $year);

        return response()->json([
            'summary' => [
                'balance' => $balance,
            ],
            'revenue' => $revenue,
            'expense' => $expense,
            'expensesByCategory' => $expensesByCategory,
        ], 200);
    }
}
