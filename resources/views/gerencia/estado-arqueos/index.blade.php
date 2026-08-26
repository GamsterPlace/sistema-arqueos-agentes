@extends('layouts.gerencia')

@section('title', 'Estado de Arqueos')
@section('module-title', 'Estado de Arqueos')

@push('styles')
<style>
    .summary-grid {
        display:grid;
        grid-template-columns:repeat(5,minmax(0,1fr));
        gap:14px;
        margin-bottom:22px;
    }

    .summary-card,
    .filters-card,
    .table-card {
        border:1px solid #e0e8ee;
        border-radius:18px;
        background:#fff;
        box-shadow:0 8px 24px rgba(20,57,83,.05);
    }

    .summary-card {
        padding:17px;
    }

    .summary-card span {
        display:block;
        color:#758697;
        font-size:8px;
        font-weight:800;
        text-transform:uppercase;
    }

    .summary-card strong {
        display:block;
        margin-top:8px;
        color:#082d55;
        font-size:23px;
    }

    .summary-card.success {
        border-color:#c5e5d1;
        background:#f7fcf9;
    }

    .summary-card.warning {
        border-color:#efd99f;
        background:#fffdf6;
    }

    .summary-card.info {
        border-color:#c9dceb;
        background:#f7fbff;
    }

    .filters-card {
        padding:18px;
        margin-bottom:20px;
    }

    .filters-grid {
        display:grid;
        grid-template-columns:
            minmax(220px,1fr)
            170px
            210px
            210px
            210px
            180px;
        gap:10px;
    }

    .form-control {
        width:100%;
        min-height:42px;
        padding:0 12px;
        border:1px solid #ced9e1;
        border-radius:10px;
        background:#fff;
        color:#30485b;
        box-sizing:border-box;
    }

    .filters-actions {
        display:flex;
        justify-content:flex-end;
        gap:10px;
        margin-top:13px;
    }

    .btn {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-height:40px;
        padding:0 15px;
        border:0;
        border-radius:10px;
        font-size:10px;
        font-weight:800;
        text-decoration:none;
        cursor:pointer;
    }

    .btn-primary {
        background:#164c96;
        color:#fff;
    }

    .btn-secondary {
        background:#edf2f5;
        color:#3d596f;
    }

    .table-card {
        overflow:hidden;
    }

    .table-header {
        padding:18px 20px;
        border-bottom:1px solid #edf1f4;
        background:#fafcfd;
    }

    .table-header h3 {
        margin:0;
        color:#0a3158;
        font-size:15px;
    }

    .table-header p {
        margin:5px 0 0;
        color:#82909a;
        font-size:10px;
    }

    .table-responsive {
        overflow-x:auto;
    }

    table {
        width:100%;
        min-width:1250px;
        border-collapse:collapse;
    }

    th,
    td {
        padding:12px 13px;
        border-bottom:1px solid #edf1f4;
        text-align:left;
        vertical-align:middle;
        font-size:10px;
    }

    th {
        background:#f7f9fb;
        color:#687b8b;
        font-size:8px;
        font-weight:800;
        text-transform:uppercase;
    }

    .badge {
        display:inline-flex;
        padding:6px 9px;
        border-radius:999px;
        font-size:8px;
        font-weight:800;
        text-transform:uppercase;
        white-space:nowrap;
    }

    .badge.success {
        background:#eaf8ef;
        color:#1d7b4e;
    }

    .badge.warning {
        background:#fff5dc;
        color:#9c7014;
    }

    .badge.info {
        background:#eaf3fb;
        color:#285f91;
    }

    .diff-negative {
        color:#b33a34;
        font-weight:800;
    }

    .diff-positive {
        color:#16834f;
        font-weight:800;
    }

    .diff-zero {
        color:#607586;
        font-weight:800;
    }

    .pagination {
        padding:16px 18px;
    }

    @media(max-width:1200px) {
        .summary-grid {
            grid-template-columns:repeat(3,1fr);
        }

        .filters-grid {
            grid-template-columns:repeat(2,1fr);
        }
    }

    @media(max-width:700px) {
        .summary-grid,
        .filters-grid {
            grid-template-columns:1fr;
        }

        .filters-actions {
            flex-direction:column;
        }

        .btn {
            width:100%;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Estado de Arqueos</h2>

        <p>
            Consulte el cumplimiento diario de los Agentes,
            su estado operativo y las diferencias registradas.
        </p>
    </div>
</div>

<section class="summary-grid">
    <article class="summary-card info">
        <span>Agentes activos</span>
        <strong>{{ $totalActivos }}</strong>
    </article>

    <article class="summary-card success">
        <span>Arqueos realizados</span>
        <strong>{{ $realizados }}</strong>
    </article>

    <article class="summary-card warning">
        <span>Pendientes</span>
        <strong>{{ $pendientes }}</strong>
    </article>

    <article class="summary-card">
        <span>No atendió</span>
        <strong>{{ $noAtendio }}</strong>
    </article>

    <article class="summary-card success">
        <span>Cumplimiento</span>
        <strong>{{ number_format($cumplimiento, 1) }}%</strong>
    </article>
</section>

<section class="filters-card">
    <form
        method="GET"
        action="{{ route(
            'gerencia.estado-arqueos.index'
        ) }}"
    >
        <div class="filters-grid">
            <input
                type="text"
                name="buscar"
                class="form-control"
                value="{{ $buscar }}"
                placeholder="Agente, negocio, ruta, región o Promotor"
            >

            <input
                type="date"
                name="fecha"
                class="form-control"
                value="{{ $fecha }}"
            >

            <select
                name="region_id"
                class="form-control"
            >
                <option value="">
                    Todas las Regiones
                </option>

                @foreach($regiones as $region)
                    <option
                        value="{{ $region->id }}"
                        @selected(
                            $regionId === (int) $region->id
                        )
                    >
                        {{ $region->nombre }}
                    </option>
                @endforeach
            </select>

            <select
                name="ruta_id"
                class="form-control"
            >
                <option value="">
                    Todas las Rutas
                </option>

                @foreach($rutas as $ruta)
                    <option
                        value="{{ $ruta->id }}"
                        @selected(
                            $rutaId === (int) $ruta->id
                        )
                    >
                        {{ $ruta->codigo }}
                        — {{ $ruta->nombre }}
                    </option>
                @endforeach
            </select>

            <select
                name="promotor_id"
                class="form-control"
            >
                <option value="">
                    Todos los Promotores
                </option>

                @foreach($promotores as $promotor)
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
                            : $promotor->usuario }}
                    </option>
                @endforeach
            </select>

            <select
                name="estado"
                class="form-control"
            >
                <option value="">
                    Todos
                </option>

                <option
                    value="REALIZADO"
                    @selected($estado === 'REALIZADO')
                >
                    Realizado
                </option>

                <option
                    value="PENDIENTE"
                    @selected($estado === 'PENDIENTE')
                >
                    Pendiente
                </option>

                <option
                    value="NO_ATENDIO"
                    @selected($estado === 'NO_ATENDIO')
                >
                    No atendió
                </option>
            </select>
        </div>

        <div class="filters-actions">
            <a
                href="{{ route(
                    'gerencia.estado-arqueos.index'
                ) }}"
                class="btn btn-secondary"
            >
                Limpiar
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Aplicar Filtros
            </button>
        </div>
    </form>
</section>

<section class="table-card">
    <header class="table-header">
        <h3>Estado diario por Agente</h3>

        <p>
            Fecha consultada:
            {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </p>
    </header>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Agente</th>
                    <th>Propietario</th>
                    <th>Región</th>
                    <th>Ruta</th>
                    <th>Promotor</th>
                    <th>Estado</th>
                    <th>Estado arqueo</th>
                    <th>Diferencia</th>
                </tr>
            </thead>

            <tbody>
                @forelse($agentes as $agente)
                    @php
                        $nombrePromotor = trim(
                            ($agente->promotor_nombres ?? '')
                            . ' '
                            . ($agente->promotor_apellidos ?? '')
                        );

                        $tieneArqueo =
                            (int) $agente->arqueo_agente > 0;

                        $noAtendioAgente =
                            $agente->control_diario === 'NO_ATENDIO';

                        $estadoOperativo =
                            $tieneArqueo
                                ? 'REALIZADO'
                                : (
                                    $noAtendioAgente
                                        ? 'NO ATENDIÓ'
                                        : 'PENDIENTE'
                                );

                        $claseEstado =
                            $tieneArqueo
                                ? 'success'
                                : (
                                    $noAtendioAgente
                                        ? 'info'
                                        : 'warning'
                                );

                        $diferencia =
                            $agente->diferencia !== null
                                ? (float) $agente->diferencia
                                : null;

                        $claseDiferencia =
                            $diferencia === null
                                ? ''
                                : (
                                    $diferencia < 0
                                        ? 'diff-negative'
                                        : (
                                            $diferencia > 0
                                                ? 'diff-positive'
                                                : 'diff-zero'
                                        )
                                );
                    @endphp

                    <tr>
                        <td>
                            <strong>
                                {{ $agente->codigo_agente }}
                                — {{ $agente->nombre_negocio }}
                            </strong>
                        </td>

                        <td>
                            {{ $agente->nombre_propietario }}
                        </td>

                        <td>
                            {{ $agente->region_nombre }}
                        </td>

                        <td>
                            {{ $agente->ruta_codigo }}
                            — {{ $agente->ruta_nombre }}
                        </td>

                        <td>
                            {{ $nombrePromotor !== ''
                                ? $nombrePromotor
                                : (
                                    $agente->promotor_usuario
                                    ?? 'Sin asignación'
                                ) }}
                        </td>

                        <td>
                            <span class="badge {{ $claseEstado }}">
                                {{ $estadoOperativo }}
                            </span>
                        </td>

                        <td>
                            {{ $agente->estado_arqueo
                                ? str_replace(
                                    '_',
                                    ' ',
                                    $agente->estado_arqueo
                                )
                                : '—' }}
                        </td>

                        <td>
                            @if($diferencia === null)
                                —
                            @else
                                <span class="{{ $claseDiferencia }}">
                                    Q {{ number_format(
                                        abs($diferencia),
                                        2
                                    ) }}

                                    @if($diferencia < 0)
                                        Faltante
                                    @elseif($diferencia > 0)
                                        Sobrante
                                    @else
                                        Exacto
                                    @endif
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="8"
                            style="padding:35px;text-align:center;"
                        >
                            No se encontraron Agentes
                            para los filtros seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($agentes->hasPages())
        <div class="pagination">
            {{ $agentes->links() }}
        </div>
    @endif
</section>
@endsection
