@extends('layouts.auditoria')

@section('title','Reportes')
@section('module-title','Reportes')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Reportes de Auditoría</h2>
        <p>Indicadores de los arqueos de Auditoría realizados por su usuario.</p>
    </div>
</div>

<form method="GET" action="{{ route('auditoria.reportes.index') }}" style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;margin-bottom:20px;">
    <div style="display:grid;grid-template-columns:220px 1fr 1fr;gap:10px;">
        <select name="resultado" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todos los Resultados</option>
            <option value="FALTANTE" @selected($resultado === 'FALTANTE')>Faltante</option>
            <option value="SOBRANTE" @selected($resultado === 'SOBRANTE')>Sobrante</option>
            <option value="EXACTO" @selected($resultado === 'EXACTO')>Exacto</option>
        </select>
        <input type="date" name="desde" value="{{ $desde }}" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
        <input type="date" name="hasta" value="{{ $hasta }}" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
        <a href="{{ route('auditoria.reportes.index') }}" style="padding:10px 14px;border-radius:10px;background:#edf2f5;color:#3d596f;text-decoration:none;font-size:10px;font-weight:800;">Limpiar</a>
        <button type="submit" style="padding:10px 14px;border:0;border-radius:10px;background:#1e5d82;color:#fff;font-size:10px;font-weight:800;">Aplicar</button>
    </div>
</form>

<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px;">
    @foreach([
        ['Total',$metricas->total ?? 0],
        ['Faltantes',$metricas->faltantes ?? 0],
        ['Sobrantes',$metricas->sobrantes ?? 0],
        ['Monto Faltantes','Q '.number_format((float)($metricas->monto_faltantes ?? 0),2)],
        ['Monto Sobrantes','Q '.number_format((float)($metricas->monto_sobrantes ?? 0),2)],
    ] as [$label,$value])
        <div style="padding:15px;border:1px solid #e0e8ee;border-radius:15px;background:#fff;">
            <span style="display:block;color:#758697;font-size:8px;font-weight:800;text-transform:uppercase;">{{ $label }}</span>
            <strong style="display:block;margin-top:7px;color:#082d55;font-size:20px;">{{ $value }}</strong>
        </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Agentes con más Faltantes</h3>
        @forelse($rankingFaltantes as $r)
            <p>{{ $r->codigo_agente_historico }} — {{ $r->nombre_negocio_historico }} · {{ $r->incidencias }} · Q {{ number_format((float)$r->monto,2) }}</p>
        @empty
            <p>Sin faltantes registrados.</p>
        @endforelse
    </section>

    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Agentes con más Sobrantes</h3>
        @forelse($rankingSobrantes as $r)
            <p>{{ $r->codigo_agente_historico }} — {{ $r->nombre_negocio_historico }} · {{ $r->incidencias }} · Q {{ number_format((float)$r->monto,2) }}</p>
        @empty
            <p>Sin sobrantes registrados.</p>
        @endforelse
    </section>

    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Regiones con más Faltantes</h3>
        @forelse($porRegionFaltantes as $r)
            <p>{{ $r->region_historica ?: 'Sin Región' }} · {{ $r->incidencias }} · Q {{ number_format((float)$r->monto,2) }}</p>
        @empty
            <p>Sin información.</p>
        @endforelse
    </section>

    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Regiones con más Sobrantes</h3>
        @forelse($porRegionSobrantes as $r)
            <p>{{ $r->region_historica ?: 'Sin Región' }} · {{ $r->incidencias }} · Q {{ number_format((float)$r->monto,2) }}</p>
        @empty
            <p>Sin información.</p>
        @endforelse
    </section>
</div>
@endsection
