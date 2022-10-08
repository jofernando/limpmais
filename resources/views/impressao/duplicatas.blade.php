<!doctype html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @page {
            padding-bottom: 0;
            margin-bottom: 0;
            padding-top: 0;
            margin-top: 0;
        }
        :root {
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto Mono', monospace;
            margin: 0;
            padding: 0;
        }
        .container {
            text-transform: uppercase;
        }
        .duplicata {
            page-break-inside: avoid;
            border-top: 2px solid #6b7280;
            border-bottom: 2px solid #6b7280;
            margin-bottom: 3.4rem;
            padding-bottom: 1.7rem;
        }
        .page-break {
            page-break-after: always;
        }
        .primeira-linha {
            display: block;
        }
        .data {
            float: left;
        }
        .hora {
            float: right;
        }
        .nome-empresa {
            padding-top: 1rem;
            font-weight: bold;
        }
        .titulo {
            border-bottom: 1px solid #6b7280;
            margin-bottom: 5px;
        }
        .cnpj, .nome-empresa, .titulo {
            text-align: center;
        }
        .cliente, .vencimento {
            text-align: start;
        }

    </style>
</head>
<body>
<div class="container">
    @foreach($duplicatas as $duplicata)
        <div class="duplicata" @if($loop->iteration % 5 == 0) style="margin-bottom: 0;" @endif>
            <div class="primeira-linha">
                <div class="data">{{$data}}</div>
                <div class="hora">{{$hora}}</div>
            </div>
            <div class="nome-empresa">limp mais</div>
            <div class="cnpj">cnpj: 10.698.484-0001-48</div>
            <div class="titulo">extrato para simples conferência</div>
            <div class="cliente">cliente: {{$duplicata['codigo']}} {{$duplicata['nome']}}</div>
            <div class="vencimento">data de vencimento: {{$duplicata['data_vencimento']}}</div>
            <div class="divida">total: {{$duplicata['divida']}}</div>
            <br>
        </div>
        @if($loop->iteration % 5 == 0)
            <div class="page-break"></div>
        @endif
    @endforeach
</div>
</body>
</html>
