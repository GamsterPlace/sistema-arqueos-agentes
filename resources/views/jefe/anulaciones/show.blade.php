@extends('layouts.jefe')

@section('title', 'Revisar Anulación')
@section('module-title', 'Anular Arqueos')

@push('styles')
<style>
    .review-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .review-actions-right {
        display: flex;
        align-items: center;
        gap: 9px;
        flex-wrap: wrap;
    }

    .btn-back,
    .btn-print,
    .btn-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        box-sizing: border-box;
    }

    .btn-back {
        border: 1px solid #d5dfe5;
        background: #fff;
        color: #31536e;
    }

    .btn-print {
        border: 1px solid #c8d7e3;
        background: #edf5fb;
        color: #164c96;
    }

    .btn-cancel {
        border: 1px solid #b33a34;
        background: #b33a34;
        color: #fff;
    }

    .btn-cancel:hover {
        background: #9e2e29;
        border-color: #9e2e29;
    }

    .review-card {
        position: relative;
        overflow: hidden;
        padding: 26px;
        border: 1px solid #dfe6eb;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(20, 57, 83, .05);
    }

    .review-header {
        position: relative;
        display: grid;
        grid-template-columns: 150px 1fr 190px;
        align-items: center;
        gap: 18px;
        padding-bottom: 18px;
        border-bottom: 4px solid #17734b;
    }

    .review-header::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -4px;
        width: 32%;
        height: 4px;
        background: #f2c21a;
    }

    .review-brand {
        color: #164c96;
        font-size: 19px;
        font-weight: 900;
    }

    .review-title {
        text-align: center;
    }

    .review-title h2 {
        margin: 0;
        color: #153f63;
        font-size: 22px;
    }

    .review-title p {
        margin: 4px 0 0;
        color: #536b7d;
        font-weight: 800;
        letter-spacing: .08em;
    }

    .review-number {
        text-align: right;
    }

    .review-number span {
        display: block;
        color: #8795a0;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .review-number strong {
        display: block;
        margin-top: 3px;
        color: #b33a34;
        font-size: 16px;
        overflow-wrap: anywhere;
    }

    .status-row {
        display: flex;
        gap: 9px;
        flex-wrap: wrap;
        margin: 20px 0;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 850;
        text-transform: uppercase;
    }

    .badge.agent {
        background: #edf5fb;
        color: #164c96;
    }

    .badge.promoter {
        background: #eef7f2;
        color: #28704d;
    }

    .badge.pending {
        border: 1px solid #c9dcf4;
        background: #eef5ff;
        color: #285b9b;
    }

    .badge.certified {
        border: 1px solid #c7e4d2;
        background: #effaf3;
        color: #197247;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .info-item {
        padding: 12px 14px;
        border-bottom: 1px solid #cfd9e0;
    }

    .info-item span {
        display: block;
        color: #7d8c97;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .info-item strong {
        display: block;
        margin-top: 4px;
        color: #203d54;
        font-size: 13px;
    }

    .money-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 22px;
        margin-top: 22px;
    }

    .money-box {
        overflow: hidden;
        border: 1px solid #dfe6eb;
        border-radius: 13px;
    }

    .money-box h3 {
        margin: 0;
        padding: 12px 14px;
        background: #f5f8fa;
        color: #164c96;
        font-size: 13px;
    }

    .money-table {
        width: 100%;
        border-collapse: collapse;
    }

    .money-table th,
    .money-table td {
        padding: 9px 12px;
        border-top: 1px solid #edf1f4;
        text-align: right;
        font-size: 12px;
    }

    .money-table th:first-child,
    .money-table td:first-child {
        text-align: left;
    }

    .totals {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-top: 22px;
    }

    .total-box {
        padding: 15px;
        border: 1px solid #dfe6eb;
        border-radius: 12px;
        background: #fafcfd;
    }

    .total-box span {
        display: block;
        color: #7b8c98;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .total-box strong {
        display: block;
        margin-top: 5px;
        color: #153f63;
        font-size: 16px;
    }

    .text-section {
        margin-top: 20px;
        padding: 16px;
        border: 1px solid #dfe6eb;
        border-radius: 12px;
    }

    .text-section h3 {
        margin: 0 0 8px;
        color: #164c96;
        font-size: 13px;
    }

    .text-section p {
        margin: 0;
        color: #455f72;
        font-size: 12px;
        line-height: 1.6;
    }

    .warning-box {
        margin-top: 22px;
        padding: 14px 16px;
        border: 1px solid #f0d2cf;
        border-radius: 12px;
        background: #fff6f5;
        color: #9d3b35;
        font-size: 11px;
        line-height: 1.55;
    }

    .warning-box strong {
        display: block;
        margin-bottom: 3px;
        font-size: 12px;
    }

    .modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 2500;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(10, 24, 37, .62);
        backdrop-filter: blur(3px);
    }

    .modal-backdrop.is-open {
        display: flex;
    }

    .modal-dialog {
        width: min(520px, 100%);
        overflow: hidden;
        border: 1px solid #dce5eb;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 24px 70px rgba(0, 0, 0, .25);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 22px;
        border-bottom: 1px solid #e7edf1;
    }

    .modal-header h3 {
        margin: 0;
        color: #9e2e29;
        font-size: 17px;
    }

    .modal-header p {
        margin: 5px 0 0;
        color: #6c7f90;
        font-size: 11px;
    }

    .modal-close {
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 9px;
        background: #eef3f7;
        color: #415d72;
        font-size: 21px;
        cursor: pointer;
    }

    .modal-body {
        padding: 20px 22px;
    }

    .modal-info {
        padding: 12px 14px;
        margin-bottom: 16px;
        border: 1px solid #dce6ed;
        border-radius: 11px;
        background: #f8fafb;
    }

    .modal-info span {
        display: block;
        color: #7a8b98;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .modal-info strong {
        display: block;
        margin-top: 4px;
        color: #173b59;
    }

    .modal-body label {
        display: block;
        margin: 13px 0 7px;
        color: #52697b;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .modal-input,
    .modal-textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ced9e1;
        border-radius: 10px;
        outline: none;
        box-sizing: border-box;
        font-family: inherit;
        color: #30485b;
    }

    .modal-input {
        min-height: 43px;
    }

    .modal-textarea {
        min-height: 95px;
        resize: vertical;
    }

    .modal-help {
        margin: 12px 0 0;
        color: #84939e;
        font-size: 10px;
        line-height: 1.45;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 22px 20px;
        border-top: 1px solid #e7edf1;
        background: #fbfcfd;
    }

    .modal-btn {
        min-height: 40px;
        padding: 0 15px;
        border: 1px solid #d5dfe5;
        border-radius: 10px;
        background: #fff;
        color: #3d596f;
        font-weight: 800;
        cursor: pointer;
    }

    .modal-btn.danger {
        border-color: #b33a34;
        background: #b33a34;
        color: #fff;
    }

    @media (max-width: 950px) {
        .review-header {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .review-number {
            text-align: center;
        }

        .info-grid,
        .totals {
            grid-template-columns: 1fr 1fr;
        }

        .money-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .info-grid,
        .totals {
            grid-template-columns: 1fr;
        }

        .review-card {
            padding: 18px;
        }

        .review-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .review-actions-right {
            width: 100%;
            flex-direction: column;
        }

        .btn-back,
        .btn-print,
        .btn-cancel {
            width: 100%;
        }

        .modal-footer {
            flex-direction: column-reverse;
        }
    }
</style>
@endpush

@section('content')
@php
    $fecha = $arqueo->fecha_arqueo
        ? \Carbon\Carbon::parse($arqueo->fecha_arqueo)->format('d/m/Y')
        : '—';

    $horaInicio = $arqueo->hora_inicio
        ? \Carbon\Carbon::parse($arqueo->hora_inicio)->format('H:i')
        : '—';

    $horaFin = $arqueo->hora_fin
        ? \Carbon\Carbon::parse($arqueo->hora_fin)->format('H:i')
        : '—';

    $esPromotor = $arqueo->tipo === 'VISITA_PROMOTOR';

    $tipoTexto = match ($arqueo->tipo) {
        'DIARIO_AGENTE' => 'Arqueo de Agente',
        'VISITA_PROMOTOR' => 'Arqueo de Promotor',
        'VISITA_AUDITORIA' => 'Arqueo de Auditoría',
        default => str_replace('_', ' ', $arqueo->tipo),
    };

    $estadoTexto = str_replace('_', ' ', $arqueo->estado);

    $rutaPdf = $esPromotor
        ? route('jefe.arqueos-promotores.imprimir', $arqueo->id)
        : route('jefe.arqueos.imprimir', $arqueo->id);

    $puedeAnular = in_array(
        $arqueo->estado,
        ['PENDIENTE_CERTIFICACION', 'CERTIFICADO'],
        true
    );
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>Revisar Arqueo para Anulación</h2>
        <p>
            Revise cuidadosamente la información antes de realizar una anulación.
        </p>
    </div>
</div>

@if ($errors->any())
    <div class="alert-message warning">
        {{ $errors->first() }}
    </div>
@endif

<div class="review-actions">
    <a href="{{ route('jefe.anulaciones.index') }}" class="btn-back">
        ← Regresar
    </a>

    <div class="review-actions-right">
        <a
            href="{{ $rutaPdf }}"
            target="_blank"
            class="btn-print"
        >
            Imprimir PDF
        </a>

        @if ($puedeAnular)
            <button
                type="button"
                class="btn-cancel"
                id="open-cancel-modal"
            >
                Anular Arqueo
            </button>
        @endif
    </div>
</div>

<article class="review-card">
    <header class="review-header">
        <div class="review-brand">
            ECOSABA
        </div>

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
        <span class="badge {{ $esPromotor ? 'promoter' : 'agent' }}">
            {{ $tipoTexto }}
        </span>

        <span class="badge {{ $arqueo->estado === 'CERTIFICADO' ? 'certified' : 'pending' }}">
            {{ $estadoTexto }}
        </span>
    </div>

    <section class="info-grid">
        <div class="info-item">
            <span>Fecha</span>
            <strong>{{ $fecha }}</strong>
        </div>

        <div class="info-item">
            <span>Hora inicio</span>
            <strong>{{ $horaInicio }}</strong>
        </div>

        <div class="info-item">
            <span>Hora finalización</span>
            <strong>{{ $horaFin }}</strong>
        </div>

        <div class="info-item">
            <span>Código de agente</span>
            <strong>{{ $arqueo->codigo_agente_historico ?? '—' }}</strong>
        </div>

        <div class="info-item">
            <span>Nombre del negocio</span>
            <strong>{{ $arqueo->nombre_negocio_historico ?? '—' }}</strong>
        </div>

        <div class="info-item">
            <span>Propietario</span>
            <strong>{{ $arqueo->nombre_propietario_historico ?? '—' }}</strong>
        </div>

        <div class="info-item">
            <span>Dirección</span>
            <strong>{{ $arqueo->direccion_historica ?? '—' }}</strong>
        </div>

        <div class="info-item">
            <span>Ruta</span>
            <strong>{{ $arqueo->ruta_historica ?? '—' }}</strong>
        </div>

        <div class="info-item">
            <span>Región</span>
            <strong>{{ $arqueo->region_historica ?? '—' }}</strong>
        </div>
    </section>

    <section class="money-grid">
        <div class="money-box">
            <h3>Billetes</h3>

            <table class="money-table">
                <thead>
                    <tr>
                        <th>Denominación</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($billetes as $detalle)
                        <tr>
                            <td>
                                Q {{ number_format((float) $detalle->denominacion, 2) }}
                            </td>
                            <td>
                                {{ $detalle->cantidad }}
                            </td>
                            <td>
                                Q {{ number_format((float) $detalle->subtotal, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                Sin billetes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="money-box">
            <h3>Monedas</h3>

            <table class="money-table">
                <thead>
                    <tr>
                        <th>Denominación</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($monedas as $detalle)
                        <tr>
                            <td>
                                Q {{ number_format((float) $detalle->denominacion, 2) }}
                            </td>
                            <td>
                                {{ $detalle->cantidad }}
                            </td>
                            <td>
                                Q {{ number_format((float) $detalle->subtotal, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                Sin monedas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="totals">
        <div class="total-box">
            <span>Total billetes</span>
            <strong>
                Q {{ number_format((float) $arqueo->total_billetes, 2) }}
            </strong>
        </div>

        <div class="total-box">
            <span>Total monedas</span>
            <strong>
                Q {{ number_format((float) $arqueo->total_monedas, 2) }}
            </strong>
        </div>

        <div class="total-box">
            <span>Total arqueado</span>
            <strong>
                Q {{ number_format((float) $arqueo->total_arqueado, 2) }}
            </strong>
        </div>

        <div class="total-box">
            <span>Saldo sistema</span>
            <strong>
                Q {{ number_format((float) $arqueo->saldo_sistema, 2) }}
            </strong>
        </div>

        <div class="total-box">
            <span>Diferencia</span>
            <strong>
                Q {{ number_format((float) $arqueo->diferencia, 2) }}
            </strong>
        </div>
    </section>

    <section class="text-section">
        <h3>Observaciones</h3>
        <p>
            {{ $arqueo->observaciones ?: 'Sin observaciones registradas.' }}
        </p>
    </section>

    @if ($esPromotor)
        <section class="text-section">
            <h3>Calificación / Certificación</h3>
            <p>
                {{ $arqueo->certificacion ?: 'Sin calificación registrada.' }}
            </p>
        </section>
    @endif

    <div class="warning-box">
        <strong>Advertencia</strong>
        La anulación es una acción irreversible. El registro permanecerá
        almacenado con estado ANULADO y deberá conservar el motivo y la
        identidad del usuario que realizó la operación.
    </div>
</article>

@if ($puedeAnular)
    <div
        class="modal-backdrop"
        id="cancel-modal"
        aria-hidden="true"
    >
        <div
            class="modal-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="cancel-title"
        >
            <form
                method="POST"
                action="{{ route('jefe.arqueos.anular', $arqueo->id) }}"
            >
                @csrf

                <div class="modal-header">
                    <div>
                        <h3 id="cancel-title">
                            Anular arqueo
                        </h3>

                        <p>
                            Esta operación no puede revertirse.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="modal-close"
                        id="close-cancel-modal"
                        aria-label="Cerrar"
                    >
                        ×
                    </button>
                </div>

                <div class="modal-body">
                    <div class="modal-info">
                        <span>Número de arqueo</span>
                        <strong>{{ $arqueo->numero_arqueo }}</strong>
                    </div>

                    <div class="modal-info">
                        <span>Tipo</span>
                        <strong>{{ $tipoTexto }}</strong>
                    </div>

                    <label for="cancel-reason">
                        Motivo de anulación
                    </label>

                    <textarea
                        name="motivo"
                        id="cancel-reason"
                        class="modal-textarea"
                        required
                        maxlength="500"
                        placeholder="Describa claramente el motivo de la anulación"
                    >{{ old('motivo') }}</textarea>

                    <label for="cancel-password">
                        Contraseña institucional
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="cancel-password"
                        class="modal-input"
                        required
                        autocomplete="current-password"
                        placeholder="Ingrese su contraseña"
                    >

                    <p class="modal-help">
                        Al confirmar, el arqueo cambiará a estado ANULADO.
                    </p>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="modal-btn"
                        id="cancel-cancel-modal"
                    >
                        Regresar
                    </button>

                    <button
                        type="submit"
                        class="modal-btn danger"
                    >
                        Confirmar anulación
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@if ($puedeAnular)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('cancel-modal');
    const openButton = document.getElementById('open-cancel-modal');
    const closeButton = document.getElementById('close-cancel-modal');
    const cancelButton = document.getElementById('cancel-cancel-modal');
    const reason = document.getElementById('cancel-reason');

    function openModal() {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');

        setTimeout(function () {
            reason.focus();
        }, 80);
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    }

    openButton.addEventListener('click', openModal);
    closeButton.addEventListener('click', closeModal);
    cancelButton.addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (
            event.key === 'Escape'
            && modal.classList.contains('is-open')
        ) {
            closeModal();
        }
    });

    @if ($errors->any())
        openModal();
    @endif
});
</script>
@endpush
@endif
