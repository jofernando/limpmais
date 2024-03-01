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
            margin: 0 15px;
            padding: 0 0;
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
            /* border-top: 2px solid #6b7280;
            border-bottom: 2px solid #6b7280; */
            height: 5.83cm;
            border-top: 2px solid #6b7280;
            border-bottom: 2px solid #6b7280;
            /* margin-bottom: 3.4rem;
            padding-bottom: 1.7rem; */
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
            margin-top: 1.2rem;
            font-weight: bold;
        }
        .cnpj, .nome-empresa, .titulo {
            text-align: center;
        }
        .cliente, .vencimento {
            text-align: start;
        }
        .cnpj, .titulo, .cliente, .vencimento{
            border-bottom: 2px dashed #6b7280;
            padding-top: 4px;
            padding-bottom: 4px;
        }

        .divida {
            margin-top: 4px;
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
            <div class="nome-empresa">{{config('app.name')}}</div>
            <div class="titulo">extrato para simples conferência</div>
            <div class="cliente">cliente: {{$duplicata['codigo']}} {{$duplicata['nome']}}</div>
            <div class="vencimento">data de vencimento: {{$duplicata['data_vencimento']}}</div>
            <div class="divida">total: R$ {{$duplicata['divida']}}</div>
            <br>
        </div>
        @if($loop->iteration % 5 == 0 && !$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</div>
</body>
</html>
