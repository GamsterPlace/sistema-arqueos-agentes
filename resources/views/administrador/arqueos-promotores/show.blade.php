@extends('layouts.administrador')
@section('title','Detalle Arqueo Promotor')
@section('module-title','Arqueos de Promotores')
@section('content')
@php $a=$arqueo->agente; $p=trim(($promotor->nombres??'').' '.($promotor->apellidos??'')); @endphp
<div class="page-header"><div class="page-title"><h2>{{ $arqueo->numero_arqueo }}</h2><p>Detalle de arqueo de Promotor.</p></div></div>
<section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;"><p><strong>Promotor:</strong> {{ $p!==''?$p:($promotor->usuario??'—') }}</p><p><strong>Agente:</strong> {{ $a?->codigo_agente }} — {{ $a?->nombre_negocio }}</p><p><strong>Estado:</strong> {{ $arqueo->estado }}</p><p><strong>Saldo:</strong> Q {{ number_format((float)$arqueo->saldo_sistema,2) }}</p><p><strong>Arqueado:</strong> Q {{ number_format((float)$arqueo->total_arqueado,2) }}</p><p><strong>Diferencia:</strong> Q {{ number_format(abs((float)$arqueo->diferencia),2) }}</p></section>
@endsection
