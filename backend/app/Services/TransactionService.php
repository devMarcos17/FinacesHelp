<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use App\Enums\TransactionCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class TransactionService
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
    public function createTransaction(array $data): Transaction
    {
        $transaction = new Transaction();
        $transaction->id_user = $data['id_user'];
        $transaction->type = $data['type'];
        $transaction->amount = $data['amount'];
        $transaction->category = $data['category'];
        $transaction->description = $data['description'];
        $transaction->payment_method = $data['payment_method'];

        $transaction->save();

        return $transaction;
    }
    public function updateTransaction(array $data, int $idTransaction): Transaction
    {
        $transaction = Transaction::where('id', $idTransaction)
            ->where('id_user', $this->getUserId())
            ->firstOrFail();

        $transaction->update($data);

        return $transaction;
    }
    public function deleteTransaction(): Transaction
    {
        $transaction = Transaction::where('id', $this->getUserId())->where('id_user', $this->getUserId())
        ->firstOrFail();

        $transaction->delete();
        
        return $transaction;
    }
    public function calculateBalance(): int
    {
        $idUser = $this->getUserId();
        $revenue = Transaction::where('id_user', $idUser)->where('type', 'revenue')->sum('amount');
        $expense = Transaction::where('id_user', $idUser)->where('type', 'expense')->sum('amount');
        return $revenue - $expense;
    }
    public function totalRevenue(): int
    {
        $revenue = Transaction::where('id_user', $this->getUserId())->where('type', 'revenue')->sum('amount');
        return $revenue;
    }
    public function totalExpense(): int
    {
        $expanse = Transaction::where('id_user', $this->getUserId())->where('type', 'expense')->sum('amount');
        return $expanse;
    }
    public function filterDate(int $month, int $year)
    {
        $start = Date::createFromDate($year, $month, 1)->startOfMonth();
        $end = Date::createFromDate($year, $month, 1)->endOfMonth();

        $filter = Transaction::where('id_user', $this->getUserId())->whereBetween('created_at', [$start, $end])->get();
        return $filter;
    }
    public function filterCategory(string $category)
    {
        $filterCategory = Transaction::where('id_user', $this->getUserId())->where('category', $category)->get();
        return $filterCategory;
    }
    public function filterRevenue()
    {
        $revenue = Transaction::where('id_user', $this->getUserId())->where('type', 'revenue')->get();
        return $revenue;
    }

    public function filterExpense()
    {
        $expense = Transaction::where('id_user', $this->getUserId())->where('type', 'expense')->get();
        return $expense;
    }

    public function expensesCategories(TransactionCategory $transactionCategory)
    {
        $transaction = Transaction::where('id_user', $this->getUserId())->where('category', $transactionCategory->value)->get();
        return $transaction;
    }

    public function monthlyRevenue(int $month, int $year): int
    {
        $transaction = Transaction::where('id_user', $this->getUserId())->where('type', 'revenue')->whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('amount');
        return $transaction;
    }

    public function monthlyExpense(int $month, int $year): int
    {
        $transaction = Transaction::where('id_user', $this->getUserId())->where('type', 'expense')->whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('amount');
        return $transaction;
    }

    public function expensesByCategory(int $month, int $year)
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        return $transaction;
    }
    public function expensesByPayment()
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        return $transaction;
    }
    public function revenueByPayment()
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        return $transaction;
    }
    public function highestMonthlyExpense()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('amount', 'desc')
            ->first();

        return $transaction;
    }
    public function highestMonthlyRevenue()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('amount', 'desc')
            ->first();

        return $transaction;
    }
}
