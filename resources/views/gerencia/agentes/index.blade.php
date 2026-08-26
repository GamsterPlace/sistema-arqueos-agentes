@extends('layouts.gerencia')

@section('title', 'Agentes')
@section('module-title', 'Agentes')

@push('styles')
<style>
    .summary-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:22px}
    .summary-card,.filters-card,.table-card{border:1px solid #e0e8ee;border-radius:18px;background:#fff;box-shadow:0 8px 24px rgba(20,57,83,.05)}
    .summary-card{padding:17px}
    .summary-card span{display:block;color:#758697;font-size:8px;font-weight:800;text-transform:uppercase}
    .summary-card strong{display:block;margin-top:8px;color:#082d55;font-size:23px}
    .summary-card.success{border-color:#c5e5d1;background:#f7fcf9}
    .summary-card.warning{border-color:#efd99f;background:#fffdf6}
    .filters-card{padding:18px;margin-bottom:20px}
    .filters-grid{display:grid;grid-template-columns:minmax(250px,1fr) 190px 220px 220px;gap:10px}
    .form-control{width:100%;min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;background:#fff;color:#30485b;box-sizing:border-box}
    .filters-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:13px}
    .btn{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border:0;border-radius:10px;font-size:10px;font-weight:800;text-decoration:none;cursor:pointer}
    .btn-primary{background:#164c96;color:#fff}
    .btn-secondary{background:#edf2f5;color:#3d596f}
    .table-card{overflow:hidden}
    .table-header{padding:18px 20px;border-bottom:1px solid #edf1f4;background:#fafcfd}
    .table-header h3{margin:0;color:#0a3158;font-size:15px}
    .table-header p{margin:5px 0 0;color:#82909a;font-size:10px}
    .table-responsive{overflow-x:auto}
    table{width:100%;min-width:1250px;border-collapse:collapse}
    th,td{padding:12px 13px;border-bottom:1px solid #edf1f4;text-align:left;vertical-align:middle;font-size:10px}
    th{background:#f7f9fb;color:#687b8b;font-size:8px;text-transform:uppercase}
    .badge{display:inline-flex;padding:6px 9px;border-radius:999px;font-size:8px;font-weight:800;text-transform:uppercase}
    .badge.active{background:#eaf8ef;color:#1d7b4e}
    .badge.inactive{background:#fdecec;color:#b13c3c}
    .action-link{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:0 10px;border:1px solid #d6e0e6;border-radius:9px;background:#fff;color:#31536e;font-size:9px;font-weight:800;text-decoration:none}
    .pagination{padding:16px 18px}
    @media(max-width:1100px){.summary-grid{grid-template-columns:repeat(2,1fr)}.filters-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:700px){.summary-grid,.filters-grid{grid-template-columns:1fr}.filters-actions{flex-direction:column}.btn{width:100%}}
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Listado de Agentes</h2>
        <p>
            Consulte la información general de los Agentes,
            su ubicación operativa, Promotor asignado y actividad de arqueos.
        </p>
    </div>
</div>

<section class="summary-grid">
    <article class="summary-card">
        <span>Total Agentes</span>
        <strong>{{ $totalAgentes }}</strong>
    </article>

    <article class="summary-card success">
        <span>Activos</span>
        <strong>{{ $totalActivos }}</strong>
    </article>

    <article class="summary-card warning">
        <span>Inactivos</span>
        <strong>{{ $totalInactivos }}</strong>
    </article>

    <article class="summary-card success">
        <span>Con arqueo hoy</span>
        <strong>{{ $conArqueoHoy }}</strong>
    </article>
</section>

<section class="filters-card">
    <form method="GET" action="{{ route('gerencia.agentes.index') }}">
        <div class="filters-grid">
            <input
                type="text"
                name="buscar"
                class="form-control"
                value="{{ $buscar }}"
                placeholder="Código, negocio, propietario, ruta o región"
            >

            <select name="estado" class="form-control">
                <option value="">Todos los estados</option>
                <option value="ACTIVO" @selected($estado === 'ACTIVO')>
                    Activos
                </option>
                <option value="INACTIVO" @selected($estado === 'INACTIVO')>
                    Inactivos
                </option>
            </select>

            <select name="region_id" class="form-control">
                <option value="">Todas las Regiones</option>

                @foreach($regiones as $region)
                    <option
                        value="{{ $region->id }}"
                        @selected($regionId === (int) $region->id)
                    >
                        {{ $region->nombre }}
                    </option>
                @endforeach
            </select>

            <select name="ruta_id" class="form-control">
                <option value="">Todas las Rutas</option>

                @foreach($rutas as $ruta)
                    <option
                        value="{{ $ruta->id }}"
                        @selected($rutaId === (int) $ruta->id)
                    >
                        {{ $ruta->codigo }} — {{ $ruta->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filters-actions">
            <a href="{{ route('gerencia.agentes.index') }}" class="btn btn-secondary">
                Limpiar
            </a>

            <button type="submit" class="btn btn-primary">
                Aplicar Filtros
            </button>
        </div>
    </form>
</section>

<section class="table-card">
    <header class="table-header">
        <h3>Agentes registrados</h3>
        <p>
            Información de consulta. Gerencia no modifica
            la configuración de los Agentes.
        </p>
    </header>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Negocio</th>
                    <th>Propietario</th>
                    <th>Región</th>
                    <th>Ruta</th>
                    <th>Promotor</th>
                    <th>Estado</th>
                    <th>Total Arqueos</th>
                    <th>Último Arqueo</th>
                    <th>Hoy</th>
                    <th>Acción</th>
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
                    @endphp

                    <tr>
                        <td><strong>{{ $agente->codigo_agente }}</strong></td>
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
                                : ($agente->promotor_usuario ?? 'Sin asignación') }}
                        </td>
                        <td>
                            <span
                                class="badge {{
                                    $agente->estado === 'ACTIVO'
                                        ? 'active'
                                        : 'inactive'
                                }}"
                            >
                                {{ $agente->estado }}
                            </span>
                        </td>
                        <td>{{ (int) $agente->total_arqueos }}</td>
                        <td>
                            {{ $agente->ultimo_arqueo
                                ? \Carbon\Carbon::parse(
                                    $agente->ultimo_arqueo
                                )->format('d/m/Y')
                                : '—' }}
                        </td>
                        <td>
                            {{ (int) $agente->arqueo_hoy > 0
                                ? 'Realizado'
                                : 'Pendiente' }}
                        </td>
                        <td>
                            <a
                                href="{{ route(
                                    'gerencia.agentes.show',
                                    $agente->id
                                ) }}"
                                class="action-link"
                            >
                                Ver detalle
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="11"
                            style="padding:35px;text-align:center;"
                        >
                            No se encontraron Agentes.
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
