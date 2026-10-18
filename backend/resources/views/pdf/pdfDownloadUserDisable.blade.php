<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Usuários Inativos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            padding: 30px;
            font-size: 12px;
            color: #333;
        }

        /* Cabeçalho do Documento (Tema Azul) */
        .header {
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 10px;
        }

        .header table {
            width: 100%;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #0056b3;
            text-transform: uppercase;
        }

        .sub-title {
            font-size: 11px;
            color: #666;
            margin-top: 4px;
        }

        .date-info {
            text-align: right;
            font-size: 11px;
            color: #555;
        }

        /* Tabela Principal */
        .table-users {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table-users th {
            background-color: #0056b3;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-users td {
            padding: 8px 10px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 11px;
        }

        /* Zebra Striping */
        .table-users tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Badges de Status e Papel (Diferenciando o inativo com um tom mais sóbrio/vermelho claro ou cinza) */
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .badge-disable {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-role {
            background-color: #e2e3e5;
            color: #383d41;
        }

        /* Rodapé do PDF */
        .footer {
            position: fixed;
            bottom: 20px;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        /* Totalizador */
        .total-info {
            margin-top: 15px;
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            color: #444;
        }
    </style>
</head>
<body>

    <!-- Cabeçalho -->
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="title">Relatório de Usuários Inativos</div>
                    <div class="sub-title">Listagem completa dos usuários com status inativo no sistema</div>
                </td>
                <td class="date-info">
                    Data de Emissão: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabela de Dados -->
    <table class="table-users">
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 25%;">Nome</th>
                <th style="width: 25%;">E-mail</th>
                <th style="width: 15%;">Telefone</th>
                <th style="width: 10%;">Perfil</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Cadastro</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($user as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->phone ?? '-' }}</td>
                    <td>
                        <span class="badge badge-role">{{ ucfirst($item->role) }}</span>
                    </td>
                    <td>
                        <span class="badge badge-disable">Inativo</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px; color: #777;">
                        Nenhum usuário inativo encontrado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totalizador -->
    <div class="total-info">
        Total de Usuários Inativos: {{ $user->count() }}
    </div>

    <!-- Rodapé -->
    <div class="footer">
        Documento gerado automaticamente pelo sistema • Página 1
    </div>

</body>
</html>