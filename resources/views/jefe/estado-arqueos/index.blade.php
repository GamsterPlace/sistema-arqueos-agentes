@extends('layouts.jefe')

@section('title', 'Estado de Arqueos')
@section('module-title', 'Estado de Arqueos')

@push('styles')
<style>
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .summary-card {
        padding: 17px;
        border: 1px solid #e0e8ee;
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 8px 24px rgba(20, 57, 83, .05);
    }

    .summary-card span {
        display: block;
        color: #758697;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .summary-card strong {
        display: block;
        margin-top: 8px;
        color: #082d55;
        font-size: 24px;
    }

    .summary-card.pending {
        border-color: #efd99f;
        background: #fffdf6;
    }

    .summary-card.danger {
        border-color: #efc5c5;
        background: #fffafa;
    }

    .summary-card.info {
        border-color: #cbdff1;
        background: #f7fbff;
    }

    .filters-card,
    .table-card {
        overflow: hidden;
        border: 1px solid #e0e8ee;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 24px rgba(20, 57, 83, .05);
    }

    .filters-card {
        margin-bottom: 20px;
        padding: 18px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns:
            minmax(190px, 1fr)
            170px
            170px
            190px
            170px
            170px;
        gap: 11px;
    }

    .form-control {
        width: 100%;
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid #ced9e1;
        border-radius: 10px;
        background: #ffffff;
        color: #30485b;
        outline: none;
    }

    .filters-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 13px;
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
        cursor: pointer;
    }

    .btn-primary {
        background: #164c96;
        color: #ffffff;
    }

    .btn-secondary {
        background: #edf2f5;
        color: #3d596f;
    }

    .table-header {
        padding: 18px 20px;
        border-bottom: 1px solid #edf1f4;
        background: #fafcfd;
    }

    .table-header h3 {
        margin: 0;
        color: #0a3158;
        font-size: 15px;
    }

    .table-header p {
        margin: 5px 0 0;
        color: #82909a;
        font-size: 10px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .status-table {
        width: 100%;
        min-width: 1250px;
        border-collapse: collapse;
    }

    .status-table th,
    .status-table td {
        padding: 12px 13px;
        border-bottom: 1px solid #edf1f4;
        text-align: left;
        vertical-align: middle;
        font-size: 10px;
    }

    .status-table th {
        color: #687b8b;
        background: #f7f9fb;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .status-table td strong {
        color: #173b59;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .status-badge.done {
        background: #eaf8ef;
        color: #1d7b4e;
    }

    .status-badge.pending {
        background: #fff5dc;
        color: #9c7014;
    }

    .status-badge.no-attention {
        background: #fdecec;
        color: #b13c3c;
    }

    .status-badge.extra {
        background: #eee9ff;
        color: #6345a2;
    }

    .status-badge.cancelled {
        background: #edf0f2;
        color: #596a78;
    }

    .actions {
        display: flex;
        gap: 7px;
    }

    .action-link {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 0 10px;
        border: 1px solid #d5dfe5;
        border-radius: 9px;
        background: #ffffff;
        color: #31536e;
        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
    }

    .empty-state {
        padding: 44px 20px;
        color: #7d8b95;
        text-align: center;
        font-size: 11px;
    }

    .pagination {
        padding: 16px 18px;
    }

    @media (max-width: 1200px) {
        .summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .filters-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .summary-grid,
        .filters-grid {
            grid-template-columns: 1fr;
        }

        .filters-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Estado de Arqueos</h2>
        <p>
            Supervise el cumplimiento diario de los arqueos por agente,
            ruta, región y promotor asignado.
        </p>
    </div>
</div>

<section class="summary-grid">
    <article class="summary-card">
        <span>Total agentes</span>
        <strong>{{ $totalAgentes }}</strong>
    </article>

    <article class="summary-card">
        <span>Arqueados</span>
        <strong>{{ $arqueadosHoy }}</strong>
    </article>

    <article class="summary-card pending">
        <span>Pendientes</span>
        <strong>{{ $pendientes }}</strong>
    </article>

    <article class="summary-card danger">
        <span>No atendieron</span>
        <strong>{{ $noAtendieron }}</strong>
    </article>

    <article class="summary-card info">
        <span>Extemporáneos</span>
        <strong>{{ $extemporaneos }}</strong>
    </article>

    <article class="summary-card">
        <span>Anulados</span>
        <strong>{{ $anulados }}</strong>
    </article>
</section>

<section class="filters-card">
    <form method="GET" action="{{ route('jefe.estado-arqueos.index') }}">
        <div class="filters-grid">
            <input
                type="text"
                name="buscar"
                class="form-control"
                value="{{ $buscar }}"
                placeholder="Buscar agente o negocio"
            >

            <input
                type="date"
                name="fecha"
                class="form-control"
                value="{{ $fecha }}"
            >

            <select name="region_id" class="form-control">
                <option value="">Todas las regiones</option>

                @foreach ($regiones as $region)
                    <option
                        value="{{ $region->id }}"
                        @selected($regionId === (int) $region->id)
                    >
                        {{ $region->nombre }}
                    </option>
                @endforeach
            </select>

            <select name="ruta_id" class="form-control">
                <option value="">Todas las rutas</option>

                @foreach ($rutas as $ruta)
                    <option
                        value="{{ $ruta->id }}"
                        @selected($rutaId === (int) $ruta->id)
                    >
                        {{ $ruta->codigo }} — {{ $ruta->nombre }}
                    </option>
                @endforeach
            </select>

            <select name="promotor_id" class="form-control">
                <option value="">Todos los promotores</option>

                @foreach ($promotores as $promotor)
                    @php
                        $nombrePromotor = trim(
                            ($promotor->nombres ?? '')
                            . ' '
                            . ($promotor->apellidos ?? '')
                        );
                    @endphp

                    <option
                        value="{{ $promotor->id }}"
                        @selected(
                            $promotorId === (int) $promotor->id
                        )
                    >
                        {{ $nombrePromotor !== ''
                            ? $nombrePromotor
                            : $promotor->nombre_usuario }}
                    </option>
                @endforeach
            </select>

            <select name="estado" class="form-control">
                <option value="">Todos los estados</option>
                <option value="ARQUEADO" @selected($estado === 'ARQUEADO')>
                    Arqueado
                </option>
                <option value="PENDIENTE" @selected($estado === 'PENDIENTE')>
                    Pendiente
                </option>
                <option value="NO_ATENDIO" @selected($estado === 'NO_ATENDIO')>
                    No atendió
                </option>
                <option value="EXTEMPORANEO" @selected($estado === 'EXTEMPORANEO')>
                    Extemporáneo
                </option>
                <option value="ANULADO" @selected($estado === 'ANULADO')>
                    Anulado
                </option>
            </select>
        </div>

        <div class="filters-actions">
            <a
                href="{{ route('jefe.estado-arqueos.index') }}"
                class="btn btn-secondary"
            >
                Limpiar
            </a>

            <button type="submit" class="btn btn-primary">
                Aplicar filtros
            </button>
        </div>
    </form>
</section>

<section class="table-card">
    <header class="table-header">
        <h3>Control diario por agente</h3>
        <p>
            Estado correspondiente a la fecha
            {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}.
        </p>
    </header>

    <div class="table-responsive">
        @if ($agentes->isEmpty())
            <div class="empty-state">
                No se encontraron agentes para los filtros seleccionados.
            </div>
        @else
            <table class="status-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Negocio</th>
                        <th>Propietario</th>
                        <th>Región</th>
                        <th>Ruta</th>
                        <th>Promotor asignado</th>
                        <th>Estado</th>
                        <th>No. arqueo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($agentes as $agente)
                        @php
                            $nombrePromotor = trim(
                                ($agente->promotor_nombres ?? '')
                                . ' '
                                . ($agente->promotor_apellidos ?? '')
                            );

                            if ($agente->estado_control === 'NO_ATENDIO') {
                                $estadoTexto = 'No atendió';
                                $estadoClase = 'no-attention';
                            } elseif ($agente->estado_arqueo === 'ANULADO') {
                                $estadoTexto = 'Anulado';
                                $estadoClase = 'cancelled';
                            } elseif ((bool) $agente->fuera_fecha_ordinaria) {
                                $estadoTexto = 'Extemporáneo';
                                $estadoClase = 'extra';
                            } elseif ($agente->arqueo_id) {
                                $estadoTexto = 'Arqueado';
                                $estadoClase = 'done';
                            } else {
                                $estadoTexto = 'Pendiente';
                                $estadoClase = 'pending';
                            }
                        @endphp

                        <tr>
                            <td>
                                <strong>{{ $agente->codigo_agente }}</strong>
                            </td>
                            <td>{{ $agente->nombre_negocio }}</td>
                            <td>{{ $agente->nombre_propietario }}</td>
                            <td>{{ $agente->region_nombre }}</td>
                            <td>
                                {{ $agente->ruta_codigo }}
                                — {{ $agente->ruta_nombre }}
                            </td>
                            <td>
                                {{ $nombrePromotor !== ''
                                    ? $nombrePromotor
                                    : ($agente->promotor_usuario
                                        ?: 'Sin promotor asignado') }}
                            </td>
                            <td>
                                <span class="status-badge {{ $estadoClase }}">
                                    {{ $estadoTexto }}
                                </span>
                            </td>
                            <td>{{ $agente->numero_arqueo ?: '—' }}</td>
                            <td>
                                <div class="actions">
                                    <a
                                        href="{{ url(
                                            '/jefe-agentes/arqueos-agentes'
                                            . '?agente_id='
                                            . $agente->id
                                        ) }}"
                                        class="action-link"
                                    >
                                        Ver arqueos
                                    </a>

                                    <a
                                        href="{{ url(
                                            '/jefe-agentes/agentes/'
                                            . $agente->id
                                        ) }}"
                                        class="action-link"
                                    >
                                        Ver agente
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($agentes->hasPages())
        <div class="pagination">
            {{ $agentes->links() }}
        </div>
    @endif
</section>
@endsection
