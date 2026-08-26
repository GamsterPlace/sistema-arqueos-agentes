@extends('layouts.gerencia')

@section('title', 'Detalle del Promotor')
@section('module-title', 'Promotores')

@push('styles')
<style>
    .detail-grid{
        display:grid;
        grid-template-columns:360px minmax(0,1fr);
        gap:20px;
        margin-bottom:20px;
    }

    .card{
        overflow:hidden;
        border:1px solid #e0e8ee;
        border-radius:18px;
        background:#fff;
        box-shadow:0 8px 24px rgba(20,57,83,.05);
    }

    .card-header{
        padding:18px 20px;
        border-bottom:1px solid #edf1f4;
        background:#fafcfd;
    }

    .card-header h3{
        margin:0;
        color:#0a3158;
        font-size:15px;
    }

    .card-header p{
        margin:5px 0 0;
        color:#82909a;
        font-size:10px;
    }

    .card-body{padding:20px}

    .info-row{
        display:flex;
        justify-content:space-between;
        gap:15px;
        padding:12px 0;
        border-bottom:1px solid #edf1f4;
    }

    .info-row:last-child{border-bottom:0}

    .info-row span{
        color:#748596;
        font-size:9px;
        font-weight:800;
        text-transform:uppercase;
    }

    .info-row strong{
        color:#173b59;
        font-size:11px;
        text-align:right;
    }

    .route-list{
        display:grid;
        gap:10px;
    }

    .route-item{
        padding:13px;
        border:1px solid #e2e9ee;
        border-radius:12px;
        background:#fbfcfd;
    }

    .route-item strong{
        color:#173b59;
        font-size:11px;
    }

    .route-item span{
        display:block;
        margin-top:5px;
        color:#718493;
        font-size:9px;
    }

    .table-responsive{overflow-x:auto}

    table{
        width:100%;
        min-width:950px;
        border-collapse:collapse;
    }

    th,
    td{
        padding:12px 13px;
        border-bottom:1px solid #edf1f4;
        text-align:left;
        font-size:10px;
    }

    th{
        background:#f7f9fb;
        color:#687b8b;
        font-size:8px;
        text-transform:uppercase;
    }

    .diff-negative{color:#b33a34;font-weight:800}
    .diff-positive{color:#16834f;font-weight:800}
    .diff-zero{color:#607586;font-weight:800}

    .pagination{padding:16px 18px}

    @media(max-width:1000px){
        .detail-grid{
            grid-template-columns:1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $nombreCompleto = trim(
        ($promotor->nombres ?? '')
        . ' '
        . ($promotor->apellidos ?? '')
    );
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>
            {{ $nombreCompleto !== ''
                ? $nombreCompleto
                : $promotor->usuario }}
        </h2>

        <p>
            Consulta de asignaciones y actividad del Promotor.
        </p>
    </div>
</div>

<div class="detail-grid">
    <aside class="card">
        <header class="card-header">
            <h3>Información del Promotor</h3>
        </header>

        <div class="card-body">
            <div class="info-row">
                <span>Usuario</span>
                <strong>{{ $promotor->usuario }}</strong>
            </div>

            <div class="info-row">
                <span>Nombres</span>
                <strong>{{ $promotor->nombres ?: '—' }}</strong>
            </div>

            <div class="info-row">
                <span>Apellidos</span>
                <strong>{{ $promotor->apellidos ?: '—' }}</strong>
            </div>

            <div class="info-row">
                <span>Estado</span>
                <strong>{{ $promotor->estado }}</strong>
            </div>
        </div>
    </aside>

    <section class="card">
        <header class="card-header">
            <h3>Rutas Asignadas</h3>

            <p>
                Asignaciones activas vigentes a la fecha.
            </p>
        </header>

        <div class="card-body">
            <div class="route-list">
                @forelse($rutas as $ruta)
                    <article class="route-item">
                        <strong>
                            {{ $ruta->ruta_codigo }}
                            — {{ $ruta->ruta_nombre }}
                        </strong>

                        <span>
                            Región: {{ $ruta->region_nombre }}
                        </span>

                        <span>
                            Agentes activos:
                            {{ (int) $ruta->agentes_activos }}
                        </span>

                        <span>
                            Desde:
                            {{ \Carbon\Carbon::parse(
                                $ruta->fecha_inicio
                            )->format('d/m/Y') }}

                            @if($ruta->fecha_fin)
                                · Hasta:
                                {{ \Carbon\Carbon::parse(
                                    $ruta->fecha_fin
                                )->format('d/m/Y') }}
                            @endif
                        </span>
                    </article>
                @empty
                    <p>
                        El Promotor no tiene Rutas activas asignadas.
                    </p>
                @endforelse
            </div>
        </div>
    </section>
</div>

<section class="card">
    <header class="card-header">
        <h3>Arqueos Realizados</h3>

        <p>
            Historial de visitas de arqueo realizadas por este Promotor.
        </p>
    </header>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Fecha</th>
                    <th>Agente</th>
                    <th>Región</th>
                    <th>Ruta</th>
                    <th>Estado</th>
                    <th>Diferencia</th>
                </tr>
            </thead>

            <tbody>
                @forelse($arqueos as $arqueo)
                    @php
                        $diferencia = (float) $arqueo->diferencia;

                        $clase =
                            $diferencia < 0
                                ? 'diff-negative'
                                : (
                                    $diferencia > 0
                                        ? 'diff-positive'
                                        : 'diff-zero'
                                );
                    @endphp

                    <tr>
                        <td>
                            <strong>
                                {{ $arqueo->numero_arqueo }}
                            </strong>
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse(
                                $arqueo->fecha_arqueo
                            )->format('d/m/Y') }}
                        </td>

                        <td>
                            {{ $arqueo->codigo_agente }}
                            — {{ $arqueo->nombre_negocio }}
                        </td>

                        <td>
                            {{ $arqueo->region_nombre ?? '—' }}
                        </td>

                        <td>
                            {{ $arqueo->ruta_nombre ?? '—' }}
                        </td>

                        <td>
                            {{ str_replace(
                                '_',
                                ' ',
                                $arqueo->estado
                            ) }}
                        </td>

                        <td>
                            <span class="{{ $clase }}">
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="7"
                            style="padding:35px;text-align:center;"
                        >
                            El Promotor no tiene arqueos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($arqueos->hasPages())
        <div class="pagination">
            {{ $arqueos->links() }}
        </div>
    @endif
</section>
@endsection
