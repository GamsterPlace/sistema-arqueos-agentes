@extends('layouts.promotor')

@section('title', 'Arqueos Fuera de Tiempo')
@section('module-title', 'Arqueos Fuera de Tiempo')

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

    .content-grid {
        display: grid;
        grid-template-columns: 390px minmax(0, 1fr);
        gap: 20px;
    }

    .card {
        overflow: hidden;
        border: 1px solid #dfe7ed;
        border-radius: 16px;
        background: #ffffff;
    }

    .card-header {
        padding: 16px 18px;
        border-bottom: 1px solid #e7edf1;
        background: #f8fafb;
    }

    .card-header h3 {
        margin: 0;
        color: #173f66;
        font-size: 16px;
        font-weight: 800;
    }

    .card-body {
        padding: 18px;
    }

    .form-group + .form-group {
        margin-top: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 7px;
        color: #65788a;
        font-size: 12px;
        font-weight: 800;
    }

    .form-control {
        width: 100%;
        min-height: 44px;
        padding: 0 13px;
        border: 1px solid #ced9e1;
        border-radius: 10px;
        background: #ffffff;
        color: #2f4659;
        outline: none;
    }

    textarea.form-control {
        min-height: 115px;
        padding-top: 11px;
        resize: vertical;
    }

    .form-control:focus {
        border-color: #2b72b8;
        box-shadow: 0 0 0 3px rgba(43, 114, 184, .12);
    }

    .field-error {
        display: block;
        margin-top: 6px;
        color: #a93b35;
        font-size: 12px;
        font-weight: 700;
    }

    .form-help {
        display: block;
        margin-top: 6px;
        color: #7a8b99;
        font-size: 11px;
        line-height: 1.45;
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
        width: 100%;
        margin-top: 18px;
        background: #174f8a;
        color: #ffffff;
    }

    .btn-secondary {
        background: #eef3f7;
        color: #38556d;
    }

    .btn-danger {
        min-height: 35px;
        background: #fff0ef;
        color: #9a302a;
    }

    .filters {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) 190px auto auto;
        gap: 10px;
        padding: 16px 18px;
        border-bottom: 1px solid #e7edf1;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table th,
    .history-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #edf1f4;
        text-align: left;
        vertical-align: middle;
    }

    .history-table th {
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
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .badge-pending {
        background: #fff3d5;
        color: #8a6100;
    }

    .badge-used {
        background: #e8f7ee;
        color: #247048;
    }

    .badge-cancelled {
        background: #ffe8e6;
        color: #a93b35;
    }

    .reason {
        max-width: 310px;
        color: #50667a;
        line-height: 1.45;
    }

    .empty-state {
        padding: 42px;
        color: #758697;
        text-align: center;
    }

    .pagination-wrapper {
        padding: 16px;
    }

    @media (max-width: 1050px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .filters {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <div class="page-title">
        <h2>Arqueos Fuera de Tiempo</h2>

        <p>
            Autorice a un agente de sus rutas asignadas para realizar
            el arqueo correspondiente a una fecha anterior.
        </p>
    </div>
</div>

<div class="content-grid">
    <section class="card">
        <div class="card-header">
            <h3>Nueva habilitación</h3>
        </div>

        <div class="card-body">
            <form
                method="POST"
                action="{{ route(
                    'promotor.habilitaciones-atrasadas.store'
                ) }}"
            >
                @csrf

                <div class="form-group">
                    <label for="agente_id">
                        Agente
                    </label>

                    <select
                        id="agente_id"
                        name="agente_id"
                        class="form-control"
                        required
                    >
                        <option value="">
                            Seleccione un agente
                        </option>

                        @foreach ($agentesAsignados as $agente)
                            <option
                                value="{{ $agente->id }}"
                                @selected(
                                    old('agente_id') == $agente->id
                                )
                            >
                                {{ $agente->codigo_agente }}
                                — {{ $agente->nombre_negocio }}
                                — {{ $agente->ruta_nombre }}
                            </option>
                        @endforeach
                    </select>

                    @error('agente_id')
                        <span class="field-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="fecha_autorizada">
                        Fecha que se habilitará
                    </label>

                    <input
                        type="date"
                        id="fecha_autorizada"
                        name="fecha_autorizada"
                        class="form-control"
                        value="{{ old('fecha_autorizada') }}"
                        max="{{ today()->subDay()->format('Y-m-d') }}"
                        required
                    >

                    <span class="form-help">
                        Solo puede seleccionar una fecha anterior al día de hoy.
                    </span>

                    @error('fecha_autorizada')
                        <span class="field-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="motivo">
                        Motivo de la habilitación
                    </label>

                    <textarea
                        id="motivo"
                        name="motivo"
                        class="form-control"
                        minlength="10"
                        maxlength="500"
                        required
                        placeholder="Explique por qué el agente debe realizar el arqueo fuera de tiempo."
                    >{{ old('motivo') }}</textarea>

                    @error('motivo')
                        <span class="field-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Habilitar arqueo
                </button>
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <h3>Historial de habilitaciones</h3>
        </div>

        <form
            method="GET"
            action="{{ route(
                'promotor.habilitaciones-atrasadas.index'
            ) }}"
            class="filters"
        >
            <input
                type="text"
                name="buscar"
                class="form-control"
                value="{{ $busqueda }}"
                placeholder="Buscar agente, ruta o región"
            >

            <select
                name="estado"
                class="form-control"
            >
                <option value="">Todos los estados</option>

                @foreach ([
                    'PENDIENTE' => 'Pendiente',
                    'UTILIZADA' => 'Utilizada',
                    'CANCELADA' => 'Cancelada',
                ] as $valor => $texto)
                    <option
                        value="{{ $valor }}"
                        @selected($estado === $valor)
                    >
                        {{ $texto }}
                    </option>
                @endforeach
            </select>

            <button
                type="submit"
                class="btn btn-primary"
                style="width: auto; margin-top: 0;"
            >
                Filtrar
            </button>

            @if ($busqueda !== '' || $estado !== '')
                <a
                    href="{{ route(
                        'promotor.habilitaciones-atrasadas.index'
                    ) }}"
                    class="btn btn-secondary"
                >
                    Limpiar
                </a>
            @endif
        </form>

        <div class="table-wrapper">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Agente</th>
                        <th>Fecha habilitada</th>
                        <th>Motivo</th>
                        <th>Autorizada</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($habilitaciones as $habilitacion)
                        @php
                            $badgeClase = match (
                                $habilitacion->estado
                            ) {
                                'PENDIENTE' => 'badge-pending',
                                'UTILIZADA' => 'badge-used',
                                'CANCELADA' => 'badge-cancelled',
                                default => 'badge-pending',
                            };
                        @endphp

                        <tr>
                            <td>
                                <strong>
                                    {{ $habilitacion->codigo_agente }}
                                </strong>
                                <br>
                                {{ $habilitacion->nombre_negocio }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse(
                                    $habilitacion->fecha_autorizada
                                )->format('d/m/Y') }}
                            </td>

                            <td class="reason">
                                {{ $habilitacion->motivo }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse(
                                    $habilitacion->autorizado_at
                                )->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                <span class="badge {{ $badgeClase }}">
                                    {{ $habilitacion->estado }}
                                </span>
                            </td>

                            <td>
                                @if (
                                    $habilitacion->estado === 'PENDIENTE'
                                )
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'promotor.habilitaciones-atrasadas.cancelar',
                                            $habilitacion->id
                                        ) }}"
                                        onsubmit="return confirm(
                                            '¿Desea cancelar esta habilitación?'
                                        );"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="btn btn-danger"
                                        >
                                            Cancelar
                                        </button>
                                    </form>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    No existen habilitaciones registradas.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($habilitaciones->hasPages())
            <div class="pagination-wrapper">
                {{ $habilitaciones->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
