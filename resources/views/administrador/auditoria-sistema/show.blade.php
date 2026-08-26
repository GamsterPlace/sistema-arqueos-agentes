@extends('layouts.administrador')

@section('title', 'Detalle Auditoría del Sistema')
@section('module-title', 'Auditoría del Sistema')

@section('content')
@php
    $nombre = trim(
        ($registro->nombres ?? '')
        . ' '
        . ($registro->apellidos ?? '')
    );
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>Registro de Auditoría #{{ $registro->id }}</h2>
        <p>
            Detalle inalterable de la acción registrada por el sistema.
        </p>
    </div>
</div>

<section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;margin-bottom:18px;">
    <h3 style="margin-top:0;color:#0a3158;">Información General</h3>

    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($registro->created_at)->format('d/m/Y H:i:s') }}</p>
    <p><strong>Usuario:</strong> {{ $nombre !== '' ? $nombre : ($registro->usuario ?? 'Sistema') }}</p>
    <p><strong>Rol:</strong> {{ $registro->rol_nombre ?? '—' }}</p>
    <p><strong>Módulo:</strong> {{ $registro->modulo }}</p>
    <p><strong>Acción:</strong> {{ $registro->accion }}</p>
    <p><strong>Tabla afectada:</strong> {{ $registro->tabla_afectada ?? '—' }}</p>
    <p><strong>ID del registro:</strong> {{ $registro->registro_id ?? '—' }}</p>
    <p><strong>Descripción:</strong> {{ $registro->descripcion }}</p>
</section>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
    <section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Valores Anteriores</h3>

        @forelse($anteriores as $clave => $valor)
            <div style="padding:9px 0;border-bottom:1px solid #edf1f4;">
                <strong>{{ $clave }}:</strong>
                {{ is_array($valor) ? json_encode($valor,JSON_UNESCAPED_UNICODE) : (string)$valor }}
            </div>
        @empty
            <p>No se registraron valores anteriores.</p>
        @endforelse
    </section>

    <section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Valores Nuevos</h3>

        @forelse($nuevos as $clave => $valor)
            <div style="padding:9px 0;border-bottom:1px solid #edf1f4;">
                <strong>{{ $clave }}:</strong>
                {{ is_array($valor) ? json_encode($valor,JSON_UNESCAPED_UNICODE) : (string)$valor }}
            </div>
        @empty
            <p>No se registraron valores nuevos.</p>
        @endforelse
    </section>
</div>
@endsection
