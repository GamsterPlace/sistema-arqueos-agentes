@extends('layouts.gerencia')

@section('title', 'Detalle de Arqueo')
@section('module-title', 'Todos los Arqueos')

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

    $diferencia = (float) $arqueo->diferencia;
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>Arqueo {{ $arqueo->numero_arqueo }}</h2>

        <p>
            Consulta general del arqueo registrado en el sistema.
        </p>
    </div>

    <div>
        <a
            href="{{ route(
                'gerencia.arqueos.imprimir',
                $arqueo->id
            ) }}"
            target="_blank"
            style="display:inline-flex;padding:10px 14px;border-radius:10px;background:#164c96;color:#fff;text-decoration:none;font-size:10px;font-weight:800;"
        >
            Imprimir
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:20px;">
    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">
            Información del Arqueo
        </h3>

        <p><strong>Tipo:</strong>
            {{ $arqueo->tipo === 'DIARIO_AGENTE'
                ? 'Arqueo del Agente'
                : (
                    $arqueo->tipo === 'VISITA_PROMOTOR'
                        ? 'Arqueo de Promotor'
                        : str_replace('_', ' ', $arqueo->tipo)
                ) }}
        </p>

        <p>
            <strong>Fecha:</strong>
            {{ \Carbon\Carbon::parse(
                $arqueo->fecha_arqueo
            )->format('d/m/Y') }}
        </p>

        <p>
            <strong>Estado:</strong>
            {{ str_replace('_', ' ', $arqueo->estado) }}
        </p>

        <p>
            <strong>Extemporáneo:</strong>
            {{ $arqueo->fuera_fecha_ordinaria ? 'Sí' : 'No' }}
        </p>

        <p>
            <strong>Responsable:</strong>
            {{ $nombreResponsable !== ''
                ? $nombreResponsable
                : ($responsable->usuario ?? '—') }}
        </p>
    </section>

    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">
            Agente y Resultado
        </h3>

        <p>
            <strong>Agente:</strong>
            {{ $agente?->codigo_agente }}
            — {{ $agente?->nombre_negocio }}
        </p>

        <p>
            <strong>Propietario:</strong>
            {{ $agente?->nombre_propietario }}
        </p>

        <p>
            <strong>Región:</strong>
            {{ $region?->nombre ?? '—' }}
        </p>

        <p>
            <strong>Ruta:</strong>
            {{ $ruta?->codigo ?? '' }}
            {{ $ruta?->nombre ?? '—' }}
        </p>

        <p>
            <strong>Saldo Sistema:</strong>
            Q {{ number_format(
                (float) $arqueo->saldo_sistema,
                2
            ) }}
        </p>

        <p>
            <strong>Total Arqueado:</strong>
            Q {{ number_format(
                (float) $arqueo->total_arqueado,
                2
            ) }}
        </p>

        <p>
            <strong>Diferencia:</strong>
            Q {{ number_format(abs($diferencia), 2) }}

            @if($diferencia < 0)
                Faltante
            @elseif($diferencia > 0)
                Sobrante
            @else
                Exacto
            @endif
        </p>
    </section>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:20px;">
    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Billetes</h3>

        @forelse($billetes as $detalle)
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #edf1f4;">
                <span>
                    Q {{ number_format(
                        (float) $detalle->denominacion,
                        2
                    ) }}
                    × {{ $detalle->cantidad }}
                </span>

                <strong>
                    Q {{ number_format(
                        (float) $detalle->subtotal,
                        2
                    ) }}
                </strong>
            </div>
        @empty
            <p>Sin billetes registrados.</p>
        @endforelse
    </section>

    <section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Monedas</h3>

        @forelse($monedas as $detalle)
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #edf1f4;">
                <span>
                    Q {{ number_format(
                        (float) $detalle->denominacion,
                        2
                    ) }}
                    × {{ $detalle->cantidad }}
                </span>

                <strong>
                    Q {{ number_format(
                        (float) $detalle->subtotal,
                        2
                    ) }}
                </strong>
            </div>
        @empty
            <p>Sin monedas registradas.</p>
        @endforelse
    </section>
</div>

<section style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
    <h3 style="margin-top:0;color:#0a3158;">
        Observaciones
    </h3>

    <p>
        {{ $arqueo->observaciones ?: 'Sin observaciones' }}
    </p>
</section>
@endsection
