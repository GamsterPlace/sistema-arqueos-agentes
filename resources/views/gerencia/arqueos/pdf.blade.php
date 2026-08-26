<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $arqueo->numero_arqueo }}</title>

    <style>
        body{
            font-family:DejaVu Sans,sans-serif;
            font-size:9px;
            color:#263d50;
        }

        h1,h2,h3{
            color:#12375f;
        }

        .grid{
            width:100%;
            border-collapse:collapse;
            margin-bottom:12px;
        }

        .grid td{
            width:50%;
            padding:8px;
            border:1px solid #dce5eb;
            vertical-align:top;
        }

        .detalle{
            width:100%;
            border-collapse:collapse;
        }

        .detalle th,
        .detalle td{
            padding:6px;
            border:1px solid #dce5eb;
        }

        .detalle th{
            background:#173e6b;
            color:#fff;
        }
    </style>
</head>

<body>
@php
    $agente = $arqueo->agente;
    $ruta = $agente?->ruta;
    $region = $ruta?->region;

    $nombreResponsable = trim(
        ($responsable->nombres ?? '')
        . ' '
        . ($responsable->apellidos ?? '')
    );
@endphp

<h1>Sistema de Arqueos para Agentes MICOOPE</h1>
<h2>{{ $arqueo->numero_arqueo }}</h2>

<table class="grid">
    <tr>
        <td>
            <strong>Agente:</strong>
            {{ $agente?->codigo_agente }}
            — {{ $agente?->nombre_negocio }}
            <br>

            <strong>Propietario:</strong>
            {{ $agente?->nombre_propietario }}
            <br>

            <strong>Región:</strong>
            {{ $region?->nombre ?? '—' }}
            <br>

            <strong>Ruta:</strong>
            {{ $ruta?->codigo ?? '' }}
            {{ $ruta?->nombre ?? '—' }}
        </td>

        <td>
            <strong>Tipo:</strong>
            {{ $arqueo->tipo === 'DIARIO_AGENTE'
                ? 'Arqueo del Agente'
                : (
                    $arqueo->tipo === 'VISITA_PROMOTOR'
                        ? 'Arqueo de Promotor'
                        : str_replace('_', ' ', $arqueo->tipo)
                ) }}
            <br>

            <strong>Fecha:</strong>
            {{ \Carbon\Carbon::parse(
                $arqueo->fecha_arqueo
            )->format('d/m/Y') }}
            <br>

            <strong>Estado:</strong>
            {{ str_replace('_', ' ', $arqueo->estado) }}
            <br>

            <strong>Responsable:</strong>
            {{ $nombreResponsable !== ''
                ? $nombreResponsable
                : ($responsable->usuario ?? '—') }}
            <br>

            <strong>Extemporáneo:</strong>
            {{ $arqueo->fuera_fecha_ordinaria ? 'Sí' : 'No' }}
        </td>
    </tr>
</table>

<table class="grid">
    <tr>
        <td>
            <strong>Saldo Sistema:</strong>
            Q {{ number_format(
                (float) $arqueo->saldo_sistema,
                2
            ) }}
        </td>

        <td>
            <strong>Total Arqueado:</strong>
            Q {{ number_format(
                (float) $arqueo->total_arqueado,
                2
            ) }}
        </td>
    </tr>

    <tr>
        <td colspan="2">
            <strong>Diferencia:</strong>
            Q {{ number_format(
                abs((float) $arqueo->diferencia),
                2
            ) }}
        </td>
    </tr>
</table>

<h3>Billetes</h3>

<table class="detalle">
    <thead>
        <tr>
            <th>Denominación</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
        </tr>
    </thead>

    <tbody>
        @foreach($billetes as $detalle)
            <tr>
                <td>
                    Q {{ number_format(
                        (float) $detalle->denominacion,
                        2
                    ) }}
                </td>

                <td>{{ $detalle->cantidad }}</td>

                <td>
                    Q {{ number_format(
                        (float) $detalle->subtotal,
                        2
                    ) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<h3>Monedas</h3>

<table class="detalle">
    <thead>
        <tr>
            <th>Denominación</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
        </tr>
    </thead>

    <tbody>
        @foreach($monedas as $detalle)
            <tr>
                <td>
                    Q {{ number_format(
                        (float) $detalle->denominacion,
                        2
                    ) }}
                </td>

                <td>{{ $detalle->cantidad }}</td>

                <td>
                    Q {{ number_format(
                        (float) $detalle->subtotal,
                        2
                    ) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<p>
    <strong>Observaciones:</strong>
    {{ $arqueo->observaciones ?: 'Sin observaciones' }}
</p>
</body>
</html>
