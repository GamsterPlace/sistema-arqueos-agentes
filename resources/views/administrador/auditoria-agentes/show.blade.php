@extends('layouts.administrador')

@section('title', 'Detalle Auditoría de Agente')
@section('module-title', 'Auditoría de Agentes')

@section('content')
@php
    $nombreAuditor = trim(
        ($auditor->nombres ?? '')
        . ' '
        . ($auditor->apellidos ?? '')
    );

    $diferencia = (float) $arqueo->diferencia;
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>{{ $arqueo->numero_arqueo }}</h2>
        <p>Detalle completo del arqueo realizado por Auditoría.</p>
    </div>

    <a
        target="_blank"
        href="{{ route('administrador.auditoria-agentes.imprimir',$arqueo->id) }}"
        style="display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;text-decoration:none;"
    >
        Imprimir
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px;">
    <section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Información de Auditoría</h3>
        <p><strong>Auditor:</strong> {{ $nombreAuditor !== '' ? $nombreAuditor : ($auditor->usuario ?? '—') }}</p>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($arqueo->fecha_arqueo)->format('d/m/Y') }}</p>
        <p><strong>Estado:</strong> {{ str_replace('_',' ',$arqueo->estado) }}</p>
        <p><strong>Extemporáneo:</strong> {{ $arqueo->fuera_fecha_ordinaria ? 'Sí' : 'No' }}</p>
    </section>

    <section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Agente Auditado</h3>
        <p><strong>Código:</strong> {{ $arqueo->codigo_agente_historico }}</p>
        <p><strong>Negocio:</strong> {{ $arqueo->nombre_negocio_historico }}</p>
        <p><strong>Propietario:</strong> {{ $arqueo->nombre_propietario_historico }}</p>
        <p><strong>Dirección:</strong> {{ $arqueo->direccion_historica }}</p>
        <p><strong>Región:</strong> {{ $arqueo->region_historica }}</p>
        <p><strong>Ruta:</strong> {{ $arqueo->ruta_historica }}</p>
    </section>
</div>

<section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;margin-bottom:18px;">
    <h3 style="margin-top:0;color:#0a3158;">Resultado</h3>
    <p><strong>Total Billetes:</strong> Q {{ number_format((float)$arqueo->total_billetes,2) }}</p>
    <p><strong>Total Monedas:</strong> Q {{ number_format((float)$arqueo->total_monedas,2) }}</p>
    <p><strong>Total Arqueado:</strong> Q {{ number_format((float)$arqueo->total_arqueado,2) }}</p>
    <p><strong>Saldo Sistema:</strong> Q {{ number_format((float)$arqueo->saldo_sistema,2) }}</p>
    <p>
        <strong>Diferencia:</strong>
        Q {{ number_format(abs($diferencia),2) }}
        @if($diferencia < 0)
            Faltante
        @elseif($diferencia > 0)
            Sobrante
        @else
            Exacto
        @endif
    </p>
    <p><strong>Certificación:</strong> {{ $arqueo->certificacion ?: '—' }}</p>
    <p><strong>Observaciones:</strong> {{ $arqueo->observaciones ?: 'Sin observaciones' }}</p>
</section>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
    <section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Billetes</h3>
        @forelse($billetes as $detalle)
            <p>
                Q {{ number_format((float)$detalle->denominacion,2) }}
                × {{ $detalle->cantidad }}
                = Q {{ number_format((float)$detalle->subtotal,2) }}
            </p>
        @empty
            <p>Sin detalle de billetes.</p>
        @endforelse
    </section>

    <section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Monedas</h3>
        @forelse($monedas as $detalle)
            <p>
                Q {{ number_format((float)$detalle->denominacion,2) }}
                × {{ $detalle->cantidad }}
                = Q {{ number_format((float)$detalle->subtotal,2) }}
            </p>
        @empty
            <p>Sin detalle de monedas.</p>
        @endforelse
    </section>
</div>
@endsection
