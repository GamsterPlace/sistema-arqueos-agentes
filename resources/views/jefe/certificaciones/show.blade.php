@extends('layouts.jefe')

@section('title', 'Revisar Arqueo')
@section('module-title', 'Certificar Arqueos')

@push('styles')
<style>
.review-actions{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:18px}
.btn-back,.btn-print,.btn-certify{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:40px;padding:0 14px;border-radius:10px;font-weight:800;text-decoration:none;cursor:pointer}
.btn-back{border:1px solid #d5dfe5;background:#fff;color:#31536e}
.btn-print{border:1px solid #c8d7e3;background:#edf5fb;color:#164c96}
.btn-certify{border:1px solid #197247;background:#197247;color:#fff}
.btn-certify:disabled{opacity:.55;cursor:not-allowed}
.review-card{position:relative;overflow:hidden;background:#fff;border:1px solid #dfe6eb;border-radius:18px;padding:26px;box-shadow:0 8px 24px rgba(20,57,83,.05)}
.review-header{position:relative;display:grid;grid-template-columns:150px 1fr 190px;align-items:center;gap:18px;padding-bottom:18px;border-bottom:4px solid #17734b}
.review-header:after{content:"";position:absolute;left:0;bottom:-4px;width:32%;height:4px;background:#f2c21a}
.review-brand{font-weight:900;color:#164c96;font-size:19px}
.review-title{text-align:center}
.review-title h2{margin:0;color:#153f63;font-size:22px}
.review-title p{margin:4px 0 0;color:#536b7d;font-weight:800;letter-spacing:.08em}
.review-number{text-align:right}
.review-number span{display:block;color:#8795a0;font-size:11px;text-transform:uppercase;font-weight:800}
.review-number strong{display:block;color:#b33a34;font-size:16px;overflow-wrap:anywhere}
.status-row{display:flex;gap:10px;flex-wrap:wrap;margin:20px 0}
.status-badge{padding:7px 10px;border-radius:999px;background:#fff5d9;color:#81620d;font-size:11px;font-weight:850}
.info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}
.info-item{padding:12px 14px;border-bottom:1px solid #cfd9e0}
.info-item span{display:block;color:#7d8c97;font-size:10px;text-transform:uppercase;font-weight:800}
.info-item strong{display:block;margin-top:4px;color:#203d54;font-size:13px}
.money-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-top:22px}
.money-box{border:1px solid #dfe6eb;border-radius:13px;overflow:hidden}
.money-box h3{margin:0;padding:12px 14px;background:#f5f8fa;color:#164c96;font-size:13px}
.money-table{width:100%;border-collapse:collapse}
.money-table th,.money-table td{padding:9px 12px;border-top:1px solid #edf1f4;text-align:right;font-size:12px}
.money-table th:first-child,.money-table td:first-child{text-align:left}
.totals{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:22px}
.total-box{padding:15px;border:1px solid #dfe6eb;border-radius:12px;background:#fafcfd}
.total-box span{display:block;color:#7b8c98;font-size:10px;text-transform:uppercase;font-weight:800}
.total-box strong{display:block;margin-top:5px;color:#153f63;font-size:17px}
.total-box.difference.positive strong{color:#197247}
.total-box.difference.negative strong{color:#b33a34}
.text-section{margin-top:20px;padding:16px;border:1px solid #dfe6eb;border-radius:12px}
.text-section h3{margin:0 0 8px;color:#164c96;font-size:13px}
.text-section p{margin:0;color:#455f72;line-height:1.6;font-size:12px}
.signatures{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:24px}
.signature{padding:15px;border:1px solid #dfe6eb;border-radius:12px;text-align:center}
.signature span{display:block;color:#7c8b96;font-size:10px;text-transform:uppercase;font-weight:800}
.signature strong{display:block;margin-top:7px;color:#203d54;font-size:12px}
.signature small{display:block;margin-top:5px;color:#82909a}
.signature.pending{background:#fafbfc;color:#7e8c96}
.modal-backdrop{position:fixed;inset:0;z-index:2500;display:none;align-items:center;justify-content:center;padding:20px;background:rgba(10,24,37,.62);backdrop-filter:blur(3px)}
.modal-backdrop.is-open{display:flex}
.modal-dialog{width:min(500px,100%);overflow:hidden;border-radius:18px;background:#fff;border:1px solid #dce5eb;box-shadow:0 24px 70px rgba(0,0,0,.25)}
.modal-header{display:flex;justify-content:space-between;gap:14px;padding:20px 22px;border-bottom:1px solid #e7edf1}
.modal-header h3{margin:0;color:#0a3158;font-size:17px}.modal-header p{margin:5px 0 0;color:#6c7f90;font-size:11px}
.modal-close{width:34px;height:34px;border:0;border-radius:9px;background:#eef3f7;color:#415d72;font-size:21px;cursor:pointer}
.modal-body{padding:20px 22px}.modal-info{padding:12px 14px;margin-bottom:16px;border:1px solid #dce6ed;border-radius:11px;background:#f8fafb}
.modal-info span{display:block;color:#7a8b98;font-size:10px;text-transform:uppercase;font-weight:800}.modal-info strong{display:block;margin-top:4px;color:#173b59}
.modal-body label{display:block;margin-bottom:7px;color:#52697b;font-size:10px;text-transform:uppercase;font-weight:800}
.modal-input{width:100%;min-height:43px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;box-sizing:border-box}
.modal-help{color:#84939e;font-size:10px;line-height:1.45}
.modal-footer{display:flex;justify-content:flex-end;gap:10px;padding:16px 22px 20px;border-top:1px solid #e7edf1;background:#fbfcfd}
.modal-btn{min-height:40px;padding:0 15px;border:1px solid #d5dfe5;border-radius:10px;background:#fff;color:#3d596f;font-weight:800;cursor:pointer}
.modal-btn.confirm{border-color:#197247;background:#197247;color:#fff}
@media(max-width:900px){.review-header{grid-template-columns:1fr;text-align:center}.review-number{text-align:center}.info-grid,.totals,.signatures{grid-template-columns:1fr 1fr}.money-grid{grid-template-columns:1fr}}
@media(max-width:600px){.info-grid,.totals,.signatures{grid-template-columns:1fr}.review-card{padding:18px}.review-actions{align-items:stretch;flex-direction:column}.review-actions>div{display:flex;gap:8px;flex-wrap:wrap}.modal-footer{flex-direction:column-reverse}}
</style>
@endpush

@section('content')
@php
    $fecha = \Carbon\Carbon::parse($arqueo->fecha_arqueo)->format('d/m/Y');
    $horaInicio = $arqueo->hora_inicio ? \Carbon\Carbon::parse($arqueo->hora_inicio)->format('H:i') : '—';
    $horaFin = $arqueo->hora_fin ? \Carbon\Carbon::parse($arqueo->hora_fin)->format('H:i') : '—';
    $diferencia = (float) $arqueo->diferencia;

    $nombreFirma = function ($firma) {
        if (! $firma) return 'Pendiente';
        $nombre = trim(($firma->nombres_historicos ?? '') . ' ' . ($firma->apellidos_historicos ?? ''));
        return $nombre !== '' ? $nombre : 'Firma electrónica registrada';
    };
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>Revisión para Certificación</h2>
        <p>Verifique cuidadosamente la información y las firmas existentes antes de certificar el arqueo.</p>
    </div>
</div>

@if ($errors->any())
    <div class="alert-message warning">{{ $errors->first() }}</div>
@endif

<div class="review-actions">
    <a href="{{ route('jefe.certificaciones.index') }}" class="btn-back">← Regresar</a>

    <div>
        <a href="{{ route('jefe.arqueos-promotores.imprimir', $arqueo->id) }}"
           target="_blank" class="btn-print">Imprimir PDF</a>

        @if ($puedeCertificar)
            <button type="button" class="btn-certify" id="open-certification">
                Certificar Arqueo
            </button>
        @else
            <button type="button" class="btn-certify" disabled>
                No disponible para certificación
            </button>
        @endif
    </div>
</div>

<article class="review-card">
    <header class="review-header">
        <div class="review-brand">ECOSABA</div>

        <div class="review-title">
            <h2>ARQUEO Y CORTE DE CAJA</h2>
            <p>AGENTES MICOOPE</p>
        </div>

        <div class="review-number">
            <span>Número de arqueo</span>
            <strong>{{ $arqueo->numero_arqueo }}</strong>
        </div>
    </header>

    <div class="status-row">
        <span class="status-badge">{{ str_replace('_', ' ', $arqueo->estado) }}</span>
        <span class="status-badge">VISITA DE PROMOTOR</span>
    </div>

    <section class="info-grid">
        <div class="info-item"><span>Fecha</span><strong>{{ $fecha }}</strong></div>
        <div class="info-item"><span>Hora inicio</span><strong>{{ $horaInicio }}</strong></div>
        <div class="info-item"><span>Hora fin</span><strong>{{ $horaFin }}</strong></div>
        <div class="info-item"><span>Agente No.</span><strong>{{ $arqueo->codigo_agente_historico ?? '—' }}</strong></div>
        <div class="info-item"><span>Nombre del negocio</span><strong>{{ $arqueo->nombre_negocio_historico ?? '—' }}</strong></div>
        <div class="info-item"><span>Propietario</span><strong>{{ $arqueo->nombre_propietario_historico ?? '—' }}</strong></div>
        <div class="info-item"><span>Dirección</span><strong>{{ $arqueo->direccion_historica ?? '—' }}</strong></div>
        <div class="info-item"><span>Ruta</span><strong>{{ $arqueo->ruta_historica ?? '—' }}</strong></div>
        <div class="info-item"><span>Región</span><strong>{{ $arqueo->region_historica ?? '—' }}</strong></div>
    </section>

    <section class="money-grid">
        <div class="money-box">
            <h3>Billetes</h3>
            <table class="money-table">
                <thead><tr><th>Denominación</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                <tbody>
                @forelse ($billetes as $detalle)
                    <tr>
                        <td>Q {{ number_format((float) $detalle->denominacion, 2) }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>Q {{ number_format((float) $detalle->subtotal, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">Sin billetes registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="money-box">
            <h3>Monedas</h3>
            <table class="money-table">
                <thead><tr><th>Denominación</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                <tbody>
                @forelse ($monedas as $detalle)
                    <tr>
                        <td>Q {{ number_format((float) $detalle->denominacion, 2) }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>Q {{ number_format((float) $detalle->subtotal, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">Sin monedas registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="totals">
        <div class="total-box"><span>Total billetes</span><strong>Q {{ number_format((float) $arqueo->total_billetes, 2) }}</strong></div>
        <div class="total-box"><span>Total monedas</span><strong>Q {{ number_format((float) $arqueo->total_monedas, 2) }}</strong></div>
        <div class="total-box"><span>Total arqueado</span><strong>Q {{ number_format((float) $arqueo->total_arqueado, 2) }}</strong></div>
        <div class="total-box"><span>Saldo sistema</span><strong>Q {{ number_format((float) $arqueo->saldo_sistema, 2) }}</strong></div>
        <div class="total-box difference {{ $diferencia > 0 ? 'positive' : ($diferencia < 0 ? 'negative' : '') }}">
            <span>Diferencia</span>
            <strong>Q {{ number_format($diferencia, 2) }}</strong>
        </div>
    </section>

    <section class="text-section">
        <h3>Certificación</h3>
        <p>{{ $arqueo->certificacion ?: 'Sin texto de certificación registrado.' }}</p>
    </section>

    <section class="text-section">
        <h3>Observaciones</h3>
        <p>{{ $arqueo->observaciones ?: 'Sin observaciones.' }}</p>
    </section>

    <section class="signatures">
        <div class="signature">
            <span>Realizado por Promotor</span>
            <strong>{{ $nombreFirma($firmaPromotor) }}</strong>
            <small>{{ $firmaPromotor?->fecha_firma ? \Carbon\Carbon::parse($firmaPromotor->fecha_firma)->format('d/m/Y H:i') : '—' }}</small>
        </div>

        <div class="signature">
            <span>Validado por Agente</span>
            <strong>{{ $nombreFirma($firmaAgente) }}</strong>
            <small>{{ $firmaAgente?->fecha_firma ? \Carbon\Carbon::parse($firmaAgente->fecha_firma)->format('d/m/Y H:i') : '—' }}</small>
        </div>

        <div class="signature {{ $firmaJefe ? '' : 'pending' }}">
            <span>Certificación Jefe de Agentes</span>
            <strong>{{ $nombreFirma($firmaJefe) }}</strong>
            <small>{{ $firmaJefe?->fecha_firma ? \Carbon\Carbon::parse($firmaJefe->fecha_firma)->format('d/m/Y H:i') : 'Pendiente' }}</small>
        </div>
    </section>
</article>

@if ($puedeCertificar)
<div class="modal-backdrop" id="certification-modal" aria-hidden="true">
    <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="certification-title">
        <form method="POST" action="{{ route('jefe.certificaciones.certificar', $arqueo->id) }}">
            @csrf

            <div class="modal-header">
                <div>
                    <h3 id="certification-title">Certificar arqueo</h3>
                    <p>Confirme su identidad para registrar la certificación electrónica.</p>
                </div>
                <button type="button" class="modal-close" id="close-certification" aria-label="Cerrar">×</button>
            </div>

            <div class="modal-body">
                <div class="modal-info">
                    <span>Número de arqueo</span>
                    <strong>{{ $arqueo->numero_arqueo }}</strong>
                </div>

                <label for="certification-password">Contraseña institucional</label>
                <input type="password" name="password" id="certification-password"
                       class="modal-input" required autocomplete="current-password"
                       placeholder="Ingrese su contraseña">

                <p class="modal-help">
                    Al confirmar se registrará su firma electrónica como Jefe de Agentes
                    y el arqueo cambiará a estado CERTIFICADO.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="modal-btn" id="cancel-certification">Cancelar</button>
                <button type="submit" class="modal-btn confirm">Confirmar certificación</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@if ($puedeCertificar)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('certification-modal');
    const openButton = document.getElementById('open-certification');
    const closeButton = document.getElementById('close-certification');
    const cancelButton = document.getElementById('cancel-certification');
    const password = document.getElementById('certification-password');

    function openModal() {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        setTimeout(() => password.focus(), 80);
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        password.value = '';
    }

    openButton.addEventListener('click', openModal);
    closeButton.addEventListener('click', closeModal);
    cancelButton.addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
});
</script>
@endpush
@endif
