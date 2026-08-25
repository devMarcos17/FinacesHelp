<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;

class TransactionService
{
    public function calculateBalance(int $id): int
    {
        $revenue = Transaction::where('id_user', $id)->where('type', 'revenue')->sum('amount');
        $expanse = Transaction::where('id_user', $id)->where('type', 'expense')->sum('amount');

        $balance = $revenue - $expanse;
        return $balance;
    }
    public function totalRevenue(int $id): int
    {
        $revenue = Transaction::where('id_user', $id)->where('type', 'revenue')->sum('amount');
        return $revenue;
    }
    public function totalExpense(int $id): int
    {
        $expanse = Transaction::where('id_user', $id)->where('type', 'expense')->sum('amount');
        return $expanse;
    }
    public function filterDate(int $id, int $month, int $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $filter = Transaction::where('id_user', $id)
            ->whereBetween('created_at', [$startDate, $endDate])->get();

        return $filter;
    } public function filterCategory(int $id, string $category)
    {
        $categoryFilter = Transaction::where('id_user', $id)->where('category', $category)->get();
        return $categoryFilter;

    }
    public function filterRevenue(int $id)
    {
        $revenue = Transaction::where('id_user', $id)->where('type', 'revenue')->get();
        return $revenue;
    }
    public function filterExpense(int $id)
    {
        $expense = Transaction::where('id_user', $id)->where('type','expense')->get();
        return $expense;
    }

}
