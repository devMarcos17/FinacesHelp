<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use App\Enums\TransactionCategory;
use Illuminate\Support\Facades\Auth;

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

    public function calculateBalance(): int
    {
        $userId = $this->getUserId();

        $revenue = Transaction::where('id_user', $userId)->where('type', 'revenue')->sum('amount');
        $expanse = Transaction::where('id_user', $userId)->where('type', 'expense')->sum('amount');

        $balance = $revenue - $expanse;
        return $balance;
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
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $filter = Transaction::where('id_user', $this->getUserId())
            ->whereBetween('created_at', [$startDate, $endDate])->get();

        return $filter;
    }

    public function filterCategory(string $category)
    {
        $categoryFilter = Transaction::where('id_user', $this->getUserId())->where('category', $category)->get();
        return $categoryFilter;
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
}