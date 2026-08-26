@extends('layouts.promotor')

@section('title', 'Reportes')
@section('module-title', 'Reportes')

@section('content')
<style>
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .page-title h2 {
        margin: 0;
        color: #123d69;
        font-size: 28px;
        font-weight: 800;
    }

    .page-title p {
        margin: 7px 0 0;
        color: #6b7c8d;
    }

    .filters-card {
        margin-bottom: 22px;
        padding: 18px;
        border: 1px solid #dfe7ed;
        border-radius: 16px;
        background: #ffffff;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 12px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 6px;
        color: #66798a;
        font-size: 12px;
        font-weight: 800;
    }

    .form-control {
        width: 100%;
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid #ced9e1;
        border-radius: 10px;
        background: #ffffff;
        color: #2f4659;
        outline: none;
    }

    .form-control:focus {
        border-color: #2b72b8;
        box-shadow: 0 0 0 3px rgba(43, 114, 184, .12);
    }

    .filters-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 14px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 17px;
        border: 0;
        border-radius: 10px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-primary {
        background: #174f8a;
        color: #ffffff;
    }

    .btn-secondary {
        background: #eef3f7;
        color: #38556d;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .summary-card {
        padding: 17px;
        border: 1px solid #dfe7ed;
        border-radius: 14px;
        background: #ffffff;
    }

    .summary-card span {
        display: block;
        color: #718394;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .summary-card strong {
        display: block;
        margin-top: 7px;
        color: #173f66;
        font-size: 22px;
        font-weight: 800;
    }

    .table-card {
        overflow: hidden;
        border: 1px solid #dfe7ed;
        border-radius: 16px;
        background: #ffffff;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th,
    .report-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #edf1f4;
        text-align: left;
        vertical-align: middle;
    }

    .report-table th {
        color: #627588;
        background: #f7f9fb;
        font-size: 11px;
        text-transform: uppercase;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        min-height: 25px;
        padding: 0 9px;
        border-radius: 999px;
        background: #edf2f6;
        color: #50667a;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .amount {
        color: #173f66;
        font-weight: 800;
        white-space: nowrap;
    }

    .difference-positive {
        color: #247048;
        font-weight: 800;
    }

    .difference-negative {
        color: #a93b35;
        font-weight: 800;
    }

    .empty-state {
        padding: 42px;
        color: #758697;
        text-align: center;
    }

    .pagination-wrapper {
        padding: 16px;
    }

    @media (max-width: 1150px) {
        .filters-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .table-card {
            overflow-x: auto;
        }

        .report-table {
            min-width: 1100px;
        }
    }

    @media (max-width: 700px) {
        .filters-grid,
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .filters-actions {
            flex-direction: column;
        }
    }
</style>

<div class="page-header">
    <div class="page-title">
        <h2>Reportes</h2>

        <p>
            Consulte los arqueos de sus rutas asignadas y filtre la
            información por fecha, ruta, agente, tipo y estado.
        </p>
    </div>
</div>

<div class="filters-card">
    <form
        method="GET"
        action="{{ route('promotor.reportes.index') }}"
    >
        <div class="filters-grid">
            <div class="filter-group">
                <label for="fecha_inicio">Fecha inicial</label>

                <input
                    type="date"
                    id="fecha_inicio"
                    name="fecha_inicio"
                    class="form-control"
                    value="{{ $fechaInicio }}"
                >
            </div>

            <div class="filter-group">
                <label for="fecha_fin">Fecha final</label>

                <input
                    type="date"
                    id="fecha_fin"
                    name="fecha_fin"
                    class="form-control"
                    value="{{ $fechaFin }}"
                >
            </div>

            <div class="filter-group">
                <label for="ruta_id">Ruta</label>

                <select
                    id="ruta_id"
                    name="ruta_id"
                    class="form-control"
                >
                    <option value="">Todas las rutas</option>

                    @foreach ($rutasAsignadas as $ruta)
                        <option
                            value="{{ $ruta->id }}"
                            @selected($rutaId === (int) $ruta->id)
                        >
                            {{ $ruta->codigo }} — {{ $ruta->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="agente_id">Agente</label>

                <select
                    id="agente_id"
                    name="agente_id"
                    class="form-control"
                >
                    <option value="">Todos los agentes</option>

                    @foreach ($agentesAsignados as $agente)
                        <option
                            value="{{ $agente->id }}"
                            @selected($agenteId === (int) $agente->id)
                        >
                            {{ $agente->codigo_agente }}
                            — {{ $agente->nombre_negocio }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="tipo">Tipo de arqueo</label>

                <select
                    id="tipo"
                    name="tipo"
                    class="form-control"
                >
                    <option value="">Todos los tipos</option>

                    <option
                        value="DIARIO_AGENTE"
                        @selected($tipo === 'DIARIO_AGENTE')
                    >
                        Arqueo del Agente
                    </option>

                    <option
                        value="VISITA_PROMOTOR"
                        @selected($tipo === 'VISITA_PROMOTOR')
                    >
                        Arqueo del Promotor
                    </option>
                </select>
            </div>

            <div class="filter-group">
                <label for="estado">Estado</label>

                <select
                    id="estado"
                    name="estado"
                    class="form-control"
                >
                    <option value="">Todos los estados</option>

                    @foreach ([
                        'PENDIENTE_CERTIFICACION' => 'Pendiente',
                        'CERTIFICADO' => 'Certificado',
                        'ANULADO' => 'Anulado',
                    ] as $valor => $texto)
                        <option
                            value="{{ $valor }}"
                            @selected($estado === $valor)
                        >
                            {{ $texto }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="filters-actions">
            <a
                href="{{ route('promotor.reportes.index') }}"
                class="btn btn-secondary"
            >
                Limpiar filtros
            </a>

            <button type="submit" class="btn btn-primary">
                Generar reporte
            </button>
        </div>
    </form>
</div>

<div class="summary-grid">
    <div class="summary-card">
        <span>Total de arqueos</span>
        <strong>{{ (int) $resumen->total_arqueos }}</strong>
    </div>

    <div class="summary-card">
        <span>Arqueos de agentes</span>
        <strong>{{ (int) $resumen->arqueos_agentes }}</strong>
    </div>

    <div class="summary-card">
        <span>Arqueos del promotor</span>
        <strong>{{ (int) $resumen->arqueos_promotor }}</strong>
    </div>

    <div class="summary-card">
        <span>Certificados</span>
        <strong>{{ (int) $resumen->certificados }}</strong>
    </div>

    <div class="summary-card">
        <span>Pendientes</span>
        <strong>{{ (int) $resumen->pendientes }}</strong>
    </div>

    <div class="summary-card">
        <span>Anulados</span>
        <strong>{{ (int) $resumen->anulados }}</strong>
    </div>


</div>

<div class="table-card">
    <table class="report-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Número</th>
                <th>Agente</th>
                <th>Ruta</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Total arqueado</th>
                <th>Saldo sistema</th>
                <th>Diferencia</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($arqueos as $arqueo)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse(
                            $arqueo->fecha_arqueo
                        )->format('d/m/Y') }}
                    </td>

                    <td>{{ $arqueo->numero_arqueo }}</td>

                    <td>
                        {{ $arqueo->codigo_agente }}
                        — {{ $arqueo->nombre_negocio }}
                    </td>

                    <td>
                        {{ $arqueo->ruta_codigo }}
                        — {{ $arqueo->ruta_nombre }}
                    </td>

                    <td>
                        <span class="badge">
                            {{ $arqueo->tipo === 'DIARIO_AGENTE'
                                ? 'Agente'
                                : 'Promotor' }}
                        </span>
                    </td>

                    <td>
                        <span class="badge">
                            {{ str_replace('_', ' ', $arqueo->estado) }}
                        </span>
                    </td>

                    <td class="amount">
                        Q {{ number_format(
                            (float) $arqueo->total_arqueado,
                            2
                        ) }}
                    </td>

                    <td class="amount">
                        Q {{ number_format(
                            (float) $arqueo->saldo_sistema,
                            2
                        ) }}
                    </td>

                    <td class="{{ (float) $arqueo->diferencia > 0
                        ? 'difference-positive'
                        : ((float) $arqueo->diferencia < 0
                            ? 'difference-negative'
                            : '') }}"
                    >
                        Q {{ number_format(
                            (float) $arqueo->diferencia,
                            2
                        ) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            No se encontraron arqueos para los filtros
                            seleccionados.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($arqueos->hasPages())
        <div class="pagination-wrapper">
            {{ $arqueos->links() }}
        </div>
    @endif
</div>
@endsection
