@extends('layouts.auditoria')

@section('title', 'Historial')
@section('module-title', 'Historial')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Historial de Auditorías</h2>

        <p>
            Consulte los arqueos de Auditoría realizados por su usuario.
        </p>
    </div>

    <a
        href="{{ route(
            'auditoria.arqueos.create'
        ) }}"
        style="display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border-radius:10px;background:#1e5d82;color:#fff;font-size:10px;font-weight:800;text-decoration:none;"
    >
        Realizar Arqueo
    </a>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    @foreach([
        ['Total',$resumen->total],
        ['Certificados',$resumen->certificados],
        ['Pendientes',$resumen->pendientes],
        ['Anulados',$resumen->anulados],
    ] as [$label,$value])
        <div style="padding:17px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
            <span style="display:block;color:#758697;font-size:8px;font-weight:800;text-transform:uppercase;">
                {{ $label }}
            </span>

            <strong style="display:block;margin-top:8px;color:#082d55;font-size:23px;">
                {{ $value }}
            </strong>
        </div>
    @endforeach
</div>

<form
    method="GET"
    action="{{ route(
        'auditoria.arqueos.index'
    ) }}"
    style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;margin-bottom:20px;"
>
    <div style="display:grid;grid-template-columns:1fr 220px 220px 1fr 1fr;gap:10px;">
        <input
            type="text"
            name="buscar"
            value="{{ $buscar }}"
            placeholder="Número, Agente, Ruta o Región"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >

        <select
            name="estado"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >
            <option value="">Todos los Estados</option>
            <option value="PENDIENTE_CERTIFICACION" @selected($estado === 'PENDIENTE_CERTIFICACION')>Pendiente</option>
            <option value="CERTIFICADO" @selected($estado === 'CERTIFICADO')>Certificado</option>
            <option value="ANULADO" @selected($estado === 'ANULADO')>Anulado</option>
        </select>

        <select
            name="resultado"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >
            <option value="">Todos los Resultados</option>
            <option value="FALTANTE" @selected($resultado === 'FALTANTE')>Faltante</option>
            <option value="SOBRANTE" @selected($resultado === 'SOBRANTE')>Sobrante</option>
            <option value="EXACTO" @selected($resultado === 'EXACTO')>Exacto</option>
        </select>

        <input
            type="date"
            name="desde"
            value="{{ $desde }}"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >

        <input
            type="date"
            name="hasta"
            value="{{ $hasta }}"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
        <a
            href="{{ route(
                'auditoria.arqueos.index'
            ) }}"
            style="padding:10px 14px;border-radius:10px;background:#edf2f5;color:#3d596f;text-decoration:none;font-size:10px;font-weight:800;"
        >
            Limpiar
        </a>

        <button
            type="submit"
            style="padding:10px 14px;border:0;border-radius:10px;background:#1e5d82;color:#fff;font-size:10px;font-weight:800;"
        >
            Aplicar Filtros
        </button>
    </div>
</form>

<div style="overflow:hidden;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
    <div style="overflow-x:auto;">
        <table style="width:100%;min-width:1150px;border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f9fb;">
                    <th style="padding:12px;">Número</th>
                    <th style="padding:12px;">Fecha</th>
                    <th style="padding:12px;">Agente</th>
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
                        $diferencia =
                            (float) $arqueo->diferencia;
                    @endphp

                    <tr style="border-top:1px solid #edf1f4;">
                        <td style="padding:12px;">
                            <strong>
                                {{ $arqueo->numero_arqueo }}
                            </strong>
                        </td>

                        <td style="padding:12px;">
                            {{ \Carbon\Carbon::parse(
                                $arqueo->fecha_arqueo
                            )->format('d/m/Y') }}
                        </td>

                        <td style="padding:12px;">
                            {{ $arqueo->codigo_agente_historico }}
                            — {{ $arqueo->nombre_negocio_historico }}
                        </td>

                        <td style="padding:12px;">
                            {{ $arqueo->region_historica }}
                        </td>

                        <td style="padding:12px;">
                            {{ $arqueo->ruta_historica }}
                        </td>

                        <td style="padding:12px;">
                            {{ str_replace(
                                '_',
                                ' ',
                                $arqueo->estado
                            ) }}
                        </td>

                        <td style="padding:12px;">
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
                        </td>

                        <td style="padding:12px;white-space:nowrap;">
                            <a
                                href="{{ route(
                                    'auditoria.arqueos.show',
                                    $arqueo->id
                                ) }}"
                                style="padding:8px 10px;border-radius:8px;background:#edf2f5;color:#31536e;text-decoration:none;font-size:9px;font-weight:800;"
                            >
                                Ver
                            </a>

                            <a
                                target="_blank"
                                href="{{ route(
                                    'auditoria.arqueos.imprimir',
                                    $arqueo->id
                                ) }}"
                                style="padding:8px 10px;border-radius:8px;background:#1e5d82;color:#fff;text-decoration:none;font-size:9px;font-weight:800;"
                            >
                                PDF
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="8"
                            style="padding:35px;text-align:center;"
                        >
                            No hay arqueos de Auditoría registrados.
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
