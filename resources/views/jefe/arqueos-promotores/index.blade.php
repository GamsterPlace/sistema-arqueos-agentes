@extends('layouts.jefe')

@section('title', 'Arqueos por Promotor')
@section('module-title', 'Arqueos por Promotor')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Ver Arqueos por Promotor</h2>
        <p>Consulte los arqueos realizados por cada Promotor durante sus visitas a los Agentes MICOOPE.</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin-bottom:22px;">
    <div style="padding:18px;border:1px solid #e0e8ee;border-radius:16px;background:#fff;">
        <span style="font-size:9px;font-weight:800;color:#768692;text-transform:uppercase;">Total arqueos de Promotor</span>
        <strong style="display:block;margin-top:8px;font-size:25px;color:#082d55;">{{ $totalArqueos }}</strong>
    </div>

    <div style="padding:18px;border:1px solid #e0e8ee;border-radius:16px;background:#fff;">
        <span style="font-size:9px;font-weight:800;color:#768692;text-transform:uppercase;">Realizados hoy</span>
        <strong style="display:block;margin-top:8px;font-size:25px;color:#082d55;">{{ $arqueosHoy }}</strong>
    </div>

    <div style="padding:18px;border:1px solid #efd99f;border-radius:16px;background:#fffdf6;">
        <span style="font-size:9px;font-weight:800;color:#768692;text-transform:uppercase;">Pendientes de certificación</span>
        <strong style="display:block;margin-top:8px;font-size:25px;color:#082d55;">{{ $pendientes }}</strong>
    </div>

    <div style="padding:18px;border:1px solid #c4e4d1;border-radius:16px;background:#f7fcf9;">
        <span style="font-size:9px;font-weight:800;color:#768692;text-transform:uppercase;">Certificados</span>
        <strong style="display:block;margin-top:8px;font-size:25px;color:#082d55;">{{ $certificados }}</strong>
    </div>
</div>

<section style="margin-bottom:20px;padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
    <form method="GET" action="{{ route('jefe.arqueos-promotores.index') }}">
        <div style="display:grid;grid-template-columns:minmax(220px,1fr) 230px 190px 160px 160px;gap:11px;">
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Número, agente, negocio o promotor" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">

            <select name="promotor_id" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
                <option value="">Todos los promotores</option>
                @foreach ($promotores as $promotor)
                    @php
                        $nombrePromotor = trim(($promotor->nombres ?? '') . ' ' . ($promotor->apellidos ?? ''));
                    @endphp
                    <option value="{{ $promotor->id }}" @selected($promotorId === (int) $promotor->id)>
                        {{ $nombrePromotor !== '' ? $nombrePromotor : $promotor->nombre_usuario }}
                    </option>
                @endforeach
            </select>

            <select name="estado" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
                <option value="">Todos los estados</option>
                <option value="PENDIENTE_CERTIFICACION" @selected($estado === 'PENDIENTE_CERTIFICACION')>Pendiente certificación</option>
                <option value="CERTIFICADO" @selected($estado === 'CERTIFICADO')>Certificado</option>
                <option value="ANULADO" @selected($estado === 'ANULADO')>Anulado</option>
            </select>

            <input type="date" name="desde" value="{{ $desde }}" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <input type="date" name="hasta" value="{{ $hasta }}" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:13px;">
            <a href="{{ route('jefe.arqueos-promotores.index') }}" style="display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border-radius:10px;background:#edf2f5;color:#3d596f;font-size:10px;font-weight:800;text-decoration:none;">Limpiar</a>
            <button type="submit" style="min-height:40px;padding:0 15px;border:0;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;">Aplicar filtros</button>
        </div>
    </form>
</section>

<section style="overflow:hidden;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
    <div style="padding:18px 20px;border-bottom:1px solid #edf1f4;background:#fafcfd;">
        <h3 style="margin:0;color:#0a3158;font-size:15px;">Arqueos realizados por Promotores</h3>
        <p style="margin:5px 0 0;color:#82909a;font-size:10px;">Resultados ordenados del más reciente al más antiguo.</p>
    </div>

    <div style="overflow-x:auto;">
        @if ($arqueos->isEmpty())
            <div style="padding:44px 20px;text-align:center;color:#7d8b95;font-size:11px;">No se encontraron arqueos para los filtros seleccionados.</div>
        @else
            <table style="width:100%;min-width:1220px;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Promotor</th>
                        <th>Agente</th>
                        <th>Región</th>
                        <th>Ruta</th>
                        <th>Estado</th>
                        <th>Total arqueado</th>
                        <th>Diferencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($arqueos as $arqueo)
                        @php
                            $nombrePromotor = trim(($arqueo->promotor_nombres ?? '') . ' ' . ($arqueo->promotor_apellidos ?? ''));
                        @endphp
                        <tr>
                            <td>{{ $arqueo->numero_arqueo }}</td>
                            <td>{{ \Carbon\Carbon::parse($arqueo->fecha_arqueo)->format('d/m/Y') }}</td>
                            <td>{{ $nombrePromotor !== '' ? $nombrePromotor : $arqueo->promotor_usuario }}</td>
                            <td>{{ $arqueo->codigo_agente }} — {{ $arqueo->nombre_negocio }}</td>
                            <td>{{ $arqueo->region_nombre ?? '—' }}</td>
                            <td>{{ $arqueo->ruta_nombre ?? '—' }}</td>
                            <td>{{ str_replace('_', ' ', $arqueo->estado) }}</td>
                            <td>Q {{ number_format((float) $arqueo->total_arqueado, 2) }}</td>
                            <td>Q {{ number_format((float) $arqueo->diferencia, 2) }}</td>
                            <td>
                                <a href="{{ route('jefe.arqueos-promotores.show', $arqueo->id) }}">Ver</a>
                                |
                                <a href="{{ route('jefe.arqueos-promotores.imprimir', $arqueo->id) }}" target="_blank">Imprimir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($arqueos->hasPages())
        <div style="padding:16px 18px;">{{ $arqueos->links() }}</div>
    @endif
</section>
@endsection
