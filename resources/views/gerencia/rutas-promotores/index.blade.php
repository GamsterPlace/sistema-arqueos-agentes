@extends('layouts.gerencia')

@section('title', 'Rutas Asignadas')
@section('module-title', 'Rutas Asignadas')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Rutas Asignadas a Promotores</h2>
        <p>
            Consulte la distribución vigente e histórica de Rutas,
            Promotores responsables y cobertura de Agentes.
        </p>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px;">
    <div style="padding:16px;border:1px solid #e0e8ee;border-radius:16px;background:#fff;">
        <span style="font-size:8px;font-weight:800;text-transform:uppercase;color:#758697;">Total Asignaciones</span>
        <strong style="display:block;margin-top:7px;font-size:22px;color:#082d55;">{{ $totalAsignaciones }}</strong>
    </div>

    <div style="padding:16px;border:1px solid #c5e5d1;border-radius:16px;background:#f7fcf9;">
        <span style="font-size:8px;font-weight:800;text-transform:uppercase;color:#758697;">Asignaciones Activas</span>
        <strong style="display:block;margin-top:7px;font-size:22px;color:#082d55;">{{ $totalAsignacionesActivas }}</strong>
    </div>

    <div style="padding:16px;border:1px solid #efd99f;border-radius:16px;background:#fffdf6;">
        <span style="font-size:8px;font-weight:800;text-transform:uppercase;color:#758697;">Rutas sin Promotor</span>
        <strong style="display:block;margin-top:7px;font-size:22px;color:#082d55;">{{ $rutasSinPromotor }}</strong>
    </div>

    <div style="padding:16px;border:1px solid #c5e5d1;border-radius:16px;background:#f7fcf9;">
        <span style="font-size:8px;font-weight:800;text-transform:uppercase;color:#758697;">Promotores con Ruta</span>
        <strong style="display:block;margin-top:7px;font-size:22px;color:#082d55;">{{ $promotoresConRuta }}</strong>
    </div>

    <div style="padding:16px;border:1px solid #efc5c5;border-radius:16px;background:#fffafa;">
        <span style="font-size:8px;font-weight:800;text-transform:uppercase;color:#758697;">Promotores sin Ruta</span>
        <strong style="display:block;margin-top:7px;font-size:22px;color:#082d55;">{{ $promotoresSinRuta }}</strong>
    </div>
</div>

<form
    method="GET"
    action="{{ route('gerencia.rutas-promotores.index') }}"
    style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;margin-bottom:20px;"
>
    <div style="display:grid;grid-template-columns:minmax(250px,1fr) 240px 220px 190px;gap:10px;">
        <input
            type="text"
            name="buscar"
            value="{{ $buscar }}"
            placeholder="Ruta, Región, Promotor o usuario"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >

        <select
            name="promotor_id"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >
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

        <select
            name="region_id"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >
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

        <select
            name="estado"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >
            <option value="">Todos los estados</option>
            <option value="ACTIVA" @selected($estado === 'ACTIVA')>Activas</option>
            <option value="PROGRAMADA" @selected($estado === 'PROGRAMADA')>Programadas</option>
            <option value="FINALIZADA" @selected($estado === 'FINALIZADA')>Finalizadas</option>
        </select>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
        <a
            href="{{ route('gerencia.rutas-promotores.index') }}"
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
        <h3 style="margin:0;color:#0a3158;font-size:15px;">Asignaciones de Rutas</h3>
        <p style="margin:5px 0 0;color:#82909a;font-size:10px;">
            Información de consulta para supervisar la cobertura territorial.
        </p>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;min-width:1250px;border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f9fb;">
                    <th style="padding:12px;">Promotor</th>
                    <th style="padding:12px;">Región</th>
                    <th style="padding:12px;">Ruta</th>
                    <th style="padding:12px;">Inicio</th>
                    <th style="padding:12px;">Fin</th>
                    <th style="padding:12px;">Estado</th>
                    <th style="padding:12px;">Agentes Activos</th>
                    <th style="padding:12px;">Arqueados Hoy</th>
                    <th style="padding:12px;">Cumplimiento</th>
                </tr>
            </thead>

            <tbody>
                @forelse($asignaciones as $asignacion)
                    @php
                        $nombrePromotor = trim(
                            ($asignacion->promotor_nombres ?? '')
                            . ' '
                            . ($asignacion->promotor_apellidos ?? '')
                        );

                        $esProgramada =
                            (bool) $asignacion->asignacion_estado
                            && \Carbon\Carbon::parse(
                                $asignacion->fecha_inicio
                            )->isFuture();

                        $esActiva =
                            (bool) $asignacion->asignacion_estado
                            && ! $esProgramada
                            && (
                                ! $asignacion->fecha_fin
                                || \Carbon\Carbon::parse(
                                    $asignacion->fecha_fin
                                )->endOfDay()->gte(now())
                            );

                        if ($esActiva) {
                            $estadoTexto = 'ACTIVA';
                        } elseif ($esProgramada) {
                            $estadoTexto = 'PROGRAMADA';
                        } else {
                            $estadoTexto = 'FINALIZADA';
                        }

                        $agentesActivos = (int) $asignacion->agentes_activos;
                        $arqueadosHoy = (int) $asignacion->agentes_arqueados_hoy;

                        $cumplimiento =
                            $agentesActivos > 0
                                ? round(
                                    ($arqueadosHoy / $agentesActivos) * 100,
                                    1
                                )
                                : 0;
                    @endphp

                    <tr style="border-top:1px solid #edf1f4;">
                        <td style="padding:12px;">
                            <strong>
                                {{ $nombrePromotor !== ''
                                    ? $nombrePromotor
                                    : $asignacion->promotor_usuario }}
                            </strong>
                        </td>

                        <td style="padding:12px;">
                            {{ $asignacion->region_nombre }}
                        </td>

                        <td style="padding:12px;">
                            {{ $asignacion->ruta_codigo }}
                            — {{ $asignacion->ruta_nombre }}
                        </td>

                        <td style="padding:12px;">
                            {{ \Carbon\Carbon::parse(
                                $asignacion->fecha_inicio
                            )->format('d/m/Y') }}
                        </td>

                        <td style="padding:12px;">
                            {{ $asignacion->fecha_fin
                                ? \Carbon\Carbon::parse(
                                    $asignacion->fecha_fin
                                )->format('d/m/Y')
                                : 'Indefinida' }}
                        </td>

                        <td style="padding:12px;">
                            {{ $estadoTexto }}
                        </td>

                        <td style="padding:12px;">
                            {{ $agentesActivos }}
                        </td>

                        <td style="padding:12px;">
                            {{ $arqueadosHoy }}
                        </td>

                        <td style="padding:12px;">
                            {{ number_format($cumplimiento, 1) }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="9"
                            style="padding:35px;text-align:center;"
                        >
                            No se encontraron asignaciones
                            para los filtros seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($asignaciones->hasPages())
        <div style="padding:16px 18px;">
            {{ $asignaciones->links() }}
        </div>
    @endif
</div>
@endsection
