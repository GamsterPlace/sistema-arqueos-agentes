@extends('layouts.jefe')

@section('title', 'Detalle del Arqueo')
@section('module-title', 'Todos los Arqueos')

@push('styles')
<style>
    .page-actions{display:flex;gap:10px;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border:1px solid #d5dfe5;border-radius:10px;background:#fff;color:#31536e;font-size:10px;font-weight:800;text-decoration:none}
    .btn-primary{border-color:#164c96;background:#164c96;color:#fff}
    .detail-grid{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(300px,.7fr);gap:20px}
    .card{overflow:hidden;border:1px solid #e0e8ee;border-radius:18px;background:#fff;box-shadow:0 8px 24px rgba(20,57,83,.05)}
    .card+.card{margin-top:20px}.card-header{padding:17px 19px;border-bottom:1px solid #edf1f4;background:#fafcfd}.card-header h3{margin:0;color:#0a3158;font-size:15px}.card-body{padding:19px}
    .info-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.info-item{padding:13px;border:1px solid #e2e9ee;border-radius:11px;background:#fbfcfd}.info-item.full{grid-column:1/-1}
    .info-item span{display:block;color:#758697;font-size:8px;font-weight:800;text-transform:uppercase}.info-item strong{display:block;margin-top:6px;color:#173b59;font-size:11px;overflow-wrap:anywhere}
    .money-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.money-box{overflow:hidden;border:1px solid #e1e8ed;border-radius:12px}.money-box h4{margin:0;padding:11px 13px;background:#f5f8fa;color:#173b59;font-size:11px}
    .money-table{width:100%;border-collapse:collapse}.money-table th,.money-table td{padding:9px 11px;border-bottom:1px solid #edf1f4;text-align:right;font-size:9px}.money-table th:first-child,.money-table td:first-child{text-align:left}
    .summary-row{display:flex;justify-content:space-between;gap:12px;padding:12px 0;border-bottom:1px solid #edf1f4}.summary-row span{color:#6f808d;font-size:10px;font-weight:700}.summary-row strong{color:#163b5b;font-size:11px}
    .text-box{padding:13px;border:1px solid #e1e8ed;border-radius:11px;background:#fbfcfd;color:#4a6173;font-size:10px;line-height:1.55;white-space:pre-line}

    .btn-danger {
        border-color: #b33a34;
        background: #b33a34;
        color: #ffffff;
    }

    .btn-danger:hover {
        background: #982f2a;
    }

    .alert {
        margin-bottom: 18px;
        padding: 14px 16px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 800;
    }

    .alert-success {
        border: 1px solid #a9d8bc;
        background: #ebf8f0;
        color: #216b45;
    }

    .alert-error {
        border: 1px solid #e3aaa5;
        background: #fff0ef;
        color: #9a302a;
    }

    .modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 2200;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(10, 24, 37, .65);
        backdrop-filter: blur(3px);
    }

    .modal-backdrop.is-open {
        display: flex;
    }

    .modal-dialog {
        width: min(580px, 100%);
        overflow: hidden;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 24px 70px rgba(0, 0, 0, .28);
    }

    .modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 22px 16px;
        border-bottom: 1px solid #e7edf1;
    }

    .modal-header h3 {
        margin: 0;
        color: #8f2f2a;
        font-size: 20px;
        font-weight: 800;
    }

    .modal-header p {
        margin: 6px 0 0;
        color: #6c7f90;
        font-size: 10px;
        line-height: 1.45;
    }

    .modal-close {
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 10px;
        background: #eef3f7;
        color: #415d72;
        font-size: 22px;
        cursor: pointer;
    }

    .modal-body {
        padding: 20px 22px;
    }

    .modal-warning {
        margin-bottom: 16px;
        padding: 14px;
        border: 1px solid #e3aaa5;
        border-radius: 11px;
        background: #fff0ef;
        color: #8f2f2a;
        font-size: 10px;
        line-height: 1.5;
    }

    .modal-form-group + .modal-form-group {
        margin-top: 15px;
    }

    .modal-form-group label {
        display: block;
        margin-bottom: 7px;
        color: #52697b;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .modal-form-control {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid #ced9e1;
        border-radius: 10px;
        background: #ffffff;
        color: #2f4659;
        outline: none;
        box-sizing: border-box;
    }

    textarea.modal-form-control {
        min-height: 110px;
        resize: vertical;
    }

    .modal-form-control:focus {
        border-color: #b33a34;
        box-shadow: 0 0 0 3px rgba(179, 58, 52, .12);
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 22px 20px;
        border-top: 1px solid #e7edf1;
        background: #fbfcfd;
    }

    body.modal-open {
        overflow: hidden;
    }

    @media(max-width:950px){.detail-grid{grid-template-columns:1fr}}
    @media(max-width:650px){.info-grid,.money-grid{grid-template-columns:1fr}.info-item.full{grid-column:auto}}
</style>
@endpush

@section('content')
@php
    $nombreCreador = trim(($creador->nombres ?? '') . ' ' . ($creador->apellidos ?? ''));
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>Detalle del Arqueo</h2>
        <p>Consulte toda la información asociada al arqueo seleccionado.</p>
    </div>

    <div class="page-actions">
        <a href="{{ route('jefe.arqueos.index') }}" class="btn">Regresar</a>
        <a href="{{ route('jefe.arqueos.imprimir', $arqueo->id) }}" target="_blank" class="btn btn-primary">Imprimir PDF</a>

        @if ($puedeAnular)
            <button
                type="button"
                class="btn btn-danger"
                id="open-annulment-modal"
            >
                Anular arqueo
            </button>
        @endif
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-error">
        {{ $errors->first() }}
    </div>
@endif

<div class="detail-grid">
    <div>
        <section class="card">
            <header class="card-header"><h3>Información general</h3></header>

            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item"><span>Número de arqueo</span><strong>{{ $arqueo->numero_arqueo }}</strong></div>
                    <div class="info-item"><span>Fecha</span><strong>{{ $arqueo->fecha_arqueo->format('d/m/Y') }}</strong></div>
                    <div class="info-item"><span>Estado</span><strong>{{ str_replace('_', ' ', $arqueo->estado) }}</strong></div>
                    <div class="info-item"><span>Tipo</span><strong>{{ str_replace('_', ' ', $arqueo->tipo) }}</strong></div>
                    <div class="info-item"><span>Responsable</span><strong>{{ $nombreCreador !== '' ? $nombreCreador : ($creador->nombre_usuario ?? 'No disponible') }}</strong></div>
                    <div class="info-item"><span>Rol</span><strong>{{ $creador->rol_nombre ?? 'No disponible' }}</strong></div>
                    <div class="info-item"><span>Código del agente</span><strong>{{ $arqueo->codigo_agente_historico }}</strong></div>
                    <div class="info-item"><span>Negocio</span><strong>{{ $arqueo->nombre_negocio_historico }}</strong></div>
                    <div class="info-item"><span>Propietario</span><strong>{{ $arqueo->nombre_propietario_historico }}</strong></div>
                    <div class="info-item"><span>Ruta</span><strong>{{ $arqueo->ruta_historica }}</strong></div>
                    <div class="info-item"><span>Región</span><strong>{{ $arqueo->region_historica }}</strong></div>
                    <div class="info-item"><span>Extemporáneo</span><strong>{{ $arqueo->fuera_fecha_ordinaria ? 'Sí' : 'No' }}</strong></div>
                    <div class="info-item full"><span>Dirección</span><strong>{{ $arqueo->direccion_historica }}</strong></div>
                </div>
            </div>
        </section>

        <section class="card">
            <header class="card-header"><h3>Conteo de efectivo</h3></header>
            <div class="card-body">
                <div class="money-grid">
                    <div class="money-box">
                        <h4>Billetes</h4>
                        <table class="money-table">
                            <thead><tr><th>Denominación</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                            <tbody>
                                @forelse($billetes as $detalle)
                                    <tr>
                                        <td>Q {{ number_format((float) $detalle->denominacion, 2) }}</td>
                                        <td>{{ $detalle->cantidad }}</td>
                                        <td>Q {{ number_format((float) $detalle->subtotal, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">Sin registros.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="money-box">
                        <h4>Monedas</h4>
                        <table class="money-table">
                            <thead><tr><th>Denominación</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                            <tbody>
                                @forelse($monedas as $detalle)
                                    <tr>
                                        <td>Q {{ number_format((float) $detalle->denominacion, 2) }}</td>
                                        <td>{{ $detalle->cantidad }}</td>
                                        <td>Q {{ number_format((float) $detalle->subtotal, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">Sin registros.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <header class="card-header"><h3>Certificación y observaciones</h3></header>
            <div class="card-body">
                <div class="text-box">{{ $arqueo->certificacion ?: 'Sin certificación registrada.' }}</div>
                <div style="height:14px;"></div>
                <div class="text-box">{{ $arqueo->observaciones ?: 'Sin observaciones registradas.' }}</div>
            </div>
        </section>
    </div>

    <aside>
        <section class="card">
            <header class="card-header"><h3>Resumen</h3></header>
            <div class="card-body">
                <div class="summary-row"><span>Total billetes</span><strong>Q {{ number_format((float) $arqueo->total_billetes, 2) }}</strong></div>
                <div class="summary-row"><span>Total monedas</span><strong>Q {{ number_format((float) $arqueo->total_monedas, 2) }}</strong></div>
                <div class="summary-row"><span>Total arqueado</span><strong>Q {{ number_format((float) $arqueo->total_arqueado, 2) }}</strong></div>
                <div class="summary-row"><span>Saldo sistema</span><strong>Q {{ number_format((float) $arqueo->saldo_sistema, 2) }}</strong></div>
                <div class="summary-row"><span>Diferencia</span><strong>Q {{ number_format((float) $arqueo->diferencia, 2) }}</strong></div>
            </div>
        </section>
    </aside>
</div>

@if ($puedeAnular)
    <div
        class="modal-backdrop"
        id="annulment-modal"
        aria-hidden="true"
    >
        <div
            class="modal-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="annulment-modal-title"
        >
            <form
                method="POST"
                action="{{ route(
                    'jefe.arqueos.anular',
                    $arqueo->id
                ) }}"
            >
                @csrf

                <header class="modal-header">
                    <div>
                        <h3 id="annulment-modal-title">
                            Anular arqueo
                        </h3>

                        <p>
                            La anulación es irreversible.
                            El arqueo y sus firmas se conservarán
                            como evidencia histórica.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="modal-close"
                        id="close-annulment-modal"
                        aria-label="Cerrar"
                    >
                        ×
                    </button>
                </header>

                <div class="modal-body">
                    <div class="modal-warning">
                        Está por anular el arqueo
                        <strong>
                            {{ $arqueo->numero_arqueo }}
                        </strong>.
                        Debe registrar el motivo y confirmar
                        con su contraseña institucional.
                    </div>

                    <div class="modal-form-group">
                        <label for="motivo_anulacion">
                            Motivo de la anulación
                        </label>

                        <textarea
                            name="motivo_anulacion"
                            id="motivo_anulacion"
                            class="modal-form-control"
                            minlength="10"
                            maxlength="500"
                            required
                            placeholder="Explique claramente el motivo de la anulación..."
                        >{{ old('motivo_anulacion') }}</textarea>
                    </div>

                    <div class="modal-form-group">
                        <label for="password">
                            Contraseña
                        </label>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="modal-form-control"
                            required
                            autocomplete="current-password"
                            placeholder="Ingrese su contraseña"
                        >
                    </div>
                </div>

                <footer class="modal-footer">
                    <button
                        type="button"
                        class="btn"
                        id="cancel-annulment-modal"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        Confirmar anulación
                    </button>
                </footer>
            </form>
        </div>
    </div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal =
            document.getElementById('annulment-modal');

        const openButton =
            document.getElementById('open-annulment-modal');

        const closeButton =
            document.getElementById('close-annulment-modal');

        const cancelButton =
            document.getElementById('cancel-annulment-modal');

        if (! modal) {
            return;
        }

        const openModal = () => {
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');

            const motivo =
                document.getElementById('motivo_anulacion');

            if (motivo) {
                setTimeout(() => motivo.focus(), 100);
            }
        };

        const closeModal = () => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
        };

        openButton?.addEventListener('click', openModal);
        closeButton?.addEventListener('click', closeModal);
        cancelButton?.addEventListener('click', closeModal);

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        @if (
            $errors->has('motivo_anulacion')
            || $errors->has('password')
        )
            openModal();
        @endif
    });
</script>
@endpush

@endsection
