<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {
        $this->transactionService = new TransactionService();
    }
    //
    public function createTransaction(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id_user' => 'required|integer|exists:users,id',
                'type' => 'required|string',
                'amount' => 'required|integer',
                'category' => 'required|string',
                'description' => 'required|string',

            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $transaction = new Transaction();
        $transaction->id_user = $request->id_user;
        $transaction->type = $request->type;
        $transaction->amount = $request->amount;
        $transaction->category = $request->category;
        $transaction->description = $request->description;

        $transaction->save();

        return response()->json(['transaction' => $transaction], 201);
    }
    public function listTransaction(): JsonResponse
    {
        $transactions = Transaction::all();
        return response()->json(['transactions' => $transactions], 200);
    }
    public function listTransactionUser(int $id): JsonResponse
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'not found'], 404);
        }
        return response()->json(['transaction' => $transaction], 200);
    }
    public function updateTransaction(int $id, Request $request)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'not found'], 404);
        }
        $validator = Validator::make(
            request()->all(),
            [
                'type' => 'nullable|string',
                'amount' => 'nullable|integer',
                'category' => 'nullable|string',
                'description' => 'nullable|string',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson());
        }


        $filterData = array_filter($request->only(['type', 'amount', 'category', 'description']), function ($value) {
            return $value !== null && $value !== '';
        });

        $transaction->update($filterData);
    }
    public function deleteTransaction(int $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'not found'], 404);
        }
        $transaction->delete();
        return response()->json(['transaction' => $transaction], 200);
    }
    public function balance(int $id): JsonResponse
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'not found'], 404);
        }
        $balance = $this->transactionService->calculateBalance($id);

        return response()->json(['saldo' => $balance], 200);
    }
    public function revenue(int $id): JsonResponse
    {
        $revenue = $this->transactionService->totalRevenue($id);
        return response()->json(['revenue' => $revenue], 200);
    }
    public function expense(int $id): JsonResponse
    {
        $expense = $this->transactionService->totalExpense($id);
        return response()->json(['expense' => $expense], 200);
    }
    public function filterDate(int $id, Request $request)
    {
        $validator = Validator::make(
            request()->all(),
            [
                'month' => 'nullable|integer|between:1,12',
                'year' => 'nullable|integer|digits:4',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $month = $request->input('month', Carbon::now()->month);
        $year  = $request->input('year', Carbon::now()->year);
        $filterTransaction = $this->transactionService->filterDate($id, (int)$month, (int)$year);
        return response()->json(['transaction' => $filterTransaction], 200);
    }
    public function filterCategory(int $id, Request $request)
    {
        $validator = Validator::make(request()->all(),
        [
            'category' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson());
        }
        $category = $request->category;
        $filterTransaction = $this->transactionService->filterCategory($id, $category);

        return response()->json(['transaction' => $filterTransaction], 200);
    }
    public function filterRevenue(int $id): JsonResponse
    {
        $filterRevenue = $this->transactionService->filterRevenue($id);

        return response()->json(['transactions' => $filterRevenue], 200);
    }
    public function filterExpense(int $id): JsonResponse
    {
        $filterExpense = $this->transactionService->filterExpense($id);
        return response()->json(['transaction' => $filterExpense], 200);
    }
}
