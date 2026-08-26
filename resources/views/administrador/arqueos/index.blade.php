@extends('layouts.administrador')
@section('title','Todos los Arqueos')
@section('module-title','Todos los Arqueos')
@section('content')
<div class="page-header"><div class="page-title"><h2>Todos los Arqueos</h2><p>Consulta consolidada de arqueos de Agentes y Promotores.</p></div></div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
@foreach([
 ['Total',$resumen->total],['Agente',$resumen->agente],['Promotor',$resumen->promotor],
 ['Certificados',$resumen->certificados],['Pendientes',$resumen->pendientes],
 ['Anulados',$resumen->anulados],['Extemporáneos',$resumen->extemporaneos]
] as [$l,$v])
<div style="padding:15px;border:1px solid #e0e8ee;border-radius:15px;background:#fff;"><span style="font-size:8px;font-weight:800;text-transform:uppercase;color:#758697;">{{ $l }}</span><strong style="display:block;margin-top:7px;font-size:21px;color:#082d55;">{{ $v }}</strong></div>
@endforeach
</div>

<form method="GET" action="{{ route('administrador.arqueos.index') }}" style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;margin-bottom:20px;">
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
<input name="buscar" value="{{ $buscar }}" placeholder="Número, Agente, responsable, Ruta o Región" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
<select name="tipo" style="min-height:42px;border:1px solid #ced9e1;border-radius:10px;padding:0 12px;"><option value="">Todos los tipos</option><option value="DIARIO_AGENTE" @selected($tipo==='DIARIO_AGENTE')>Agente</option><option value="VISITA_PROMOTOR" @selected($tipo==='VISITA_PROMOTOR')>Promotor</option></select>
<select name="estado" style="min-height:42px;border:1px solid #ced9e1;border-radius:10px;padding:0 12px;"><option value="">Todos los estados</option><option value="PENDIENTE_CERTIFICACION" @selected($estado==='PENDIENTE_CERTIFICACION')>Pendiente</option><option value="CERTIFICADO" @selected($estado==='CERTIFICADO')>Certificado</option><option value="ANULADO" @selected($estado==='ANULADO')>Anulado</option></select>
<select name="extemporaneo" style="min-height:42px;border:1px solid #ced9e1;border-radius:10px;padding:0 12px;"><option value="">Todos</option><option value="SI" @selected($extemporaneo==='SI')>Extemporáneos</option><option value="NO" @selected($extemporaneo==='NO')>Ordinarios</option></select>
</div>
<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;"><a href="{{ route('administrador.arqueos.index') }}" style="padding:10px 14px;border-radius:10px;background:#edf2f5;text-decoration:none;">Limpiar</a><button style="padding:10px 14px;border:0;border-radius:10px;background:#164c96;color:#fff;font-weight:800;">Aplicar</button></div>
</form>

<div style="overflow:hidden;border:1px solid #e0e8ee;border-radius:18px;background:#fff;"><div style="overflow-x:auto;"><table style="width:100%;min-width:1300px;border-collapse:collapse;"><thead><tr style="background:#f7f9fb;"><th style="padding:12px;">Número</th><th style="padding:12px;">Fecha</th><th style="padding:12px;">Tipo</th><th style="padding:12px;">Agente</th><th style="padding:12px;">Responsable</th><th style="padding:12px;">Región</th><th style="padding:12px;">Ruta</th><th style="padding:12px;">Estado</th><th style="padding:12px;">Diferencia</th><th style="padding:12px;">Acciones</th></tr></thead><tbody>
@forelse($arqueos as $arqueo)
@php $resp=trim(($arqueo->responsable_nombres??'').' '.($arqueo->responsable_apellidos??'')); @endphp
<tr style="border-top:1px solid #edf1f4;"><td style="padding:12px;">{{ $arqueo->numero_arqueo }}</td><td style="padding:12px;">{{ \Carbon\Carbon::parse($arqueo->fecha_arqueo)->format('d/m/Y') }}</td><td style="padding:12px;">{{ str_replace('_',' ',$arqueo->tipo) }}</td><td style="padding:12px;">{{ $arqueo->codigo_agente }} — {{ $arqueo->nombre_negocio }}</td><td style="padding:12px;">{{ $resp!==''?$resp:($arqueo->responsable_usuario??'—') }}</td><td style="padding:12px;">{{ $arqueo->region_nombre??'—' }}</td><td style="padding:12px;">{{ $arqueo->ruta_nombre??'—' }}</td><td style="padding:12px;">{{ str_replace('_',' ',$arqueo->estado) }}</td><td style="padding:12px;">Q {{ number_format(abs((float)$arqueo->diferencia),2) }}</td><td style="padding:12px;white-space:nowrap;"><a href="{{ route('administrador.arqueos.show',$arqueo->id) }}">Ver</a> · <a target="_blank" href="{{ route('administrador.arqueos.imprimir',$arqueo->id) }}">Imprimir</a></td></tr>
@empty
<tr><td colspan="10" style="padding:35px;text-align:center;">No hay arqueos.</td></tr>
@endforelse
</tbody></table></div>@if($arqueos->hasPages())<div style="padding:16px;">{{ $arqueos->links() }}</div>@endif</div>
@endsection
