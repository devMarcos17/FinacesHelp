<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Financeiro</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; padding: 30px; margin: 0; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 15px; }
        h1 { color: #4f46e5; margin: 0 0 5px 0; font-size: 24px; }
        .subtitle { color: #666; font-size: 14px; margin: 0; }
        
        .card-projecao { background-color: #f3f4f6; border-left: 4px solid #4f46e5; padding: 15px; margin-bottom: 25px; border-radius: 4px; }
        .card-projecao p { margin: 0; font-size: 15px; color: #1f2937; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 12px; border: 1px solid #e5e7eb; font-size: 14px; }
        td { padding: 12px; border: 1px solid #e5e7eb; font-size: 14px; color: #4b5563; }
        
        .text-green { color: #059669; font-weight: bold; }
        .text-red { color: #dc2626; font-weight: bold; }
        .text-blue { color: #2563eb; font-weight: bold; }

        .footer { margin-top: 50px; font-size: 11px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FinancesHelp</h1>
        <p class="subtitle">Relatório Financeiro do Dashboard — Período: {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}</p>
    </div>

    <div class="card-projecao">
        <p><strong>Saldo Projetado para o final do período:</strong> R$ {{ number_format($projection, 2, ',', '.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Indicador Financeiro</th>
                <th>Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Saldo Atual</strong></td>
                <td class="text-blue">R$ {{ number_format($balance, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total de Receitas</strong></td>
                <td class="text-green">R$ {{ number_format($revenue, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total de Despesas</strong></td>
                <td class="text-red">R$ {{ number_format($expense, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Relatório gerado automaticamente pelo sistema FinancesHelp em {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}.</p>
    </div>
</body>
</html>