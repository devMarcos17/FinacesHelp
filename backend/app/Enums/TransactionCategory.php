<?php
namespace App\Enums;
enum TransactionCategory: string
{
    case SALARY = 'salary';
    case FREELANCE = 'freelance';
    case INVESTMENT = 'investment';
    case OTHER_INCOME = 'other_income';

        // Expenses (Despesas)
    case WATER_BILL = 'water_bill';
    case ELECTRICITY_BILL = 'electricity_bill';
    case INTERNET = 'internet';
    case RENT = 'rent';
    case FOOD = 'food';
    case GROCERIES = 'groceries';
    case LEISURE = 'leisure';
    case TRANSPORTATION = 'transportation';
    case HEALTH = 'health';
    case EDUCATION = 'education';
    case OTHER_EXPENSE = 'other_expense';

    public function label(): string
    {
        return match ($this) {
            self::SALARY => 'Salary',
            self::WATER_BILL => 'Water Bill',
            self::FOOD => 'Food & Dining',
            self::LEISURE => 'Leisure & Entertainment',
        };
    }
}
