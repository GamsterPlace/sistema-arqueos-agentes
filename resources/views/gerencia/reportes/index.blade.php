@extends('layouts.gerencia')

@section('title', 'Reportes')
@section('module-title', 'Reportes')

@push('styles')
<style>
    .report-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 20px
    }

    .report-option,
    .filters-card,
    .metric-card,
    .table-card {
        border: 1px solid #e0e8ee;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(20, 57, 83, .05)
    }

    .report-option {
        padding: 14px;
        text-decoration: none;
        color: #173b59;
        font-size: 10px;
        font-weight: 800;
        transition: .2s ease
    }

    .report-option:hover {
        transform: translateY(-2px);
        border-color: #b8cddd
    }

    .report-option.active {
        background: #164c96;
        color: #fff;
        border-color: #164c96
    }

    .filters-card {
        padding: 18px;
        margin-bottom: 20px
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px
    }

    .form-control {
        width: 100%;
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid #ced9e1;
        border-radius: 10px;
        background: #fff;
        color: #30485b;
        box-sizing: border-box
    }

    .filters-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 12px
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 15px;
        border: 0;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer
    }

    .btn-primary {
        background: #164c96;
        color: #fff
    }

    .btn-secondary {
        background: #edf2f5;
        color: #3d596f
    }

    .btn-print {
        background: #0f7b4f;
        color: #fff
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-bottom: 20px
    }

    .metric-card {
        padding: 15px
    }

    .metric-card span {
        display: block;
        color: #758697;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase
    }

    .metric-card strong {
        display: block;
        margin-top: 7px;
        color: #082d55;
        font-size: 21px
    }

    .metric-card.danger {
        border-color: #efc5c5;
        background: #fffafa
    }

    .metric-card.success {
        border-color: #c5e5d1;
        background: #f7fcf9
    }

    .metric-card.warning {
        border-color: #efd99f;
        background: #fffdf6
    }

    .table-card {
        overflow: hidden
    }

    .table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid #edf1f4;
        background: #fafcfd
    }

    .table-header h3 {
        margin: 0;
        color: #0a3158;
        font-size: 15px
    }

    .table-header p {
        margin: 5px 0 0;
        color: #82909a;
        font-size: 10px
    }

    .table-responsive {
        overflow-x: auto
    }

    table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse
    }

    th,
    td {
        padding: 12px 13px;
        border-bottom: 1px solid #edf1f4;
        text-align: left;
        vertical-align: middle;
        font-size: 10px
    }

    th {
        background: #f7f9fb;
        color: #687b8b;
        font-size: 8px;
        text-transform: uppercase
    }

    .money-negative {
        color: #b33a34;
        font-weight: 800
    }

    .money-positive {
        color: #16834f;
        font-weight: 800
    }

    .money-neutral {
        color: #607586;
        font-weight: 800
    }

    @media(max-width:1200px) {
        .report-grid {
            grid-template-columns: repeat(3, 1fr)
        }

        .metrics-grid {
            grid-template-columns: repeat(3, 1fr)
        }

        .filters-grid {
            grid-template-columns: repeat(2, 1fr)
        }
    }

    @media(max-width:700px) {
        .report-grid,
        .metrics-grid,
        .filters-grid {
            grid-template-columns: 1fr
        }

        .filters-actions {
            flex-direction: column
        }

        .btn {
            width: 100%
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Centro de Reportes de Gerencia</h2>
        <p>
            Analice comportamiento, incidencias, diferencias y desempeño operativo
            con una vista ejecutiva y filtros por período, Región, Ruta, Agente y Promotor.
        </p>
    </div>
</div>

<section class="report-grid">
    @foreach($tiposReporte as $clave => $nombre)
        <a
            href="{{ route('gerencia.reportes.index', array_merge(request()->except('page'), ['tipo_reporte' => $clave])) }}"
            class="report-option {{ $tipoReporte === $clave ? 'active' : '' }}"
        >
            {{ $nombre }}
        </a>
    @endforeach
</section>

<section class="filters-card">
    <form method="GET" action="{{ route('gerencia.reportes.index') }}">
        <input type="hidden" name="tipo_reporte" value="{{ $tipoReporte }}">

        <div class="filters-grid">
            <input type="date" name="desde" value="{{ $desde }}" class="form-control">
            <input type="date" name="hasta" value="{{ $hasta }}" class="form-control">

            <select name="region_id" class="form-control">
                <option value="">Todas las Regiones</option>
                @foreach($regiones as $region)
                    <option value="{{ $region->id }}" @selected($regionId === (int) $region->id)>
                        {{ $region->nombre }}
                    </option>
                @endforeach
            </select>

            <select name="ruta_id" class="form-control">
                <option value="">Todas las Rutas</option>
                @foreach($rutas as $ruta)
                    <option value="{{ $ruta->id }}" @selected($rutaId === (int) $ruta->id)>
                        {{ $ruta->codigo }} — {{ $ruta->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filters-grid" style="margin-top:10px;">
            <select name="agente_id" class="form-control">
                <option value="">Todos los Agentes</option>
                @foreach($agentes as $agente)
                    <option value="{{ $agente->id }}" @selected($agenteId === (int) $agente->id)>
                        {{ $agente->codigo_agente }} — {{ $agente->nombre_negocio }}
                    </option>
                @endforeach
            </select>

            <select name="promotor_id" class="form-control">
                <option value="">Todos los Promotores</option>
                @foreach($promotores as $promotor)
                    @php
                        $nombrePromotor = trim(($promotor->nombres ?? '') . ' ' . ($promotor->apellidos ?? ''));
                    @endphp
                    <option value="{{ $promotor->id }}" @selected($promotorId === (int) $promotor->id)>
                        {{ $nombrePromotor !== '' ? $nombrePromotor : $promotor->usuario }}
                    </option>
                @endforeach
            </select>

            <select name="tipo_arqueo" class="form-control">
                <option value="">Todos los tipos de arqueo</option>
                <option value="DIARIO_AGENTE" @selected($tipoArqueo === 'DIARIO_AGENTE')>Agente</option>
                <option value="VISITA_PROMOTOR" @selected($tipoArqueo === 'VISITA_PROMOTOR')>Promotor</option>
            </select>

            <div></div>
        </div>

        <div class="filters-actions">
            <a
                href="{{ route('gerencia.reportes.index', ['tipo_reporte' => $tipoReporte]) }}"
                class="btn btn-secondary"
            >
                Limpiar
            </a>

            <button type="submit" class="btn btn-primary">
                Aplicar filtros
            </button>

            <a
                href="{{ route('gerencia.reportes.imprimir', request()->query()) }}"
                target="_blank"
                class="btn btn-print"
            >
                Imprimir Reporte
            </a>
        </div>
    </form>
</section>

<section class="metrics-grid">
    <article class="metric-card">
        <span>Total Arqueos</span>
        <strong>{{ $metricas->total }}</strong>
    </article>

    <article class="metric-card danger">
        <span>Faltantes</span>
        <strong>{{ $metricas->faltantes }}</strong>
        <small>Q {{ number_format($metricas->monto_faltantes, 2) }}</small>
    </article>

    <article class="metric-card success">
        <span>Sobrantes</span>
        <strong>{{ $metricas->sobrantes }}</strong>
        <small>Q {{ number_format($metricas->monto_sobrantes, 2) }}</small>
    </article>

    <article class="metric-card success">
        <span>Exactos</span>
        <strong>{{ $metricas->exactos }}</strong>
    </article>

    <article class="metric-card warning">
        <span>Extemporáneos</span>
        <strong>{{ $metricas->extemporaneos }}</strong>
    </article>
</section>

<section class="table-card">
    <header class="table-header">
        <div>
            <h3>{{ $tiposReporte[$tipoReporte] }}</h3>
            <p>
                Resultados según los filtros seleccionados. Para consultas específicas,
                use los filtros superiores antes de imprimir.
            </p>
        </div>
    </header>

    <div class="table-responsive">
        <table>
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
                            @php
                                $valor = $fila->{$campo} ?? null;
                            @endphp

                            <td>
                                @if(in_array($campo, ['diferencia', 'monto'], true))
                                    @php
                                        $numero = (float) $valor;
                                        $clase = $numero < 0
                                            ? 'money-negative'
                                            : ($numero > 0 ? 'money-positive' : 'money-neutral');
                                    @endphp
                                    <span class="{{ $clase }}">
                                        Q {{ number_format(abs($numero), 2) }}
                                    </span>
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
                        <td colspan="{{ count($columnas) }}" style="padding:35px;text-align:center;">
                            No se encontraron resultados para este reporte.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
