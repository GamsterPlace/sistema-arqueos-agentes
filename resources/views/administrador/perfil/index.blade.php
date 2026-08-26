@extends('layouts.administrador')
@section('title','Perfil')
@section('module-title','Perfil')
@section('content')
@php $nombre=trim(($perfil->nombres??'').' '.($perfil->apellidos??'')); @endphp
<div class="page-header"><div class="page-title"><h2>Perfil del Administrador</h2><p>Información de la cuenta y resumen general del sistema.</p></div></div>
<div style="display:grid;grid-template-columns:360px 1fr;gap:20px;">
<section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;"><h3>{{ $nombre!==''?$nombre:$perfil->usuario }}</h3><p><strong>Usuario:</strong> {{ $perfil->usuario }}</p><p><strong>Rol:</strong> Administrador</p><p><strong>Estado:</strong> {{ $perfil->estado }}</p><a href="{{ url('/cambiar-password') }}">Cambiar contraseña</a></section>
<section style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;">@foreach([['Usuarios',$resumen->usuarios],['Agentes',$resumen->agentes],['Arqueos',$resumen->arqueos],['Anulados',$resumen->anulados],['Rutas',$resumen->rutas],['Regiones',$resumen->regiones]] as [$l,$v])<div style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;"><span>{{ $l }}</span><strong style="display:block;font-size:24px;">{{ $v }}</strong></div>@endforeach</section>
</div>
@endsection
