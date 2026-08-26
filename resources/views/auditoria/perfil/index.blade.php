@extends('layouts.auditoria')

@section('title','Perfil')
@section('module-title','Perfil')

@section('content')
@php
    $nombreCompleto = trim(($perfil->nombres ?? '') . ' ' . ($perfil->apellidos ?? ''));
    $nombreMostrar = $nombreCompleto !== '' ? $nombreCompleto : $perfil->usuario;
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>Perfil de Auditoría</h2>
        <p>Información de la cuenta y resumen de su actividad.</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:360px 1fr;gap:20px;">
    <section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">{{ $nombreMostrar }}</h3>
        <p><strong>Usuario:</strong> {{ $perfil->usuario }}</p>
        <p><strong>Rol:</strong> Auditoría</p>
        <p><strong>Estado:</strong> {{ $perfil->estado }}</p>

        <a href="{{ url('/cambiar-password') }}" style="display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border-radius:10px;background:#1e5d82;color:#fff;font-size:10px;font-weight:800;text-decoration:none;margin-top:8px;">
            Cambiar Contraseña
        </a>
    </section>

    <section style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;">
        @foreach([
            ['Total Auditorías',$resumen->total],
            ['Auditorías Hoy',$resumen->hoy],
            ['Certificados',$resumen->certificados],
            ['Pendientes',$resumen->pendientes],
            ['Faltantes',$resumen->faltantes],
            ['Sobrantes',$resumen->sobrantes],
        ] as [$label,$value])
            <div style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
                <span style="display:block;color:#758697;font-size:8px;font-weight:800;text-transform:uppercase;">{{ $label }}</span>
                <strong style="display:block;margin-top:8px;color:#082d55;font-size:24px;">{{ $value }}</strong>
            </div>
        @endforeach
    </section>
</div>
@endsection
