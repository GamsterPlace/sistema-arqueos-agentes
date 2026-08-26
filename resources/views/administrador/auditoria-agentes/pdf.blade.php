<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body{font-family:DejaVu Sans,sans-serif;font-size:9px;color:#263d50}
        h1,h2,h3{color:#12375f}
        .box{border:1px solid #dce5eb;padding:9px;margin-bottom:10px}
        table{width:100%;border-collapse:collapse}
        th,td{border:1px solid #dce5eb;padding:5px;text-align:left}
    </style>
</head>
<body>
@php
    $nombreAuditor = trim(
        ($auditor->nombres ?? '')
        . ' '
        . ($auditor->apellidos ?? '')
    );
@endphp

<h1>Sistema de Arqueos para Agentes MICOOPE</h1>
<h2>Auditoría de Agente — {{ $arqueo->numero_arqueo }}</h2>

<div class="box">
    <strong>Auditor:</strong> {{ $nombreAuditor !== '' ? $nombreAuditor : ($auditor->usuario ?? '—') }}<br>
    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($arqueo->fecha_arqueo)->format('d/m/Y') }}<br>
    <strong>Agente:</strong> {{ $arqueo->codigo_agente_historico }} — {{ $arqueo->nombre_negocio_historico }}<br>
    <strong>Propietario:</strong> {{ $arqueo->nombre_propietario_historico }}<br>
    <strong>Región:</strong> {{ $arqueo->region_historica }}<br>
    <strong>Ruta:</strong> {{ $arqueo->ruta_historica }}
</div>

<div class="box">
    <strong>Saldo Sistema:</strong> Q {{ number_format((float)$arqueo->saldo_sistema,2) }}<br>
    <strong>Total Arqueado:</strong> Q {{ number_format((float)$arqueo->total_arqueado,2) }}<br>
    <strong>Diferencia:</strong> Q {{ number_format(abs((float)$arqueo->diferencia),2) }}<br>
    <strong>Estado:</strong> {{ str_replace('_',' ',$arqueo->estado) }}
</div>

<h3>Billetes</h3>
<table>
    <thead>
        <tr><th>Denominación</th><th>Cantidad</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
        @foreach($billetes as $detalle)
            <tr>
                <td>Q {{ number_format((float)$detalle->denominacion,2) }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>Q {{ number_format((float)$detalle->subtotal,2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h3>Monedas</h3>
<table>
    <thead>
        <tr><th>Denominación</th><th>Cantidad</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
        @foreach($monedas as $detalle)
            <tr>
                <td>Q {{ number_format((float)$detalle->denominacion,2) }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>Q {{ number_format((float)$detalle->subtotal,2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
