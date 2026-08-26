<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $tituloReporte }}</title>
    <style>
        body{font-family:DejaVu Sans,sans-serif;font-size:8px;color:#243b4e}
        h1{margin:0;color:#12375f;font-size:18px}h2{margin:4px 0 12px;color:#315c83;font-size:13px}
        .meta{margin-bottom:12px;color:#637789;font-size:8px}
        .metrics{width:100%;border-collapse:separate;border-spacing:6px;margin-bottom:12px}.metrics td{padding:8px;border:1px solid #dbe5ec;background:#f8fafb}
        .metrics span{display:block;color:#6f8393;font-size:7px;text-transform:uppercase}.metrics strong{display:block;margin-top:4px;color:#12375f;font-size:13px}
        table.data{width:100%;border-collapse:collapse}.data th,.data td{padding:5px;border:1px solid #dce5eb}.data th{background:#173e6b;color:#fff;font-size:7px;text-transform:uppercase}
    </style>
</head>
<body>
    <h1>Sistema de Arqueos para Agentes MICOOPE</h1>
    <h2>{{ $tituloReporte }}</h2>

    <div class="meta">
        Período:
        {{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : 'Inicio' }}
        al
        {{ $hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : 'Actualidad' }}
    </div>

    <table class="metrics">
        <tr>
            <td><span>Total</span><strong>{{ $metricas->total }}</strong></td>
            <td><span>Faltantes</span><strong>{{ $metricas->faltantes }}</strong></td>
            <td><span>Sobrantes</span><strong>{{ $metricas->sobrantes }}</strong></td>
            <td><span>Exactos</span><strong>{{ $metricas->exactos }}</strong></td>
            <td><span>Extemporáneos</span><strong>{{ $metricas->extemporaneos }}</strong></td>
        </tr>
        <tr>
            <td colspan="2"><span>Monto Faltantes</span><strong>Q {{ number_format($metricas->monto_faltantes, 2) }}</strong></td>
            <td colspan="2"><span>Monto Sobrantes</span><strong>Q {{ number_format($metricas->monto_sobrantes, 2) }}</strong></td>
            <td><span>Arqueos Promotor</span><strong>{{ $metricas->promotores }}</strong></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                @foreach($columnas as $titulo)
                    <th>{{ $titulo }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($resultados as $fila)
                <tr>
                    @foreach($columnas as $campo => $titulo)
                        @php $valor = $fila->{$campo} ?? null; @endphp
                        <td>
                            @if(in_array($campo, ['diferencia', 'monto'], true))
                                Q {{ number_format(abs((float) $valor), 2) }}
                            @elseif($campo === 'fecha_arqueo' && $valor)
                                {{ \Carbon\Carbon::parse($valor)->format('d/m/Y') }}
                            @elseif($campo === 'tipo' && $valor)
                                {{ $valor === 'DIARIO_AGENTE' ? 'Agente' : ($valor === 'VISITA_PROMOTOR' ? 'Promotor' : str_replace('_', ' ', $valor)) }}
                            @else
                                {{ $valor ?? '—' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columnas) }}">Sin resultados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
