@extends('layouts.jefe')

@section('title', 'Anular Arqueos')
@section('module-title', 'Anular Arqueos')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Anular Arqueos</h2>
        <p>
            Seleccione un arqueo pendiente o certificado.
            La anulación se realiza desde el detalle del arqueo
            y requiere motivo y contraseña.
        </p>
    </div>
</div>

<form
    method="GET"
    action="{{ route('jefe.anulaciones.index') }}"
    style="margin-bottom:20px;"
>
    <div
        style="
            display:grid;
            grid-template-columns:minmax(260px,1fr) 220px;
            gap:10px;
        "
    >
        <input
            type="text"
            name="buscar"
            value="{{ $buscar }}"
            placeholder="Número, agente, negocio o responsable"
            style="
                min-height:42px;
                padding:0 12px;
                border:1px solid #ced9e1;
                border-radius:10px;
            "
        >

        <select
            name="estado"
            style="
                min-height:42px;
                padding:0 12px;
                border:1px solid #ced9e1;
                border-radius:10px;
            "
        >
            <option value="">
                Pendientes y certificados
            </option>

            <option
                value="PENDIENTE_CERTIFICACION"
                @selected(
                    $estado === 'PENDIENTE_CERTIFICACION'
                )
            >
                Pendiente certificación
            </option>

            <option
                value="CERTIFICADO"
                @selected($estado === 'CERTIFICADO')
            >
                Certificado
            </option>
        </select>
    </div>
</form>

<div style="overflow-x:auto;background:#fff;border-radius:16px;">
    <table
        style="
            width:100%;
            min-width:1100px;
            border-collapse:collapse;
        "
    >
        <thead>
            <tr style="background:#f7f9fb;">
                <th style="padding:12px;">Número</th>
                <th style="padding:12px;">Fecha</th>
                <th style="padding:12px;">Agente</th>
                <th style="padding:12px;">Tipo</th>
                <th style="padding:12px;">Estado</th>
                <th style="padding:12px;">Responsable</th>
                <th style="padding:12px;">Acción</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($arqueos as $arqueo)
                @php
                    $creador = trim(
                        ($arqueo->creador_nombres ?? '')
                        . ' '
                        . ($arqueo->creador_apellidos ?? '')
                    );
                @endphp

                <tr style="border-top:1px solid #edf1f4;">
                    <td style="padding:12px;">
                        {{ $arqueo->numero_arqueo }}
                    </td>

                    <td style="padding:12px;">
                        {{ \Carbon\Carbon::parse(
                            $arqueo->fecha_arqueo
                        )->format('d/m/Y') }}
                    </td>

                    <td style="padding:12px;">
                        {{ $arqueo->codigo_agente }}
                        — {{ $arqueo->nombre_negocio }}
                    </td>

                    <td style="padding:12px;">
                        {{ $arqueo->tipo === 'DIARIO_AGENTE'
                            ? 'Agente'
                            : ($arqueo->tipo === 'VISITA_PROMOTOR'
                                ? 'Promotor'
                                : str_replace(
                                    '_',
                                    ' ',
                                    $arqueo->tipo
                                )) }}
                    </td>

                    <td style="padding:12px;">
                        {{ str_replace(
                            '_',
                            ' ',
                            $arqueo->estado
                        ) }}
                    </td>

                    <td style="padding:12px;">
                        {{ $creador !== ''
                            ? $creador
                            : $arqueo->creador_usuario }}
                    </td>

                    <td style="padding:12px;">
                        <a
                            href="{{ route(
                                'jefe.arqueos.show',
                                $arqueo->id
                            ) }}"
                            style="
                                display:inline-flex;
                                padding:9px 12px;
                                border-radius:9px;
                                background:#b33a34;
                                color:#fff;
                                font-weight:800;
                                text-decoration:none;
                            "
                        >
                            Revisar y Anular
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td
                        colspan="7"
                        style="padding:35px;text-align:center;"
                    >
                        No hay arqueos disponibles para anulación.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($arqueos->hasPages())
    <div style="margin-top:18px;">
        {{ $arqueos->links() }}
    </div>
@endif
@endsection
