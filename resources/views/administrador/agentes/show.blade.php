@extends('layouts.administrador')

@section('title','Detalle de Agente')
@section('module-title','Agentes')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>{{ $agente->nombre_negocio }}</h2>
        <p>
            {{ $agente->codigo_agente }} · {{ $agente->region_nombre }} · {{ $agente->ruta_nombre }}
        </p>
    </div>

    <a
        href="{{ route('administrador.agentes.edit',$agente->id) }}"
        style="display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;text-decoration:none;"
    >
        Editar Agente
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:20px;">
    <section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Información del Agente</h3>

        <p><strong>Código:</strong> {{ $agente->codigo_agente }}</p>
        <p><strong>Negocio:</strong> {{ $agente->nombre_negocio }}</p>
        <p><strong>Propietario:</strong> {{ $agente->nombre_propietario }}</p>
        <p><strong>Dirección:</strong> {{ $agente->direccion }}</p>
        <p><strong>Región:</strong> {{ $agente->region_nombre }}</p>
        <p><strong>Ruta:</strong> {{ $agente->ruta_codigo }} — {{ $agente->ruta_nombre }}</p>
        <p><strong>Estado:</strong> {{ $agente->estado }}</p>
    </section>

    <section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Cuenta de Acceso</h3>

        <p><strong>Usuario:</strong> {{ $agente->usuario }}</p>
        <p><strong>Nombres:</strong> {{ $agente->nombres ?: '—' }}</p>
        <p><strong>Apellidos:</strong> {{ $agente->apellidos ?: '—' }}</p>
        <p><strong>Estado de cuenta:</strong> {{ $agente->usuario_estado }}</p>
    </section>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;">
    @foreach([
        ['Total Arqueos',$resumen->total_arqueos],
        ['Arqueos Válidos',$resumen->arqueos_validos],
        ['Anulados',$resumen->anulados],
        ['Último Arqueo',$resumen->ultimo_arqueo ? \Carbon\Carbon::parse($resumen->ultimo_arqueo)->format('d/m/Y') : 'Sin registro'],
    ] as [$label,$value])
        <div style="padding:17px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
            <span style="display:block;color:#758697;font-size:8px;font-weight:800;text-transform:uppercase;">
                {{ $label }}
            </span>

            <strong style="display:block;margin-top:8px;color:#082d55;font-size:20px;">
                {{ $value }}
            </strong>
        </div>
    @endforeach
</div>
@endsection
