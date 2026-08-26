@extends('layouts.jefe')

@section('title', 'Gestión de Regiones')
@section('module-title', 'Gestión de Regiones')

@push('styles')
<style>
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .summary-card,
    .filters-card,
    .region-card {
        border: 1px solid #e0e8ee;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(20, 57, 83, .05);
    }

    .summary-card {
        padding: 18px;
    }

    .summary-card span {
        display: block;
        color: #768692;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .summary-card strong {
        display: block;
        margin-top: 8px;
        color: #082d55;
        font-size: 25px;
    }

    .summary-card.warning {
        border-color: #efd99f;
        background: #fffdf6;
    }

    .filters-card {
        margin-bottom: 20px;
        padding: 18px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) 190px;
        gap: 11px;
    }

    .form-control {
        width: 100%;
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid #ced9e1;
        border-radius: 10px;
        background: #fff;
        color: #30485b;
        outline: none;
    }

    .filters-actions,
    .page-actions,
    .region-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filters-actions {
        justify-content: flex-end;
        margin-top: 13px;
    }

    .btn,
    .action-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .btn {
        min-height: 40px;
        padding: 0 15px;
        border: 0;
        border-radius: 10px;
        font-size: 10px;
    }

    .btn-primary {
        background: #164c96;
        color: #fff;
    }

    .btn-secondary {
        background: #edf2f5;
        color: #3d596f;
    }

    .btn-success {
        background: #16834f;
        color: #fff;
    }

    .regions-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .region-card {
        overflow: hidden;
    }

    .region-card-header {
        padding: 17px 18px;
        border-bottom: 1px solid #edf1f4;
        background: #fafcfd;
    }

    .region-card-header h3 {
        margin: 0;
        color: #0a3158;
        font-size: 15px;
    }

    .region-card-body {
        padding: 17px;
    }

    .region-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .region-stat {
        padding: 12px;
        border: 1px solid #e3e9ee;
        border-radius: 11px;
        background: #f9fbfc;
    }

    .region-stat span {
        display: block;
        color: #748596;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .region-stat strong {
        display: block;
        margin-top: 6px;
        color: #082d55;
        font-size: 20px;
    }

    .region-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 15px;
        padding-top: 14px;
        border-top: 1px solid #edf1f4;
    }

    .badge {
        display: inline-flex;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .badge.active {
        background: #eaf8ef;
        color: #1d7b4e;
    }

    .badge.inactive {
        background: #fdecec;
        color: #b13c3c;
    }

    .action-link {
        min-height: 34px;
        padding: 0 10px;
        border: 1px solid #d5dfe5;
        border-radius: 9px;
        background: #fff;
        color: #31536e;
        font-size: 9px;
        white-space: nowrap;
    }

    .action-link.primary {
        border-color: #cbdceb;
        background: #edf5fb;
        color: #164c96;
    }

    .action-link.warning {
        border-color: #eed8a6;
        background: #fff9e9;
        color: #8a6511;
    }

    .action-link.success {
        border-color: #bee2cc;
        background: #effaf3;
        color: #176c40;
    }

    .empty-state {
        grid-column: 1 / -1;
        padding: 44px 20px;
        border: 1px solid #e0e8ee;
        border-radius: 18px;
        background: #fff;
        color: #7d8b95;
        text-align: center;
        font-size: 11px;
    }

    .pagination {
        margin-top: 18px;
    }

    .modal-backdrop {
        position: fixed;
        z-index: 100;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(5, 29, 52, .55);
        backdrop-filter: blur(3px);
    }

    .modal-backdrop.open {
        display: flex;
    }

    .modal-card {
        width: 100%;
        max-width: 520px;
        overflow: hidden;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 24px 70px rgba(0, 0, 0, .22);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 18px 20px;
        border-bottom: 1px solid #edf1f4;
    }

    .modal-header h3 {
        margin: 0;
        color: #0a3158;
        font-size: 16px;
    }

    .modal-close {
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 10px;
        background: #eef3f6;
        color: #4b6578;
        cursor: pointer;
    }

    .modal-body {
        padding: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #617586;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 20px 20px;
    }

    @media (max-width: 1100px) {
        .summary-grid,
        .regions-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .summary-grid,
        .regions-grid,
        .filters-grid {
            grid-template-columns: 1fr;
        }

        .filters-actions,
        .page-actions {
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
        <h2>Gestión de Regiones</h2>
        <p>Administre las regiones utilizadas para organizar rutas y agentes del sistema.</p>
    </div>

    <div class="page-actions">
        <button type="button" class="btn btn-success" onclick="abrirCrearRegion()">
            Nueva Región
        </button>
    </div>
</div>

<section class="summary-grid">
    <article class="summary-card"><span>Total regiones</span><strong>{{ $totalRegiones }}</strong></article>
    <article class="summary-card"><span>Regiones activas</span><strong>{{ $regionesActivas }}</strong></article>
    <article class="summary-card"><span>Regiones inactivas</span><strong>{{ $regionesInactivas }}</strong></article>
    <article class="summary-card warning"><span>Regiones sin rutas</span><strong>{{ $regionesSinRutas }}</strong></article>
</section>

<section class="filters-card">
    <form method="GET" action="{{ route('jefe.regiones.index') }}">
        <div class="filters-grid">
            <input type="text" name="buscar" class="form-control" value="{{ $buscar }}" placeholder="Buscar región">

            <select name="estado" class="form-control">
                <option value="">Todos los estados</option>
                <option value="ACTIVA" @selected($estado === 'ACTIVA')>Activas</option>
                <option value="INACTIVA" @selected($estado === 'INACTIVA')>Inactivas</option>
            </select>
        </div>

        <div class="filters-actions">
            <a href="{{ route('jefe.regiones.index') }}" class="btn btn-secondary">Limpiar</a>
            <button type="submit" class="btn btn-primary">Aplicar filtros</button>
        </div>
    </form>
</section>

<section class="regions-grid">
    @forelse($regiones as $region)
        @php
            $pendientesHoy = max(
                0,
                (int) $region->agentes_activos
                - (int) $region->agentes_arqueados_hoy
            );
        @endphp

        <article class="region-card">
            <header class="region-card-header">
                <h3>{{ $region->nombre }}</h3>
            </header>

            <div class="region-card-body">
                <div class="region-stats">
                    <div class="region-stat">
                        <span>Total rutas</span>
                        <strong>{{ $region->total_rutas }}</strong>
                    </div>

                    <div class="region-stat">
                        <span>Rutas activas</span>
                        <strong>{{ $region->rutas_activas }}</strong>
                    </div>

                    <div class="region-stat">
                        <span>Agentes activos</span>
                        <strong>{{ $region->agentes_activos }}</strong>
                    </div>

                    <div class="region-stat">
                        <span>Pendientes hoy</span>
                        <strong>{{ $pendientesHoy }}</strong>
                    </div>
                </div>

                <div class="region-footer">
                    <span class="badge {{ $region->estado ? 'active' : 'inactive' }}">
                        {{ $region->estado ? 'ACTIVA' : 'INACTIVA' }}
                    </span>

                    <div class="region-actions">
                        <button
                            type="button"
                            class="action-link primary"
                            onclick='abrirEditarRegion(@json([
                                "id" => $region->id,
                                "nombre" => $region->nombre,
                            ]))'
                        >
                            Editar
                        </button>

                        <a
                            href="{{ route('jefe.agentes-region.index', ['region_id' => $region->id]) }}"
                            class="action-link"
                        >
                            Ver agentes
                        </a>

                        <form
                            method="POST"
                            action="{{ route('jefe.regiones.estado', $region->id) }}"
                            onsubmit="return confirm('{{ $region->estado ? '¿Desea desactivar esta región?' : '¿Desea activar esta región?' }}');"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="action-link {{ $region->estado ? 'warning' : 'success' }}"
                            >
                                {{ $region->estado ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </article>
    @empty
        <div class="empty-state">No se encontraron regiones.</div>
    @endforelse
</section>

@if($regiones->hasPages())
    <div class="pagination">{{ $regiones->links() }}</div>
@endif

<div class="modal-backdrop" id="modalRegion">
    <div class="modal-card">
        <form method="POST" id="formRegion" action="{{ route('jefe.regiones.store') }}">
            @csrf
            <input type="hidden" name="_method" id="regionMethod" value="POST">

            <header class="modal-header">
                <h3 id="modalRegionTitulo">Nueva Región</h3>
                <button type="button" class="modal-close" onclick="cerrarModalRegion()">×</button>
            </header>

            <div class="modal-body">
                <div class="form-group">
                    <label for="regionNombre">Nombre de la Región</label>
                    <input type="text" name="nombre" id="regionNombre" class="form-control" required maxlength="150">
                </div>
            </div>

            <footer class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModalRegion()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </footer>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modalRegion = document.getElementById('modalRegion');
    const formRegion = document.getElementById('formRegion');
    const modalRegionTitulo = document.getElementById('modalRegionTitulo');
    const regionMethod = document.getElementById('regionMethod');
    const regionNombre = document.getElementById('regionNombre');
    const storeRegionUrl = @json(route('jefe.regiones.store'));
    const updateRegionBaseUrl = @json(url('/jefe-agentes/regiones'));

    function abrirCrearRegion() {
        formRegion.reset();
        formRegion.action = storeRegionUrl;
        regionMethod.value = 'POST';
        modalRegionTitulo.textContent = 'Nueva Región';
        modalRegion.classList.add('open');
    }

    function abrirEditarRegion(region) {
        formRegion.action = updateRegionBaseUrl + '/' + region.id;
        regionMethod.value = 'PUT';
        modalRegionTitulo.textContent = 'Editar Región';
        regionNombre.value = region.nombre ?? '';
        modalRegion.classList.add('open');
    }

    function cerrarModalRegion() {
        modalRegion.classList.remove('open');
    }

    modalRegion.addEventListener('click', function(event) {
        if (event.target === modalRegion) {
            cerrarModalRegion();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalRegion();
        }
    });
</script>
@endpush
