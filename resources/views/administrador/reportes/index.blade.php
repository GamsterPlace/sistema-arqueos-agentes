@extends('layouts.administrador')
@section('title','Reportes')
@section('module-title','Reportes')
@section('content')
<div class="page-header"><div class="page-title"><h2>Reportes Administrativos</h2><p>Indicadores generales, faltantes, sobrantes y rankings del sistema.</p></div></div>
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px;">
@foreach([
 ['Total',$metricas->total??0],['Faltantes',$metricas->faltantes??0],['Sobrantes',$metricas->sobrantes??0],
 ['Monto Faltantes','Q '.number_format((float)($metricas->monto_faltantes??0),2)],
 ['Monto Sobrantes','Q '.number_format((float)($metricas->monto_sobrantes??0),2)]
] as [$l,$v])
<div style="padding:15px;border:1px solid #e0e8ee;border-radius:15px;background:#fff;"><span style="font-size:8px;font-weight:800;">{{ $l }}</span><strong style="display:block;margin-top:7px;font-size:20px;">{{ $v }}</strong></div>
@endforeach
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
<section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;"><h3>Ranking Faltantes</h3>@forelse($rankingFaltantes as $r)<p>{{ $r->codigo_agente }} — {{ $r->nombre_negocio }} · Q {{ number_format((float)$r->monto,2) }}</p>@empty<p>Sin datos.</p>@endforelse</section>
<section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;"><h3>Ranking Sobrantes</h3>@forelse($rankingSobrantes as $r)<p>{{ $r->codigo_agente }} — {{ $r->nombre_negocio }} · Q {{ number_format((float)$r->monto,2) }}</p>@empty<p>Sin datos.</p>@endforelse</section>
<section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;"><h3>Regiones con Faltantes</h3>@forelse($regionesFaltantes as $r)<p>{{ $r->nombre }} · Q {{ number_format((float)$r->monto,2) }}</p>@empty<p>Sin datos.</p>@endforelse</section>
<section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;"><h3>Regiones con Sobrantes</h3>@forelse($regionesSobrantes as $r)<p>{{ $r->nombre }} · Q {{ number_format((float)$r->monto,2) }}</p>@empty<p>Sin datos.</p>@endforelse</section>
</div>
@endsection
