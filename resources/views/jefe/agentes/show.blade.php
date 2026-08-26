@extends('layouts.jefe')

@section('title', 'Detalle del Agente')
@section('module-title', 'Agentes')

@push('styles')
<style>
    .page-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 15px;
        border: 1px solid #d5dfe5;
        border-radius: 10px;
        background: #ffffff;
        color: #31536e;
        font-size: 10px;
        font-weight: 800;
        text-decoration: none;
    }

    .btn-primary {
        border-color: #164c96;
        background: #164c96;
        color: #ffffff;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 20px;
    }

    .card {
        overflow: hidden;
        border: 1px solid #e0e8ee;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 24px rgba(20, 57, 83, .05);
    }

    .identity-header {
        padding: 27px 20px 23px;
        background: linear-gradient(
            145deg,
            #0b315f,
            #164c96
        );
        color: #ffffff;
        text-align: center;
    }

    .identity-avatar {
        width: 82px;
        height: 82px;
        display: grid;
        place-items: center;
        margin: 0 auto 14px;
        border: 4px solid rgba(255, 255, 255, .24);
        border-radius: 22px;
        background: rgba(255, 255, 255, .13);
        font-size: 24px;
        font-weight: 800;
    }

    .identity-header h3 {
        margin: 0;
        font-size: 18px;
    }

    .identity-header p {
        margin: 6px 0 0;
        color: rgba(255, 255, 255, .68);
        font-size: 10px;
    }

    .identity-body {
        padding: 17px;
    }

    .identity-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid #edf1f4;
    }

    .identity-row:last-child {
        border-bottom: 0;
    }

    .identity-row span {
        color: #738493;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .identity-row strong {
        max-width: 58%;
        color: #173b59;
        font-size: 11px;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .content-card + .content-card {
        margin-top: 20px;
    }

    .card-header {
        padding: 17px 19px;
        border-bottom: 1px solid #edf1f4;
        background: #fafcfd;
    }

    .card-header h3 {
        margin: 0;
        color: #0a3158;
        font-size: 15px;
    }

    .card-body {
        padding: 19px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .info-item {
        padding: 13px;
        border: 1px solid #e2e9ee;
        border-radius: 11px;
        background: #fbfcfd;
    }

    .info-item.full {
        grid-column: 1 / -1;
    }

    .info-item span {
        display: block;
        color: #758697;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .info-item strong {
        display: block;
        margin-top: 6px;
        color: #173b59;
        font-size: 11px;
        overflow-wrap: anywhere;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .stat-box {
        padding: 14px;
        border: 1px solid #e1e8ed;
        border-radius: 11px;
        background: #fbfcfd;
    }

    .stat-box span {
        display: block;
        color: #748596;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .stat-box strong {
        display: block;
        margin-top: 6px;
        color: #082d55;
        font-size: 21px;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table th,
    .history-table td {
        padding: 11px 12px;
        border-bottom: 1px solid #edf1f4;
        text-align: left;
        font-size: 9px;
    }

    .history-table th {
        background: #f7f9fb;
        color: #687b8b;
        font-size: 8px;
        text-transform: uppercase;
    }

    .badge {
        display: inline-flex;
        padding: 6px 9px;
        border-radius: 999px;
        background: #edf2f6;
        color: #50667a;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
    }

    @media (max-width: 950px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .info-grid,
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .info-item.full {
            grid-column: auto;
        }
    }
</style>
@endpush

@section('content')
@php
    $nombrePromotor = trim(
        ($agente->promotor_nombres ?? '')
        . ' '
        . ($agente->promotor_apellidos ?? '')
    );

    $iniciales = collect(
        preg_split('/\s+/', $agente->nombre_negocio)
    )
        ->filter()
        ->take(2)
        ->map(
            fn ($parte) => mb_strtoupper(
                mb_substr($parte, 0, 1)
            )
        )
        ->implode('');
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>Detalle del Agente</h2>

        <p>
            Información institucional, ubicación operativa,
            promotor asignado e historial reciente.
        </p>
    </div>

    <div class="page-actions">
        <a
            href="{{ route('jefe.agentes.index') }}"
            class="btn"
        >
            Regresar
        </a>

        <a
            href="{{ route('jefe.agentes.imprimir', $agente->id) }}"
            target="_blank"
            class="btn btn-primary"
        >
            Imprimir PDF
        </a>

        <a
            href="{{ url(
                '/jefe-agentes/arqueos-agentes?agente_id=' . $agente->id
            ) }}"
            class="btn btn-primary"
        >
            Ver todos los arqueos
        </a>
    </div>
</div>

<div class="profile-grid">
    <aside>
        <section class="card">
            <div class="identity-header">
                <div class="identity-avatar">
                    {{ $iniciales !== '' ? $iniciales : 'AG' }}
                </div>

                <h3>{{ $agente->nombre_negocio }}</h3>

                <p>
                    {{ $agente->codigo_agente }}
                </p>
            </div>

            <div class="identity-body">
                <div class="identity-row">
                    <span>Propietario</span>
                    <strong>
                        {{ $agente->nombre_propietario }}
                    </strong>
                </div>

                <div class="identity-row">
                    <span>Estado</span>
                    <strong>{{ $agente->estado }}</strong>
                </div>

                <div class="identity-row">
                    <span>Usuario</span>
                    <strong>
                        {{ $agente->agente_usuario
                            ?: 'Sin usuario asociado' }}
                    </strong>
                </div>

                <div class="identity-row">
                    <span>Ruta</span>
                    <strong>
                        {{ $agente->ruta_codigo }}
                        — {{ $agente->ruta_nombre }}
                    </strong>
                </div>

                <div class="identity-row">
                    <span>Región</span>
                    <strong>
                        {{ $agente->region_nombre }}
                    </strong>
                </div>
            </div>
        </section>
    </aside>

    <main>
        <section class="card content-card">
            <header class="card-header">
                <h3>Información institucional</h3>
            </header>

            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span>Código de agente</span>
                        <strong>{{ $agente->codigo_agente }}</strong>
                    </div>

                    <div class="info-item">
                        <span>Nombre del negocio</span>
                        <strong>{{ $agente->nombre_negocio }}</strong>
                    </div>

                    <div class="info-item">
                        <span>Propietario / receptor</span>
                        <strong>
                            {{ $agente->nombre_propietario }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span>Promotor asignado</span>
                        <strong>
                            {{ $nombrePromotor !== ''
                                ? $nombrePromotor
                                : (
                                    $agente->promotor_usuario
                                    ?: 'Sin promotor asignado'
                                ) }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span>Ruta</span>
                        <strong>
                            {{ $agente->ruta_codigo }}
                            — {{ $agente->ruta_nombre }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span>Región</span>
                        <strong>
                            {{ $agente->region_nombre }}
                        </strong>
                    </div>

                    <div class="info-item full">
                        <span>Dirección</span>
                        <strong>
                            {{ $agente->direccion ?: 'No registrada' }}
                        </strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="card content-card">
            <header class="card-header">
                <h3>Resumen de arqueos</h3>
            </header>

            <div class="card-body">
                <div class="stats-grid">
                    <div class="stat-box">
                        <span>Total registros</span>
                        <strong>
                            {{ (int) $resumen->total_arqueos }}
                        </strong>
                    </div>

                    <div class="stat-box">
                        <span>Arqueos del agente</span>
                        <strong>
                            {{ (int) $resumen->arqueos_agente }}
                        </strong>
                    </div>

                    <div class="stat-box">
                        <span>Arqueos de promotor</span>
                        <strong>
                            {{ (int) $resumen->arqueos_promotor }}
                        </strong>
                    </div>

                    <div class="stat-box">
                        <span>Anulados</span>
                        <strong>
                            {{ (int) $resumen->anulados }}
                        </strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="card content-card">
            <header class="card-header">
                <h3>Últimos arqueos</h3>
            </header>

            <div style="overflow-x:auto;">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Extemporáneo</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($ultimosArqueos as $arqueo)
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
                                    <span class="badge">
                                        {{ $arqueo->tipo === 'DIARIO_AGENTE'
                                            ? 'Agente'
                                            : 'Promotor' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge">
                                        {{ str_replace(
                                            '_',
                                            ' ',
                                            $arqueo->estado
                                        ) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $arqueo->fuera_fecha_ordinaria
                                        ? 'Sí'
                                        : 'No' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    Sin arqueos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
@endsection
