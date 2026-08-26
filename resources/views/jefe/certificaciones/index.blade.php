@extends('layouts.jefe')

@section('title', 'Certificar Arqueos')
@section('module-title', 'Certificar Arqueos')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Certificar Arqueos</h2>
        <p>
            Arqueos realizados por Promotores que ya fueron
            validados por el Agente y están pendientes
            de certificación del Jefe de Agentes.
        </p>
    </div>
</div>

@if ($errors->any())
    <div class="alert-message warning">
        {{ $errors->first() }}
    </div>
@endif

<div style="margin-bottom:20px;">
    <strong>
        Pendientes de certificación:
        {{ $totalPendientes }}
    </strong>
</div>

<form
    method="GET"
    action="{{ route('jefe.certificaciones.index') }}"
    style="margin-bottom:20px;"
>
    <input
        type="text"
        name="buscar"
        value="{{ $buscar }}"
        placeholder="Número, agente, negocio o Promotor"
        style="
            width:100%;
            max-width:520px;
            min-height:42px;
            padding:0 12px;
            border:1px solid #ced9e1;
            border-radius:10px;
        "
    >
</form>

<div style="overflow-x:auto;background:#fff;border-radius:16px;">
    <table
        style="
            width:100%;
            min-width:1050px;
            border-collapse:collapse;
        "
    >
        <thead>
            <tr style="background:#f7f9fb;">
                <th style="padding:12px;">Número</th>
                <th style="padding:12px;">Fecha</th>
                <th style="padding:12px;">Agente</th>
                <th style="padding:12px;">Promotor</th>
                <th style="padding:12px;">Ruta</th>
                <th style="padding:12px;">Región</th>
                <th style="padding:12px;">Acción</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($arqueos as $arqueo)
                @php
                    $promotor = trim(
                        ($arqueo->promotor_nombres ?? '')
                        . ' '
                        . ($arqueo->promotor_apellidos ?? '')
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
                        {{ $promotor !== ''
                            ? $promotor
                            : $arqueo->promotor_usuario }}
                    </td>

                    <td style="padding:12px;">
                        {{ $arqueo->ruta_nombre ?? '—' }}
                    </td>

                    <td style="padding:12px;">
                        {{ $arqueo->region_nombre ?? '—' }}
                    </td>

                    <td style="padding:12px;">
                        <form
                            method="POST"
                            action="{{ route(
                                'jefe.certificaciones.certificar',
                                $arqueo->id
                            ) }}"
                            onsubmit="return solicitarPassword(
                                this,
                                '{{ $arqueo->numero_arqueo }}'
                            );"
                        >
                            @csrf

                            <input
                                type="hidden"
                                name="password"
                                value=""
                            >

                            <button
                                type="submit"
                                style="
                                    border:0;
                                    border-radius:9px;
                                    padding:9px 12px;
                                    background:#16834f;
                                    color:#fff;
                                    font-weight:800;
                                    cursor:pointer;
                                "
                            >
                                Certificar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td
                        colspan="7"
                        style="padding:35px;text-align:center;"
                    >
                        No hay arqueos pendientes de certificación.
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

@push('scripts')
<script>
    function solicitarPassword(form, numero) {
        const password = window.prompt(
            'Ingrese su contraseña para certificar el arqueo '
            + numero
            + ':'
        );

        if (! password) {
            return false;
        }

        form.querySelector(
            'input[name="password"]'
        ).value = password;

        return window.confirm(
            '¿Confirma la certificación electrónica del arqueo '
            + numero
            + '?'
        );
    }
</script>
@endpush
