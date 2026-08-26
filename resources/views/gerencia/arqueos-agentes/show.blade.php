@extends('layouts.gerencia')

@section('title', 'Detalle de Arqueo')
@section('module-title', 'Arqueos por Agente')

@section('content')
@php
    $agente = $arqueo->agente;
    $ruta = $agente?->ruta;
    $region = $ruta?->region;

    $nombreResponsable = trim(
        ($responsable->nombres ?? '')
        . ' '
        . ($responsable->apellidos ?? '')
    );
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>Arqueo {{ $arqueo->numero_arqueo }}</h2>
        <p>Consulta detallada del arqueo realizado por el Agente.</p>
    </div>

    <div>
        <a
            href="{{ route('gerencia.arqueos-agentes.imprimir',$arqueo->id) }}"
            target="_blank"
            style="display:inline-flex;padding:10px 14px;border-radius:10px;background:#164c96;color:#fff;text-decoration:none;font-size:10px;font-weight:800;"
        >
            Imprimir
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:20px;">
    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Información General</h3>
        <p><strong>Agente:</strong> {{ $agente?->codigo_agente }} — {{ $agente?->nombre_negocio }}</p>
        <p><strong>Propietario:</strong> {{ $agente?->nombre_propietario }}</p>
        <p><strong>Región:</strong> {{ $region?->nombre ?? '—' }}</p>
        <p><strong>Ruta:</strong> {{ $ruta?->codigo ?? '' }} {{ $ruta?->nombre ?? '—' }}</p>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($arqueo->fecha_arqueo)->format('d/m/Y') }}</p>
        <p><strong>Estado:</strong> {{ str_replace('_',' ',$arqueo->estado) }}</p>
        <p><strong>Extemporáneo:</strong> {{ $arqueo->fuera_fecha_ordinaria ? 'Sí' : 'No' }}</p>
    </section>

    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Resultado</h3>
        <p><strong>Saldo Sistema:</strong> Q {{ number_format((float)$arqueo->saldo_sistema,2) }}</p>
        <p><strong>Total Arqueado:</strong> Q {{ number_format((float)$arqueo->total_arqueado,2) }}</p>
        <p><strong>Diferencia:</strong> Q {{ number_format(abs((float)$arqueo->diferencia),2) }}</p>
        <p><strong>Responsable:</strong> {{ $nombreResponsable !== '' ? $nombreResponsable : ($responsable->usuario ?? '—') }}</p>
        <p><strong>Observaciones:</strong> {{ $arqueo->observaciones ?: 'Sin observaciones' }}</p>
    </section>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Billetes</h3>
        @forelse($billetes as $detalle)
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #edf1f4;">
                <span>Q {{ number_format((float)$detalle->denominacion,2) }} × {{ $detalle->cantidad }}</span>
                <strong>Q {{ number_format((float)$detalle->subtotal,2) }}</strong>
            </div>
        @empty
            <p>Sin billetes registrados.</p>
        @endforelse
    </section>

    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Monedas</h3>
        @forelse($monedas as $detalle)
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #edf1f4;">
                <span>Q {{ number_format((float)$detalle->denominacion,2) }} × {{ $detalle->cantidad }}</span>
                <strong>Q {{ number_format((float)$detalle->subtotal,2) }}</strong>
            </div>
        @empty
            <p>Sin monedas registradas.</p>
        @endforelse
    </section>
</div>
@endsection
