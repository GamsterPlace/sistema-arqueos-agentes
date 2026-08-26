@extends('layouts.gerencia')

@section('title', 'Promotores')
@section('module-title', 'Promotores')

@push('styles')
<style>
    .summary-grid{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:14px;
        margin-bottom:22px;
    }

    .summary-card,
    .filters-card,
    .table-card{
        border:1px solid #e0e8ee;
        border-radius:18px;
        background:#fff;
        box-shadow:0 8px 24px rgba(20,57,83,.05);
    }

    .summary-card{padding:17px}

    .summary-card span{
        display:block;
        color:#758697;
        font-size:8px;
        font-weight:800;
        text-transform:uppercase;
    }

    .summary-card strong{
        display:block;
        margin-top:8px;
        color:#082d55;
        font-size:23px;
    }

    .summary-card.success{
        border-color:#c5e5d1;
        background:#f7fcf9;
    }

    .summary-card.warning{
        border-color:#efd99f;
        background:#fffdf6;
    }

    .filters-card{
        padding:18px;
        margin-bottom:20px;
    }

    .filters-grid{
        display:grid;
        grid-template-columns:minmax(260px,1fr) 220px;
        gap:10px;
    }

    .form-control{
        width:100%;
        min-height:42px;
        padding:0 12px;
        border:1px solid #ced9e1;
        border-radius:10px;
        background:#fff;
        color:#30485b;
        box-sizing:border-box;
    }

    .filters-actions{
        display:flex;
        justify-content:flex-end;
        gap:10px;
        margin-top:13px;
    }

    .btn{
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

    .btn-primary{background:#164c96;color:#fff}
    .btn-secondary{background:#edf2f5;color:#3d596f}

    .table-card{overflow:hidden}

    .table-header{
        padding:18px 20px;
        border-bottom:1px solid #edf1f4;
        background:#fafcfd;
    }

    .table-header h3{
        margin:0;
        color:#0a3158;
        font-size:15px;
    }

    .table-header p{
        margin:5px 0 0;
        color:#82909a;
        font-size:10px;
    }

    .table-responsive{overflow-x:auto}

    table{
        width:100%;
        min-width:1050px;
        border-collapse:collapse;
    }

    th,
    td{
        padding:12px 13px;
        border-bottom:1px solid #edf1f4;
        text-align:left;
        vertical-align:middle;
        font-size:10px;
    }

    th{
        background:#f7f9fb;
        color:#687b8b;
        font-size:8px;
        text-transform:uppercase;
    }

    .badge{
        display:inline-flex;
        padding:6px 9px;
        border-radius:999px;
        font-size:8px;
        font-weight:800;
        text-transform:uppercase;
    }

    .badge.active{
        background:#eaf8ef;
        color:#1d7b4e;
    }

    .badge.inactive{
        background:#fdecec;
        color:#b13c3c;
    }

    .action-link{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-height:34px;
        padding:0 10px;
        border:1px solid #d6e0e6;
        border-radius:9px;
        background:#fff;
        color:#31536e;
        font-size:9px;
        font-weight:800;
        text-decoration:none;
    }

    .pagination{padding:16px 18px}

    @media(max-width:900px){
        .summary-grid{
            grid-template-columns:repeat(2,1fr);
        }
    }

    @media(max-width:700px){
        .summary-grid,
        .filters-grid{
            grid-template-columns:1fr;
        }

        .filters-actions{
            flex-direction:column;
        }

        .btn{width:100%}
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Listado de Promotores</h2>

        <p>
            Consulte Promotores, Rutas asignadas,
            Agentes bajo cobertura y actividad de arqueos.
        </p>
    </div>
</div>

<section class="summary-grid">
    <article class="summary-card">
        <span>Total Promotores</span>
        <strong>{{ $totalPromotores }}</strong>
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
        <span>Arqueos hoy</span>
        <strong>{{ $arqueosPromotorHoy }}</strong>
    </article>
</section>

<section class="filters-card">
    <form
        method="GET"
        action="{{ route('gerencia.promotores.index') }}"
    >
        <div class="filters-grid">
            <input
                type="text"
                name="buscar"
                class="form-control"
                value="{{ $buscar }}"
                placeholder="Nombre o usuario del Promotor"
            >

            <select
                name="estado"
                class="form-control"
            >
                <option value="">
                    Todos los estados
                </option>

                <option
                    value="ACTIVO"
                    @selected($estado === 'ACTIVO')
                >
                    Activos
                </option>

                <option
                    value="INACTIVO"
                    @selected($estado === 'INACTIVO')
                >
                    Inactivos
                </option>

                <option
                    value="BLOQUEADO"
                    @selected($estado === 'BLOQUEADO')
                >
                    Bloqueados
                </option>
            </select>
        </div>

        <div class="filters-actions">
            <a
                href="{{ route('gerencia.promotores.index') }}"
                class="btn btn-secondary"
            >
                Limpiar
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Aplicar filtros
            </button>
        </div>
    </form>
</section>

<section class="table-card">
    <header class="table-header">
        <h3>Promotores registrados</h3>

        <p>
            Información consolidada de consulta para Gerencia.
        </p>
    </header>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Promotor</th>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Rutas Asignadas</th>
                    <th>Agentes Asignados</th>
                    <th>Total Arqueos</th>
                    <th>Arqueos Hoy</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody>
                @forelse($promotores as $promotor)
                    @php
                        $nombreCompleto = trim(
                            ($promotor->nombres ?? '')
                            . ' '
                            . ($promotor->apellidos ?? '')
                        );
                    @endphp

                    <tr>
                        <td>
                            <strong>
                                {{ $nombreCompleto !== ''
                                    ? $nombreCompleto
                                    : $promotor->usuario }}
                            </strong>
                        </td>

                        <td>
                            {{ $promotor->usuario }}
                        </td>

                        <td>
                            <span
                                class="badge {{
                                    $promotor->estado === 'ACTIVO'
                                        ? 'active'
                                        : 'inactive'
                                }}"
                            >
                                {{ $promotor->estado }}
                            </span>
                        </td>

                        <td>
                            {{ (int) $promotor->rutas_asignadas }}
                        </td>

                        <td>
                            {{ (int) $promotor->agentes_asignados }}
                        </td>

                        <td>
                            {{ (int) $promotor->total_arqueos }}
                        </td>

                        <td>
                            {{ (int) $promotor->arqueos_hoy }}
                        </td>

                        <td>
                            <a
                                href="{{ route(
                                    'gerencia.promotores.show',
                                    $promotor->id
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
                            colspan="8"
                            style="padding:35px;text-align:center;"
                        >
                            No se encontraron Promotores.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($promotores->hasPages())
        <div class="pagination">
            {{ $promotores->links() }}
        </div>
    @endif
</section>
@endsection
