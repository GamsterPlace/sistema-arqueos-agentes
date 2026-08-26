@extends('layouts.gerencia')

@section('title', 'Arqueos por Agente')
@section('module-title', 'Arqueos por Agente')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Arqueos por Agente</h2>
        <p>
            Consulte el historial de arqueos diarios realizados por los Agentes.
        </p>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    <div style="padding:16px;border:1px solid #e0e8ee;border-radius:16px;background:#fff;">
        <span style="font-size:8px;font-weight:800;text-transform:uppercase;color:#758697;">Total</span>
        <strong style="display:block;margin-top:7px;font-size:22px;color:#082d55;">{{ $total }}</strong>
    </div>
    <div style="padding:16px;border:1px solid #c5e5d1;border-radius:16px;background:#f7fcf9;">
        <span style="font-size:8px;font-weight:800;text-transform:uppercase;color:#758697;">Certificados</span>
        <strong style="display:block;margin-top:7px;font-size:22px;color:#082d55;">{{ $certificados }}</strong>
    </div>
    <div style="padding:16px;border:1px solid #efd99f;border-radius:16px;background:#fffdf6;">
        <span style="font-size:8px;font-weight:800;text-transform:uppercase;color:#758697;">Pendientes</span>
        <strong style="display:block;margin-top:7px;font-size:22px;color:#082d55;">{{ $pendientes }}</strong>
    </div>
    <div style="padding:16px;border:1px solid #efc5c5;border-radius:16px;background:#fffafa;">
        <span style="font-size:8px;font-weight:800;text-transform:uppercase;color:#758697;">Anulados</span>
        <strong style="display:block;margin-top:7px;font-size:22px;color:#082d55;">{{ $anulados }}</strong>
    </div>
</div>

<form method="GET" action="{{ route('gerencia.arqueos-agentes.index') }}" style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;margin-bottom:20px;">
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
        <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Número, Agente, negocio, ruta o región" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
        <select name="agente_id" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todos los Agentes</option>
            @foreach($agentes as $agente)
                <option value="{{ $agente->id }}" @selected($agenteId === (int)$agente->id)>
                    {{ $agente->codigo_agente }} — {{ $agente->nombre_negocio }}
                </option>
            @endforeach
        </select>
        <select name="region_id" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todas las Regiones</option>
            @foreach($regiones as $region)
                <option value="{{ $region->id }}" @selected($regionId === (int)$region->id)>{{ $region->nombre }}</option>
            @endforeach
        </select>
        <select name="ruta_id" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todas las Rutas</option>
            @foreach($rutas as $ruta)
                <option value="{{ $ruta->id }}" @selected($rutaId === (int)$ruta->id)>{{ $ruta->codigo }} — {{ $ruta->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:10px;">
        <select name="estado" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todos los estados</option>
            <option value="PENDIENTE_CERTIFICACION" @selected($estado==='PENDIENTE_CERTIFICACION')>Pendiente certificación</option>
            <option value="CERTIFICADO" @selected($estado==='CERTIFICADO')>Certificado</option>
            <option value="ANULADO" @selected($estado==='ANULADO')>Anulado</option>
        </select>
        <input type="date" name="desde" value="{{ $desde }}" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
        <input type="date" name="hasta" value="{{ $hasta }}" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
        <a href="{{ route('gerencia.arqueos-agentes.index') }}" style="padding:10px 14px;border-radius:10px;background:#edf2f5;color:#3d596f;font-size:10px;font-weight:800;text-decoration:none;">Limpiar</a>
        <button type="submit" style="padding:10px 14px;border:0;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;">Aplicar filtros</button>
    </div>
</form>

<div style="overflow:hidden;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
    <div style="overflow-x:auto;">
        <table style="width:100%;min-width:1250px;border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f9fb;">
                    <th style="padding:12px;">Número</th>
                    <th style="padding:12px;">Fecha</th>
                    <th style="padding:12px;">Agente</th>
                    <th style="padding:12px;">Región</th>
                    <th style="padding:12px;">Ruta</th>
                    <th style="padding:12px;">Estado</th>
                    <th style="padding:12px;">Diferencia</th>
                    <th style="padding:12px;">Extemporáneo</th>
                    <th style="padding:12px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($arqueos as $arqueo)
                    <tr style="border-top:1px solid #edf1f4;">
                        <td style="padding:12px;"><strong>{{ $arqueo->numero_arqueo }}</strong></td>
                        <td style="padding:12px;">{{ \Carbon\Carbon::parse($arqueo->fecha_arqueo)->format('d/m/Y') }}</td>
                        <td style="padding:12px;">{{ $arqueo->codigo_agente }} — {{ $arqueo->nombre_negocio }}</td>
                        <td style="padding:12px;">{{ $arqueo->region_nombre ?? '—' }}</td>
                        <td style="padding:12px;">{{ $arqueo->ruta_codigo }} — {{ $arqueo->ruta_nombre }}</td>
                        <td style="padding:12px;">{{ str_replace('_',' ',$arqueo->estado) }}</td>
                        <td style="padding:12px;">
                            @php $d=(float)$arqueo->diferencia; @endphp
                            Q {{ number_format(abs($d),2) }}
                            @if($d<0) Faltante @elseif($d>0) Sobrante @else Exacto @endif
                        </td>
                        <td style="padding:12px;">{{ $arqueo->fuera_fecha_ordinaria ? 'Sí' : 'No' }}</td>
                        <td style="padding:12px;white-space:nowrap;">
                            <a href="{{ route('gerencia.arqueos-agentes.show',$arqueo->id) }}" style="padding:8px 10px;border-radius:8px;background:#edf2f5;color:#31536e;text-decoration:none;font-size:9px;font-weight:800;">Ver</a>
                            <a href="{{ route('gerencia.arqueos-agentes.imprimir',$arqueo->id) }}" target="_blank" style="padding:8px 10px;border-radius:8px;background:#164c96;color:#fff;text-decoration:none;font-size:9px;font-weight:800;">Imprimir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="padding:35px;text-align:center;">No se encontraron arqueos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($arqueos->hasPages())
        <div style="padding:16px 18px;">{{ $arqueos->links() }}</div>
    @endif
</div>
@endsection
