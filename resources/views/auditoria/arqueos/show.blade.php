@extends('layouts.auditoria')

@section('title', 'Detalle de Arqueo')
@section('module-title', 'Historial')

@push('styles')
<style>
    .page-actions{
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
    }

    .btn-primary-custom,.btn-secondary-custom{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-height:42px;
        padding:0 17px;
        border-radius:11px;
        font-size:10px;
        font-weight:850;
        text-decoration:none;
        transition:.2s;
    }

    .btn-primary-custom{
        border:0;
        background:linear-gradient(135deg,#164c96,#1c64b5);
        color:#fff;
        box-shadow:0 8px 18px rgba(22,76,150,.16);
    }

    .btn-secondary-custom{
        border:1px solid #d8e2e8;
        background:#fff;
        color:#31536e;
    }

    .btn-primary-custom svg,.btn-secondary-custom svg{
        width:15px;
        height:15px;
        margin-right:7px;
        stroke:currentColor;
    }

    .detail-grid{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:18px;
        margin-bottom:18px;
    }

    .detail-card{
        padding:20px;
        border:1px solid #e0e8ee;
        border-radius:18px;
        background:#fff;
        box-shadow:0 6px 20px rgba(20,57,83,.035);
    }

    .detail-card h3{
        margin:0 0 17px;
        padding-bottom:10px;
        border-bottom:1px solid #edf1f4;
        color:#0a3158;
        font-size:14px;
    }

    .info-list{
        display:grid;
        gap:11px;
    }

    .info-row{
        display:grid;
        grid-template-columns:135px minmax(0,1fr);
        gap:12px;
        align-items:start;
    }

    .info-label{
        color:#748695;
        font-size:9px;
        font-weight:850;
        text-transform:uppercase;
    }

    .info-value{
        color:#263f53;
        font-size:11px;
        font-weight:650;
        word-break:break-word;
    }

    .status-badge,.result-badge{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-height:26px;
        padding:0 10px;
        border-radius:999px;
        font-size:8px;
        font-weight:850;
        text-transform:uppercase;
    }

    .status-certified{background:#eaf8f0;color:#167746}
    .status-pending{background:#fff6df;color:#9a6913}
    .status-cancelled{background:#fff0ef;color:#a83c38}
    .status-default{background:#edf2f5;color:#50687a}

    .result-faltante{background:#fff0ef;color:#ad3f3b}
    .result-sobrante{background:#eaf8f0;color:#167746}
    .result-exacto{background:#edf4fa;color:#315f86}

    .money-value{
        color:#0a3158;
        font-weight:850;
    }

    .difference-value{
        display:inline-block;
        margin-right:8px;
        color:#0a3158;
        font-size:12px;
        font-weight:900;
    }

    .denominations-grid{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:18px;
        margin-bottom:18px;
    }

    .denomination-table{
        width:100%;
        border-collapse:collapse;
    }

    .denomination-table th{
        padding:10px 9px;
        background:#f7f9fb;
        color:#667b8c;
        font-size:8px;
        font-weight:850;
        text-align:right;
        text-transform:uppercase;
    }

    .denomination-table th:first-child{
        text-align:left;
    }

    .denomination-table td{
        padding:10px 9px;
        border-top:1px solid #edf1f4;
        color:#334f64;
        font-size:10px;
        text-align:right;
    }

    .denomination-table td:first-child{
        text-align:left;
        font-weight:800;
        color:#17364f;
    }

    .denomination-table tfoot td{
        background:#fbfcfd;
        color:#0a3158;
        font-weight:900;
    }

    .observations-card{
        margin-bottom:18px;
    }

    .observations-text{
        margin:0;
        color:#4e6576;
        font-size:10px;
        line-height:1.7;
        white-space:pre-line;
    }

    .signatures-card{
        margin-bottom:10px;
    }

    .signatures-grid{
        display:grid;
        grid-template-columns:repeat(3,minmax(0,1fr));
        gap:18px;
    }

    .signature-box{
        min-height:125px;
        padding:16px;
        border:1px solid #e1e8ed;
        border-radius:14px;
        background:#fbfcfd;
        text-align:center;
    }

    .signature-role{
        display:block;
        margin-bottom:14px;
        color:#758697;
        font-size:8px;
        font-weight:850;
        letter-spacing:.35px;
        text-transform:uppercase;
    }

    .signature-name{
        min-height:32px;
        display:flex;
        align-items:flex-end;
        justify-content:center;
        padding-bottom:6px;
        border-bottom:1px solid #81909b;
        color:#17364f;
        font-size:10px;
        font-weight:850;
    }

    .signature-meta{
        margin:7px 0 0;
        color:#718391;
        font-size:8px;
        line-height:1.5;
    }

    .signature-pending{
        color:#a07019;
        font-style:italic;
    }

    @media(max-width:900px){
        .detail-grid,.denominations-grid{
            grid-template-columns:1fr;
        }

        .signatures-grid{
            grid-template-columns:1fr;
        }
    }

    @media(max-width:650px){
        .page-actions{
            width:100%;
            margin-top:14px;
        }

        .page-actions a{
            width:100%;
        }

        .info-row{
            grid-template-columns:1fr;
            gap:3px;
        }
    }
</style>
@endpush

@section('content')
@php
    $diferencia = (float) $arqueo->diferencia;

    $estadoClase = match($arqueo->estado) {
        'CERTIFICADO' => 'status-certified',
        'PENDIENTE_CERTIFICACION' => 'status-pending',
        'ANULADO' => 'status-cancelled',
        default => 'status-default',
    };

    $estadoTexto = match($arqueo->estado) {
        'PENDIENTE_CERTIFICACION' => 'Pendiente',
        'CERTIFICADO' => 'Certificado',
        'ANULADO' => 'Anulado',
        default => str_replace('_',' ',$arqueo->estado),
    };

    if ($diferencia < -0.004) {
        $resultadoTexto = 'Faltante';
        $resultadoClase = 'result-faltante';
    } elseif ($diferencia > 0.004) {
        $resultadoTexto = 'Sobrante';
        $resultadoClase = 'result-sobrante';
    } else {
        $resultadoTexto = 'Exacto';
        $resultadoClase = 'result-exacto';
    }

    $firmaRealizador = \Illuminate\Support\Facades\DB::table('firmas_arqueos')
        ->where('arqueo_id',$arqueo->id)
        ->where('tipo_firma','REALIZADOR')
        ->where('valida',1)
        ->first();

    $firmaValidador = \Illuminate\Support\Facades\DB::table('firmas_arqueos')
        ->where('arqueo_id',$arqueo->id)
        ->where('tipo_firma','VALIDADOR')
        ->where('valida',1)
        ->first();

    $firmaCertificador = \Illuminate\Support\Facades\DB::table('firmas_arqueos')
        ->where('arqueo_id',$arqueo->id)
        ->where('tipo_firma','CERTIFICADOR')
        ->where('valida',1)
        ->first();
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>{{ $arqueo->numero_arqueo }}</h2>
        <p>Detalle del arqueo realizado por Auditoría MICOOPE.</p>
    </div>

    <div class="page-actions">
        <a href="{{ route('auditoria.arqueos.index') }}" class="btn-secondary-custom">
            Volver
        </a>

        <a
            target="_blank"
            href="{{ route('auditoria.arqueos.imprimir',$arqueo->id) }}"
            class="btn-primary-custom"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8">
                <path d="M6 9V3h12v6"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <path d="M6 14h12v7H6z"/>
            </svg>
            Imprimir PDF
        </a>
    </div>
</div>

<div class="detail-grid">
    <section class="detail-card">
        <h3>Agente Auditado</h3>

        <div class="info-list">
            <div class="info-row">
                <span class="info-label">Código</span>
                <span class="info-value">{{ $arqueo->codigo_agente_historico }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Negocio</span>
                <span class="info-value">{{ $arqueo->nombre_negocio_historico }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Propietario</span>
                <span class="info-value">{{ $arqueo->nombre_propietario_historico }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Dirección</span>
                <span class="info-value">{{ $arqueo->direccion_historica }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Región</span>
                <span class="info-value">{{ $arqueo->region_historica }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Ruta</span>
                <span class="info-value">{{ $arqueo->ruta_historica }}</span>
            </div>
        </div>
    </section>

    <section class="detail-card">
        <h3>Resultado del Arqueo</h3>

        <div class="info-list">
            <div class="info-row">
                <span class="info-label">Fecha</span>
                <span class="info-value">
                    {{ \Carbon\Carbon::parse($arqueo->fecha_arqueo)->format('d/m/Y') }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Estado</span>
                <span class="info-value">
                    <span class="status-badge {{ $estadoClase }}">{{ $estadoTexto }}</span>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Total Billetes</span>
                <span class="info-value money-value">
                    Q {{ number_format((float)$arqueo->total_billetes,2) }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Total Monedas</span>
                <span class="info-value money-value">
                    Q {{ number_format((float)$arqueo->total_monedas,2) }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Total Arqueado</span>
                <span class="info-value money-value">
                    Q {{ number_format((float)$arqueo->total_arqueado,2) }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Saldo Sistema</span>
                <span class="info-value money-value">
                    Q {{ number_format((float)$arqueo->saldo_sistema,2) }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Diferencia</span>
                <span class="info-value">
                    <span class="difference-value">
                        Q {{ number_format($diferencia,2) }}
                    </span>

                    <span class="result-badge {{ $resultadoClase }}">
                        {{ $resultadoTexto }}
                    </span>
                </span>
            </div>
        </div>
    </section>
</div>

<div class="denominations-grid">
    <section class="detail-card">
        <h3>Billetes</h3>

        <table class="denomination-table">
            <thead>
                <tr>
                    <th>Denominación</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($billetes as $detalle)
                    <tr>
                        <td>Q {{ number_format((float)$detalle->denominacion,2) }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>Q {{ number_format((float)$detalle->subtotal,2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Sin detalle de billetes.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total Billetes</td>
                    <td>Q {{ number_format((float)$arqueo->total_billetes,2) }}</td>
                </tr>
            </tfoot>
        </table>
    </section>

    <section class="detail-card">
        <h3>Monedas</h3>

        <table class="denomination-table">
            <thead>
                <tr>
                    <th>Denominación</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monedas as $detalle)
                    <tr>
                        <td>Q {{ number_format((float)$detalle->denominacion,2) }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>Q {{ number_format((float)$detalle->subtotal,2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Sin detalle de monedas.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total Monedas</td>
                    <td>Q {{ number_format((float)$arqueo->total_monedas,2) }}</td>
                </tr>
            </tfoot>
        </table>
    </section>
</div>

<section class="detail-card observations-card">
    <h3>Observaciones</h3>
    <p class="observations-text">{{ $arqueo->observaciones ?: 'Sin observaciones registradas.' }}</p>
</section>

<section class="detail-card signatures-card">
    <h3>Firmas Electrónicas</h3>

    <div class="signatures-grid">
        <article class="signature-box">
            <span class="signature-role">Realizador · Auditoría MICOOPE</span>

            @if($firmaRealizador)
                <div class="signature-name">
                    {{ trim($firmaRealizador->nombres_historicos.' '.$firmaRealizador->apellidos_historicos) }}
                </div>
                <p class="signature-meta">
                    Firmado electrónicamente<br>
                    {{ \Carbon\Carbon::parse($firmaRealizador->fecha_firma)->format('d/m/Y H:i') }}
                </p>
            @else
                <div class="signature-name signature-pending">Pendiente</div>
                <p class="signature-meta">Firma electrónica no registrada.</p>
            @endif
        </article>

        <article class="signature-box">
            <span class="signature-role">Validador · Propietario o Receptor Pagador</span>

            @if($firmaValidador)
                <div class="signature-name">
                    {{ trim($firmaValidador->nombres_historicos.' '.$firmaValidador->apellidos_historicos) }}
                </div>
                <p class="signature-meta">
                    Validado electrónicamente<br>
                    {{ \Carbon\Carbon::parse($firmaValidador->fecha_firma)->format('d/m/Y H:i') }}
                </p>
            @else
                <div class="signature-name signature-pending">Pendiente de validación</div>
                <p class="signature-meta">El Agente MICOOPE aún no ha validado el arqueo.</p>
            @endif
        </article>

        <article class="signature-box">
            <span class="signature-role">Certificador · Jefe de Agentes MICOOPE</span>

            @if($firmaCertificador)
                <div class="signature-name">
                    {{ trim($firmaCertificador->nombres_historicos.' '.$firmaCertificador->apellidos_historicos) }}
                </div>
                <p class="signature-meta">
                    Certificado electrónicamente<br>
                    {{ \Carbon\Carbon::parse($firmaCertificador->fecha_firma)->format('d/m/Y H:i') }}
                </p>
            @else
                <div class="signature-name signature-pending">Pendiente de certificación</div>
                <p class="signature-meta">Pendiente de firma del Jefe de Agentes MICOOPE.</p>
            @endif
        </article>
    </div>
</section>
@endsection
