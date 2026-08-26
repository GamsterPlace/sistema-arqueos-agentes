@extends('layouts.administrador')
@section('title','Detalle de Arqueo')
@section('module-title','Todos los Arqueos')
@section('content')
@php
$agente=$arqueo->agente; $ruta=$agente?->ruta; $region=$ruta?->region;
$resp=trim(($responsable->nombres??'').' '.($responsable->apellidos??''));
@endphp
<div class="page-header"><div class="page-title"><h2>{{ $arqueo->numero_arqueo }}</h2><p>Detalle completo del arqueo.</p></div><a target="_blank" href="{{ route('administrador.arqueos.imprimir',$arqueo->id) }}">Imprimir</a></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
<section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
<h3>Información General</h3>
<p><strong>Agente:</strong> {{ $agente?->codigo_agente }} — {{ $agente?->nombre_negocio }}</p>
<p><strong>Propietario:</strong> {{ $agente?->nombre_propietario }}</p>
<p><strong>Región:</strong> {{ $region?->nombre??'—' }}</p>
<p><strong>Ruta:</strong> {{ $ruta?->nombre??'—' }}</p>
<p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($arqueo->fecha_arqueo)->format('d/m/Y') }}</p>
<p><strong>Estado:</strong> {{ str_replace('_',' ',$arqueo->estado) }}</p>
<p><strong>Responsable:</strong> {{ $resp!==''?$resp:($responsable->usuario??'—') }}</p>
</section>
<section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
<h3>Resultado</h3>
<p><strong>Saldo Sistema:</strong> Q {{ number_format((float)$arqueo->saldo_sistema,2) }}</p>
<p><strong>Total Arqueado:</strong> Q {{ number_format((float)$arqueo->total_arqueado,2) }}</p>
<p><strong>Diferencia:</strong> Q {{ number_format(abs((float)$arqueo->diferencia),2) }}</p>
<p><strong>Observaciones:</strong> {{ $arqueo->observaciones?:'Sin observaciones' }}</p>
</section>
</div>
@endsection
