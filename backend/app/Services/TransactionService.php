<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use App\Enums\TransactionCategory;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Ramsey\Collection\Collection;

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
}