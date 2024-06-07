<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $fileName }}</title>
    <style type="text/css" media="all">
        * {
            font-family: DejaVu Sans, sans-serif !important;
        }

        html{
            width:100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            border-radius: 10px 10px 10px 10px;
        }

        table td,
        th {
            border-color: #ededed;
            border-style: solid;
            border-width: 1px;
            font-size: 14px;
            overflow: hidden;
            padding: 10px 5px;
            word-break: normal;
        }

        table th {
            font-weight: normal;
        }

    </style>
</head>
<body>
    @isset($cliente)
        <p>Nome: {{ $cliente->nome }} </p>
        <p>CPF/CNPJ: {{ $cliente->cpf_cnpj }} </p>
        <p>Celular: {{ $cliente->numero }} </p>
        <p>Rua: {{ $cliente->rua }} </p>
        <p>Cidade/Sítio: {{ $cliente->cidade }} </p>
        <p>Estado: {{ $cliente->estado }} </p>
        <p>Ponto de referência: {{ $cliente->ponto_referencia }} </p>
        <p>Setor: {{ $cliente->setor }} </p>
        <p>Dívida: {{ "R$ " . number_format($cliente->divida, 2, ',', '.') }} </p>
        <p>Duplicatas:</p>
    @endisset
    <table>
        <tr>
            @foreach ($columns as $column)
                <th>
                    {{ $column->getLabel() }}
                </th>
            @endforeach
        </tr>
        @foreach ($rows as $row)
            <tr>
                @foreach ($columns as $column)
                    <td>
                        {{ $row[$column->getName()] }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>
</body>
</html>
