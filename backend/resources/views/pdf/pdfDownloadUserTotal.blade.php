<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Usuários</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 20px;
        }

        .header {
            margin-bottom: 25px;
            border-bottom: 3px solid #193e5c;
            padding-bottom: 10px;
        }

        h1 {
            color: #193e5c;
            font-size: 20px;
            text-transform: uppercase;
            margin: 0;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: #ffffff;
        }

        th {
            background-color: #193e5c;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            padding: 10px 8px;
            border: 1px solid #193e5c;
            text-align: left;
        }

        td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            color: #334155;
            vertical-align: middle;
        }

        /* Efeito zebrado suave nas linhas */
        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Cores e destaque para o status */
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .badge-ativo {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-inativo {
            background-color: #f1f5f9;
            color: #475569;
        }

        .badge-admin {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Relatório Geral de Usuários</h1>
        <div class="subtitle">Gerado pelo Painel Administrativo - FinancesHelp</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 25%;">Nome</th>
                <th style="width: 25%;">E-mail</th>
                <th style="width: 15%;">Telefone</th>
                <th style="width: 10%;">Cargo</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Cadastro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($user as $u)
                <tr>
                    <td>{{ $u->id }}</td>
                    <td><strong>{{ $u->name }}</strong></td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->phone }}</td>
                    <td>
                        <span class="badge badge-admin">
                            {{ ucfirst($u->role ?? 'user') }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusClass = ($u->status == 'ativo') ? 'badge-ativo' : 'badge-inativo';
                        @endphp
                        <span class="badge {{ $statusClass }}">
                            {{ ucfirst($u->status) }}
                        </span>
                    </td>
                    <td>{{ $u->created_at ? $u->created_at->format('d/m/Y') : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Página gerada automaticamente em {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>