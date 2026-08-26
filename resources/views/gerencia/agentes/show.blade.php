@extends('layouts.gerencia')

@section('title', 'Detalle del Agente')
@section('module-title', 'Agentes')

@push('styles')
<style>
    .detail-grid{display:grid;grid-template-columns:360px minmax(0,1fr);gap:20px}
    .card{overflow:hidden;border:1px solid #e0e8ee;border-radius:18px;background:#fff;box-shadow:0 8px 24px rgba(20,57,83,.05)}
    .card-header{padding:18px 20px;border-bottom:1px solid #edf1f4;background:#fafcfd}
    .card-header h3{margin:0;color:#0a3158;font-size:15px}
    .card-header p{margin:5px 0 0;color:#82909a;font-size:10px}
    .card-body{padding:20px}
    .info-row{display:flex;justify-content:space-between;gap:15px;padding:12px 0;border-bottom:1px solid #edf1f4}
    .info-row:last-child{border-bottom:0}
    .info-row span{color:#748596;font-size:9px;font-weight:800;text-transform:uppercase}
    .info-row strong{color:#173b59;font-size:11px;text-align:right}
    .badge{display:inline-flex;padding:6px 9px;border-radius:999px;font-size:8px;font-weight:800;text-transform:uppercase}
    .badge.active{background:#eaf8ef;color:#1d7b4e}
    .badge.inactive{background:#fdecec;color:#b13c3c}
    .table-responsive{overflow-x:auto}
    table{width:100%;min-width:950px;border-collapse:collapse}
    th,td{padding:12px 13px;border-bottom:1px solid #edf1f4;text-align:left;font-size:10px}
    th{background:#f7f9fb;color:#687b8b;font-size:8px;text-transform:uppercase}
    .diff-negative{color:#b33a34;font-weight:800}
    .diff-positive{color:#16834f;font-weight:800}
    .diff-zero{color:#607586;font-weight:800}
    .pagination{padding:16px 18px}
    @media(max-width:1000px){.detail-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>{{ $agente->nombre_negocio }}</h2>
        <p>
            Código {{ $agente->codigo_agente }} ·
            Consulta de información e historial de arqueos.
        </p>
    </div>
</div>

<div class="detail-grid">
    <aside class="card">
        <header class="card-header">
            <h3>Información del Agente</h3>
        </header>

        <div class="card-body">
            @php
                $nombrePromotor = trim(
                    ($agente->promotor_nombres ?? '')
                    . ' '
                    . ($agente->promotor_apellidos ?? '')
                );
            @endphp

            <div class="info-row">
                <span>Código</span>
                <strong>{{ $agente->codigo_agente }}</strong>
            </div>

            <div class="info-row">
                <span>Negocio</span>
                <strong>{{ $agente->nombre_negocio }}</strong>
            </div>

            <div class="info-row">
                <span>Propietario</span>
                <strong>{{ $agente->nombre_propietario }}</strong>
            </div>

            <div class="info-row">
                <span>Dirección</span>
                <strong>{{ $agente->direccion }}</strong>
            </div>

            <div class="info-row">
                <span>Región</span>
                <strong>{{ $agente->region_nombre }}</strong>
            </div>

            <div class="info-row">
                <span>Ruta</span>
                <strong>
                    {{ $agente->ruta_codigo }}
                    — {{ $agente->ruta_nombre }}
                </strong>
            </div>

            <div class="info-row">
                <span>Promotor</span>
                <strong>
                    {{ $nombrePromotor !== ''
                        ? $nombrePromotor
                        : ($agente->promotor_usuario ?? 'Sin asignación') }}
                </strong>
            </div>

            <div class="info-row">
                <span>Estado</span>
                <strong>
                    <span
                        class="badge {{
                            $agente->estado === 'ACTIVO'
                                ? 'active'
                                : 'inactive'
                        }}"
                    >
                        {{ $agente->estado }}
                    </span>
                </strong>
            </div>
        </div>
    </aside>

    <section class="card">
        <header class="card-header">
            <h3>Historial de Arqueos</h3>
            <p>
                Incluye arqueos propios del Agente
                y visitas realizadas por Promotores.
            </p>
        </header>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Responsable</th>
                        <th>Diferencia</th>
                        <th>Extemporáneo</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($arqueos as $arqueo)
                        @php
                            $responsable = trim(
                                ($arqueo->responsable_nombres ?? '')
                                . ' '
                                . ($arqueo->responsable_apellidos ?? '')
                            );

                            $diferencia = (float) $arqueo->diferencia;

                            $claseDiferencia =
                                $diferencia < 0
                                    ? 'diff-negative'
                                    : (
                                        $diferencia > 0
                                            ? 'diff-positive'
                                            : 'diff-zero'
                                    );
                        @endphp

                        <tr>
                            <td><strong>{{ $arqueo->numero_arqueo }}</strong></td>

                            <td>
                                {{ \Carbon\Carbon::parse(
                                    $arqueo->fecha_arqueo
                                )->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ $arqueo->tipo === 'DIARIO_AGENTE'
                                    ? 'Agente'
                                    : (
                                        $arqueo->tipo === 'VISITA_PROMOTOR'
                                            ? 'Promotor'
                                            : str_replace(
                                                '_',
                                                ' ',
                                                $arqueo->tipo
                                            )
                                    ) }}
                            </td>

                            <td>
                                {{ str_replace(
                                    '_',
                                    ' ',
                                    $arqueo->estado
                                ) }}
                            </td>

                            <td>
                                {{ $responsable !== ''
                                    ? $responsable
                                    : ($arqueo->responsable_usuario ?? '—') }}
                            </td>

                            <td>
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
                            </td>

                            <td>
                                {{ $arqueo->fuera_fecha_ordinaria
                                    ? 'Sí'
                                    : 'No' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="7"
                                style="padding:35px;text-align:center;"
                            >
                                Este Agente no tiene arqueos registrados.
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
</div>
@endsection
