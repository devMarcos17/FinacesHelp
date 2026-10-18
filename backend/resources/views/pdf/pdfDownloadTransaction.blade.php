<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Financeiro - FinancesHelp</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 24px;
            background-color: #ffffff;
            font-size: 13px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #6366f1;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .header-title h1 {
            color: #4338ca;
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header-title p {
            color: #6b7280;
            margin: 4px 0 0 0;
            font-size: 13px;
        }

        .period-badge {
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
        }

        /* Cards de Resumo */
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }

        .card {
            display: table-cell;
            width: 32%;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 16px;
            vertical-align: middle;
        }

        .card-spacer {
            display: table-cell;
            width: 2%;
        }

        .card-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .card-value {
            font-size: 18px;
            font-weight: bold;
        }

        .card-value.income { color: #059669; }
        .card-value.expense { color: #dc2626; }
        .card-value.balance { color: #2563eb; }

        /* Tabela de Transações */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
        }

        th {
            background-color: #4f46e5;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        td {
            font-size: 13px;
            color: #374151;
            border-top: 1px solid #e5e7eb;
        }

        /* Badges de Tipo */
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            text-transform: capitalize;
        }

        .badge-income {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-expense {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .text-right {
            text-align: right;
        }

        .amount-income {
            color: #059669;
            font-weight: 600;
        }

        .amount-expense {
            color: #dc2626;
            font-weight: 600;
        }

        .footer {
            margin-top: 32px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-title">
            <h1>FinancesHelp</h1>
            <p>Relatório Consolidado de Receitas e Despesas por Categoria</p>
        </div>
        <div>
            <span class="period-badge">Período: {{ $month }}/{{ $year }}</span>
        </div>
    </div>

    <!-- Cards com resumo dos totais vindos do Controller/Service -->
    <div class="summary-cards">
        <div class="card">
            <div class="card-label">Total Receitas</div>
            <div class="card-value income">R$ {{ number_format($totalIncome, 2, ',', '.') }}</div>
        </div>
        <div class="card-spacer"></div>
        <div class="card">
            <div class="card-label">Total Despesas</div>
            <div class="card-value expense">R$ {{ number_format($totalExpense, 2, ',', '.') }}</div>
        </div>
        <div class="card-spacer"></div>
        <div class="card">
            <div class="card-label">Saldo do Período</div>
            <div class="card-value balance">R$ {{ number_format($balance, 2, ',', '.') }}</div>
        </div>
    </div>

    <!-- Tabela principal -->
    <table>
        <thead>
            <tr>
                <th>Categoria</th>
                <th>Tipo</th>
                <th class="text-right">Total Acumulado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaction as $item)
                @php
                    $isIncome = strtolower($item['type'] ?? '') === 'revenue';
                @endphp
                <tr>
                    <td><strong>{{ $item['category'] }}</strong></td>
                    <td>
                        <span class="badge {{ $isIncome ? 'badge-income' : 'badge-expense' }}">
                            {{ $isIncome ? 'Receita' : 'Despesa' }}
                        </span>
                    </td>
                    <td class="text-right {{ $isIncome ? 'amount-income' : 'amount-expense' }}">
                        {{ $isIncome ? '+' : '-' }} R$ {{ number_format($item['total'], 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #9ca3af; padding: 24px;">
                        Nenhuma movimentação encontrada para este período.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Gerado automaticamente pelo sistema <strong>FinancesHelp</strong> em {{ date('d/m/Y \à\s H:i') }}</p>
    </div>

</body>
</html>