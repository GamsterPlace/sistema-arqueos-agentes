@extends('layouts.gerencia')

@section('title', 'Arqueos Extemporáneos')
@section('module-title', 'Arqueos Extemporáneos')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Arqueos Extemporáneos</h2>

        <p>
            Consulte los arqueos realizados fuera de la fecha ordinaria,
            su responsable, resultado y estado.
        </p>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:20px;">
    @foreach([
        ['Total', $total],
        ['Agentes', $deAgentes],
        ['Promotores', $dePromotores],
        ['Certificados', $certificados],
        ['Pendientes', $pendientes],
        ['Anulados', $anulados],
    ] as [$label,$value])
        <div style="padding:15px;border:1px solid #e0e8ee;border-radius:15px;background:#fff;">
            <span style="display:block;font-size:8px;font-weight:800;text-transform:uppercase;color:#758697;">
                {{ $label }}
            </span>

            <strong style="display:block;margin-top:7px;font-size:21px;color:#082d55;">
                {{ $value }}
            </strong>
        </div>
    @endforeach
</div>

<form
    method="GET"
    action="{{ route('gerencia.arqueos-extemporaneos.index') }}"
    style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;margin-bottom:20px;"
>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
        <input
            type="text"
            name="buscar"
            value="{{ $buscar }}"
            placeholder="Número, Agente, responsable, Ruta o Región"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >

        <select name="tipo" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todos los tipos</option>
            <option value="DIARIO_AGENTE" @selected($tipo === 'DIARIO_AGENTE')>
                Agente
            </option>
            <option value="VISITA_PROMOTOR" @selected($tipo === 'VISITA_PROMOTOR')>
                Promotor
            </option>
        </select>

        <select name="estado" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todos los estados</option>
            <option
                value="PENDIENTE_CERTIFICACION"
                @selected($estado === 'PENDIENTE_CERTIFICACION')
            >
                Pendiente certificación
            </option>
            <option value="CERTIFICADO" @selected($estado === 'CERTIFICADO')>
                Certificado
            </option>
            <option value="ANULADO" @selected($estado === 'ANULADO')>
                Anulado
            </option>
        </select>

        <select name="agente_id" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todos los Agentes</option>

            @foreach($agentes as $agente)
                <option
                    value="{{ $agente->id }}"
                    @selected($agenteId === (int) $agente->id)
                >
                    {{ $agente->codigo_agente }}
                    — {{ $agente->nombre_negocio }}
                </option>
            @endforeach
        </select>
    </div>

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:10px;">
        <select name="promotor_id" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todos los Promotores</option>

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
                    @selected($promotorId === (int) $promotor->id)
                >
                    {{ $nombrePromotor !== ''
                        ? $nombrePromotor
                        : $promotor->usuario }}
                </option>
            @endforeach
        </select>

        <select name="region_id" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
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

        <select name="ruta_id" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todas las Rutas</option>

            @foreach($rutas as $ruta)
                <option
                    value="{{ $ruta->id }}"
                    @selected($rutaId === (int) $ruta->id)
                >
                    {{ $ruta->codigo }}
                    — {{ $ruta->nombre }}
                </option>
            @endforeach
        </select>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <input
                type="date"
                name="desde"
                value="{{ $desde }}"
                style="min-height:42px;padding:0 10px;border:1px solid #ced9e1;border-radius:10px;"
            >

            <input
                type="date"
                name="hasta"
                value="{{ $hasta }}"
                style="min-height:42px;padding:0 10px;border:1px solid #ced9e1;border-radius:10px;"
            >
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
        <a
            href="{{ route('gerencia.arqueos-extemporaneos.index') }}"
            style="padding:10px 14px;border-radius:10px;background:#edf2f5;color:#3d596f;font-size:10px;font-weight:800;text-decoration:none;"
        >
            Limpiar
        </a>

        <button
            type="submit"
            style="padding:10px 14px;border:0;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;"
        >
            Aplicar filtros
        </button>
    </div>
</form>

<div style="overflow:hidden;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
    <div style="padding:18px 20px;border-bottom:1px solid #edf1f4;background:#fafcfd;">
        <h3 style="margin:0;color:#0a3158;font-size:15px;">
            Historial de Arqueos Extemporáneos
        </h3>

        <p style="margin:5px 0 0;color:#82909a;font-size:10px;">
            Información de consulta para Gerencia.
        </p>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;min-width:1350px;border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f9fb;">
                    <th style="padding:12px;">Número</th>
                    <th style="padding:12px;">Fecha</th>
                    <th style="padding:12px;">Tipo</th>
                    <th style="padding:12px;">Agente</th>
                    <th style="padding:12px;">Responsable</th>
                    <th style="padding:12px;">Región</th>
                    <th style="padding:12px;">Ruta</th>
                    <th style="padding:12px;">Estado</th>
                    <th style="padding:12px;">Diferencia</th>
                    <th style="padding:12px;">Acciones</th>
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
                    @endphp

                    <tr style="border-top:1px solid #edf1f4;">
                        <td style="padding:12px;">
                            <strong>{{ $arqueo->numero_arqueo }}</strong>
                        </td>

                        <td style="padding:12px;">
                            {{ \Carbon\Carbon::parse(
                                $arqueo->fecha_arqueo
                            )->format('d/m/Y') }}
                        </td>

                        <td style="padding:12px;">
                            {{ $arqueo->tipo === 'DIARIO_AGENTE'
                                ? 'Agente'
                                : (
                                    $arqueo->tipo === 'VISITA_PROMOTOR'
                                        ? 'Promotor'
                                        : str_replace('_', ' ', $arqueo->tipo)
                                ) }}
                        </td>

                        <td style="padding:12px;">
                            {{ $arqueo->codigo_agente }}
                            — {{ $arqueo->nombre_negocio }}
                        </td>

                        <td style="padding:12px;">
                            {{ $responsable !== ''
                                ? $responsable
                                : ($arqueo->responsable_usuario ?? '—') }}
                        </td>

                        <td style="padding:12px;">
                            {{ $arqueo->region_nombre ?? '—' }}
                        </td>

                        <td style="padding:12px;">
                            {{ $arqueo->ruta_codigo }}
                            — {{ $arqueo->ruta_nombre }}
                        </td>

                        <td style="padding:12px;">
                            {{ str_replace('_', ' ', $arqueo->estado) }}
                        </td>

                        <td style="padding:12px;">
                            Q {{ number_format(abs($diferencia), 2) }}

                            @if($diferencia < 0)
                                Faltante
                            @elseif($diferencia > 0)
                                Sobrante
                            @else
                                Exacto
                            @endif
                        </td>

                        <td style="padding:12px;white-space:nowrap;">
                            <a
                                href="{{ route(
                                    'gerencia.arqueos.show',
                                    $arqueo->id
                                ) }}"
                                style="padding:8px 10px;border-radius:8px;background:#edf2f5;color:#31536e;text-decoration:none;font-size:9px;font-weight:800;"
                            >
                                Ver
                            </a>

                            <a
                                href="{{ route(
                                    'gerencia.arqueos.imprimir',
                                    $arqueo->id
                                ) }}"
                                target="_blank"
                                style="padding:8px 10px;border-radius:8px;background:#164c96;color:#fff;text-decoration:none;font-size:9px;font-weight:800;"
                            >
                                Imprimir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="10"
                            style="padding:35px;text-align:center;"
                        >
                            No se encontraron arqueos extemporáneos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($arqueos->hasPages())
        <div style="padding:16px 18px;">
            {{ $arqueos->links() }}
        </div>
    @endif
</div>
@endsection
