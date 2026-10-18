<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use App\Enums\TransactionCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use DivisionByZeroError;
use GuzzleHttp\Psr7\UploadedFile;
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
    public function totalRevenue(): float
    {
        $totalRevenue = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->sum('amount');

        return $totalRevenue;
    }
    public function totalExpense(): float
    {
        $totalExpense = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->sum('amount');

        return $totalExpense;
    }
    public function filterDate(int $month, int $year): Collection
    {
        $start = Date::createFromDate($year, $month, 1)->startOfMonth();
        $end = Date::createFromDate($year, $month, 1)->endOfMonth();

        $filterDate = Transaction::where('id_user', $this->getUserId())
            ->whereBetween('created_at', [$start, $end])
            ->get();

        return $filterDate;
    }
    public function filterCategory(string $category): Collection
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('category', $category)
            ->get();

        return $transaction;
    }
    public function filterExpense(): Collection
    {
        $transaction = Transaction::where('id_user')
            ->where('type', 'expense')
            ->get();

        return $transaction;
    }
    public function filterRevenue(): Collection
    {
        return Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->get();
    }
    public function expensesCategories(TransactionCategory $transactionCategory): Collection
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('category', $transactionCategory->value)
            ->get();

        return $transaction;
    }
    public function monthlyRevenue(int $month, int $year): float
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('amount');

        return $transaction;
    }
    public function monthlyExpense(int $month, int $year): float
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
            ->whereYear('created_at', $year)
            ->selectRaw('category, type, SUM(amount) as total')
            ->groupBy('category', 'type')
            ->get()
            ->values();

        return $transaction;
    }
    public function highestMonthlyExpense(): ?Transaction
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('amount', 'DESC')
            ->first();

        return $transaction;
    }
    public function highestMonthlyRevenue(): ?Transaction
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('amount', 'DESC')
            ->first();

        return $transaction;
    }
    public function revenuesByPayment(): Collection
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->values();

        return $transaction;
    }
    public function expensesByPayment(): Collection
    {
        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->values();

        return $transaction;
    }
    public function meanByMonthExpense(): float
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->whereBetween('created_at', [$start, $end])
            ->avg('amount');

        return (float) ($transaction ?? 0.0);
    }
    public function meanByDayExpense(): float
    {
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->whereBetween('created_at', [$start, $end])
            ->avg('amount');

        return (float) ($transaction ?? 0.0);
    }
    public function meanByYearExpense(): float
    {
        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->endOfYear();

        $transaction = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->whereBetween('created_at', [$start, $end])
            ->avg('amount');

        return (float) ($transaction ?? 0.0);
    }
    public function projectionInBalance(): float|int
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $totalRevenue = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->sum('amount');

        $totalExpense = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->sum('amount');

        $totalExpenseThisMonth = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $currentBalance = $totalRevenue - $totalExpense;

        $now = Carbon::now();
        $currentDay = $now->day;
        if ($currentDay <= 0) {
            $currentDay = 1;
        }

        $meanByDaysExpense = $totalExpenseThisMonth / $currentDay;

        $daysRemaining = $now->daysInMonth - $now->day;

        $estimated = $daysRemaining * $meanByDaysExpense;

        return $currentBalance - $estimated;
    }
    public function expensesIncresead(): int|float
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $totalExpenseThisMonth = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $lastStart = Carbon::now()->startOfMonth()->subMonth();
        $lastEnd = Carbon::now()->endOfMonth()->subMonth();

        $totalExpenseLastMonth = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'expense')
            ->whereBetween('created_at', [$lastStart, $lastEnd])
            ->sum('amount');

        if ($totalExpenseLastMonth === 0) {
            return 0;
        }
        $percentage = (($totalExpenseThisMonth - $totalExpenseLastMonth) / $totalExpenseLastMonth) * 100;

        return $percentage;
    }
    public function revenuesIncresead(): int|float
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $totalRevenueThisMonth = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $lastStart = Carbon::now()->startOfMonth()->subMonth();
        $lastEnd = Carbon::now()->endOfMonth()->subMonth();

        $totalRevenueLastMonth = Transaction::where('id_user', $this->getUserId())
            ->where('type', 'revenue')
            ->whereBetween('created_at', [$lastStart, $lastEnd])
            ->sum('amount');

        if ($totalRevenueLastMonth === 0) {
            return 0;
        }
        $percentage = (($totalRevenueThisMonth - $totalRevenueLastMonth) / $totalRevenueLastMonth) * 100;

        return round($percentage, 2);
    }

    public function reportByCategoryAndMonth(int $month, int $year): Collection
    {
        return Transaction::where('id_user', $this->getUserId())
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->selectRaw('category, type, SUM(amount) as total')
            ->groupBy('category', 'type')
            ->get()
            ->values();
    }
    public function createPdfTransaction(int $month, int $year)
    {
        $transaction = $this->expensesByCategory($month, $year);

        $totalIncome = $this->monthlyRevenue($month, $year);
        $totalExpense = $this->monthlyExpense($month, $year);
        $balance = $totalIncome - $totalExpense;

        $pdf = Pdf::loadView('pdf.pdfDownloadTransaction', compact(
            'transaction',
            'month',
            'year',
            'totalIncome',
            'totalExpense',
            'balance'
        ));

        return $pdf->download('pdf-historico_transacoes_' . $year . '-' . $month . '.pdf');
    }
}
