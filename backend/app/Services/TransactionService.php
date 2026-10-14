<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use App\Enums\TransactionCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\Collection;

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

        $transaction->id_user = $this->getUserId();
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
    public function deleteTransaction(int $idTransaction): Transaction
    {
        $transaction = Transaction::where('id', $idTransaction)
            ->where('id_user', $this->getUserId())
            ->firstOrFail();

        $transaction->delete();

        return $transaction;
    }
    public function calculateBalance(): float
    {
        $revenue = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->sum('amount');

        $expense = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->sum('amount');

        return $revenue - $expense;
    }
    public function totalRevenue()
    {
        $totalRevenue = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->sum('amount');

        return $totalRevenue;
    }
    public function totalExpense()
    {
        $totalExpense = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->sum('amount');

        return $totalExpense;
    }
    public function filterDate(int $month, int $year)
    {
        $start = Date::createFromDate($year, $month, 1)->startOfMonth();
        $end = Date::createFromDate($year, $month, 1)->endOfMonth();

        $filterDate = Transaction::where('id_user', $this->getUserId())
            ->whereBetween('created_at', [$start, $end])
            ->get();

        return $filterDate;
    }
    public function filterCategory(string $category)
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('category', $category)
            ->get();

        return $transaction;
    }
    public function filterExpense()
    {
        $transaction = Transaction::where('id_user')
            ->where('type', 'expense')
            ->get();

        return $transaction;
    }
    public function filterRevenue()
    {
        return Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->get();
    }
    public function expensesCategories(TransactionCategory $transactionCategory)
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('category', $transactionCategory->value)
            ->get();

        return $transaction;
    }
    public function monthlyRevenue(int $month, int $year)
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('amount');

        return $transaction;
    }
    public function monthlyExpense(int $month, int $year)
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('amount');

        return $transaction;
    }
    public function expensesByCategory(int $month, int $year): Collection
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->whereMonth('created_at', $month)
            ->whereYear('created', $year)
            ->selectRaw('category SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->values();

        return $transaction;
    }
    public function expensesByPayment(): Collection
    {
        return Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->values();
    }
    public function highestMonthlyExpense()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->whereBetween('creared_at', [$start, $end])
            ->orderBy('DESC')
            ->first();

        return $transaction;
    }
    public function highestMonthlyRevenue()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->whereBetween('creared_at', [$start, $end])
            ->orderBy('ASC')
            ->first();

        return $transaction;
    }
    public function revenuesByPayment()
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
        ->where('type', 'revenue')
        ->selectRaw('payment_method, SUM(amount) as total')
        ->groupBy('payment_method')
        ->get()
        ->values();

        return $transaction;
    }
    public function meanByMonthExpense()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $transaction = Transaction::where('id_user', $this->getUserId())
        ->where('type', 'expense')
        ->whereBetween('created_at', [$start, $end])
        ->avg('amount');

        return $transaction;
    }
    public function meanByDayExpense()
    {
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        $transaction = Transaction::where('id_user', $this->getUserId())
        ->where('type', 'expense')
        ->whereBetween('created_at', [$start, $end])
        ->avg('amount');

        return $transaction;
    }
    public function meanByYearExpense()
    {
        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->endOfYear();

        $transaction = Transaction::where('id_user', $this->getUserId())
        ->where('type', 'expense')
        ->whereBetween('created_at', [$start, $end])
        ->avg('amount');

        return $transaction;
    }
}
