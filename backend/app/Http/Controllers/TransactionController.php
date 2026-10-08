<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Enum;
use App\Enums\TransactionCategory;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {
        $this->transactionService = new TransactionService();
    }
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

    public function createTransaction(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'type' => 'required|string',
                'amount' => 'required|integer',
                'category' => 'required|string',
                'description' => 'required|string',
                'payment_method' => 'required|string|in:pix,credit,debit,cash,bank_transfer,other'

            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = array_merge(
            request()->only([
                'type',
                'amount',
                'category',
                'description',
                'payment_method',
            ]),
            ['id_user' => $this->getUserId()]
        );

        $transaction = $this->transactionService->createTransaction($data);

        return response()->json(['transaction' => $transaction], 201);
    }
    public function listTransaction(): JsonResponse
    {
        $transactions = Transaction::all();
        return response()->json(['transactions' => $transactions], 200);
    }
    public function listTransactionById(Request $request): JsonResponse
    {
        $transaction = Transaction::find($request->id);
        if (!$transaction) {
            return response()->json(['message' => 'not found'], 404);
        }

        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        return response()->json(['transaction' => $transaction], 200);
    }
    public function updateTransaction(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'type' => 'nullable|string',
                'amount' => 'nullable|integer',
                'category' => 'nullable|string',
                'description' => 'nullable|string',
                'payment_method' => 'nullable|string|in:pix,credit,debit,cash,bank_transfer,other',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $filterData = array_filter(
            $request->only([
                'type',
                'amount',
                'category',
                'description',
                'payment_method'
            ]),
            function ($value) {
                return $value !== null && $value !== '';
            }
        );
        $transaction = $this->transactionService->updateTransaction(
            $filterData,
           (int)$request->id
        );

        return response()->json(['transaction' => $transaction], 200);
    }
    public function deleteTransaction(): JsonResponse
    {
        $transaction = $this->transactionService->deleteTransaction();

        return response()->json(['transaction' => $transaction], 200);
    }
    public function balance(): JsonResponse
    {
        $balance = $this->transactionService->calculateBalance();
        return response()->json(['balance' => $balance], 200);
    }
    public function totalRevenue(): JsonResponse
    {
        $revenueTotal = $this->transactionService->totalRevenue();
        return response()->json(['total_revenue' => $revenueTotal], 200);
    }
    public function totalExpense(): JsonResponse
    {
        $expense = $this->transactionService->totalExpense();
        return response()->json(['total_expense' => $expense], 200);
    }
    public function filterDate(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'month' => 'required|integer|between:1,12',
                'year'  => 'nullable|integer|digits:4',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $filter = $this->transactionService->filterDate($month, $year);

        return response()->json(['transactions' => $filter], 200);
    }
    public function filterCategory(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'category' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $category = $request->category;
        $filterTransaction = $this->transactionService->filterCategory($category);

        return response()->json(['transaction' => $filterTransaction], 200);
    }
    public function filterRevenue(): JsonResponse
    {
        $filterRevenue = $this->transactionService->filterRevenue();

        return response()->json(['transactions' => $filterRevenue], 200);
    }
    public function filterExpense(): JsonResponse
    {
        $filterExpense = $this->transactionService->filterExpense();
        return response()->json(['transactions' => $filterExpense], 200);
    }
    public function expensesCategories(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'category' => ['required', new Enum(TransactionCategory::class)],
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $categoryEnum = TransactionCategory::from($request->category);
        $expenseCategory = $this->transactionService->expensesCategories($categoryEnum);

        return response()->json(['transactions' => $expenseCategory], 200);
    }
    public function monthlyRevenue(Request $request)
    {
        $validator = Validator::make(
            request()->all(),
            [
                'month' => 'required|integer|between:1,12',
                'year' => 'nullable|integer|digits:4',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $month = $request->input('month', Carbon::now()->month);
        $year  = $request->input('year', Carbon::now()->year);

        $revenue = $this->transactionService->monthlyRevenue($month, $year);
        return response()->json(['transactions' => $revenue], 200);
    }
    public function monthlyExpense(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'month' => 'required|integer|between:1,12',
                'year' => 'nullable|integer|digits:4',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $month = $request->input('month', Carbon::now()->month);
        $year  = $request->input('year', Carbon::now()->year);

        $expense = $this->transactionService->monthlyExpense($month, $year);

        return response()->json(['transactions' => $expense], 200);
    }
    public function expensesByCategory(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'month' => 'nullable|integer|between:1,12',
                'year' => 'nullable|integer|digits:4',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $month = $request->input('month', Carbon::now()->month);
        $year  = $request->input('year', Carbon::now()->year);

        $transaction = $this->transactionService->expensesByCategory($month, $year);
        return response()->json(['transaction' => $transaction], 200);
    }
    public function expensesByPayment(): JsonResponse
    {

        $transaction = $this->transactionService->expensesByPayment()->values();

        return response()->json(['transaction' => $transaction], 200);
    }
    public function revenueByPayment(): JsonResponse
    {
        $transaction = $this->transactionService->revenueByPayment()->values();
        return response()->json(['transaction' => $transaction], 200);
    }
    public function highestMonthlyExpense(): JsonResponse
    {
        $transaction = $this->transactionService->highestMonthlyExpense();
        return response()->json(['transaction' => $transaction], 200);
    }
    public function highestMonthlyRevenue(): JsonResponse
    {
        $transaction = $this->transactionService->highestMonthlyRevenue();
        return response()->json(['transaction' => $transaction], 200);
    }
}
