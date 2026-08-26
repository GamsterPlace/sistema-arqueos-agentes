@extends('layouts.auditoria')

@section('title', 'Realizar Arqueo')
@section('module-title', 'Realizar Arqueo')

@push('styles')
<style>
    .form-card{
        padding:20px;
        border:1px solid #e0e8ee;
        border-radius:18px;
        background:#fff;
        box-shadow:0 8px 24px rgba(20,57,83,.05);
    }

    .section-title{
        margin:0 0 14px;
        color:#0a3158;
        font-size:15px;
    }

    .field-grid{
        display:grid;
        grid-template-columns:repeat(2,1fr);
        gap:14px;
    }

    .field label{
        display:block;
        margin-bottom:6px;
        color:#617586;
        font-size:9px;
        font-weight:800;
        text-transform:uppercase;
    }

    .field input,
    .field select,
    .field textarea{
        width:100%;
        min-height:43px;
        padding:0 12px;
        border:1px solid #ced9e1;
        border-radius:10px;
        background:#fff;
        color:#30485b;
    }

    .field textarea{
        min-height:90px;
        padding-top:10px;
        resize:vertical;
    }

    .cash-grid{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:18px;
        margin-top:18px;
    }

    .cash-table{
        width:100%;
        border-collapse:collapse;
    }

    .cash-table th,
    .cash-table td{
        padding:9px;
        border-bottom:1px solid #edf1f4;
        text-align:left;
        font-size:10px;
    }

    .cash-table input{
        width:100%;
        min-height:36px;
        padding:0 9px;
        border:1px solid #ced9e1;
        border-radius:8px;
    }

    .summary-grid{
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:12px;
        margin-top:18px;
    }

    .summary-card{
        padding:15px;
        border:1px solid #e0e8ee;
        border-radius:14px;
        background:#f9fbfc;
    }

    .summary-card span{
        display:block;
        color:#758697;
        font-size:8px;
        font-weight:800;
        text-transform:uppercase;
    }

    .summary-card strong{
        display:block;
        margin-top:6px;
        color:#082d55;
        font-size:20px;
    }

    .errors{
        margin-bottom:18px;
        padding:13px 15px;
        border:1px solid #efc5c5;
        border-radius:12px;
        background:#fff8f8;
        color:#a13b3b;
        font-size:10px;
    }

    .actions{
        display:flex;
        justify-content:flex-end;
        gap:10px;
        margin-top:20px;
    }

    .btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-height:40px;
        padding:0 15px;
        border:0;
        border-radius:10px;
        font-size:10px;
        font-weight:800;
        text-decoration:none;
        cursor:pointer;
    }

    .primary{
        background:#1e5d82;
        color:#fff;
    }

    .secondary{
        background:#edf2f5;
        color:#3d596f;
    }

    @media(max-width:850px){
        .field-grid,
        .cash-grid,
        .summary-grid{
            grid-template-columns:1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Realizar Arqueo de Auditoría</h2>
        <p>
            Seleccione un Agente, ingrese el efectivo encontrado y el saldo del sistema.
        </p>
    </div>
</div>

@if($errors->any())
    <div class="errors">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form
    method="POST"
    action="{{ route('auditoria.arqueos.store') }}"
    class="form-card"
    id="arqueoForm"
>
    @csrf

    <h3 class="section-title">
        Información General
    </h3>

    <div class="field-grid">
        <div class="field">
            <label>Agente</label>

            <select
                name="agente_id"
                id="agente_id"
                required
            >
                <option value="">
                    Seleccione un Agente
                </option>

                @foreach($agentes as $agente)
                    <option
                        value="{{ $agente->id }}"
                        data-codigo="{{ $agente->codigo_agente }}"
                        data-negocio="{{ $agente->nombre_negocio }}"
                        data-propietario="{{ $agente->nombre_propietario }}"
                        data-direccion="{{ $agente->direccion }}"
                        data-ruta="{{ $agente->ruta_codigo }} - {{ $agente->ruta_nombre }}"
                        data-region="{{ $agente->region_nombre }}"
                        @selected(
                            old('agente_id')
                            == $agente->id
                        )
                    >
                        {{ $agente->codigo_agente }}
                        — {{ $agente->nombre_negocio }}
                        — {{ $agente->region_nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Fecha</label>

            <input
                type="text"
                value="{{ now()->format('d/m/Y') }}"
                readonly
            >
        </div>

        <div class="field">
            <label>Propietario</label>

            <input
                type="text"
                id="propietario"
                readonly
            >
        </div>

        <div class="field">
            <label>Región</label>

            <input
                type="text"
                id="region"
                readonly
            >
        </div>

        <div class="field">
            <label>Ruta</label>

            <input
                type="text"
                id="ruta"
                readonly
            >
        </div>

        <div class="field">
            <label>Dirección</label>

            <input
                type="text"
                id="direccion"
                readonly
            >
        </div>
    </div>

    <div class="cash-grid">
        <section>
            <h3 class="section-title">
                Billetes
            </h3>

            <table class="cash-table">
                <thead>
                    <tr>
                        <th>Denominación</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($billetes as $denominacion)
                        @php
                            $clave = (string) $denominacion;
                        @endphp

                        <tr>
                            <td>
                                Q {{ number_format(
                                    $denominacion,
                                    2
                                ) }}
                            </td>

                            <td>
                                <input
                                    type="number"
                                    min="0"
                                    step="1"
                                    name="billetes[{{ $clave }}]"
                                    value="{{ old(
                                        'billetes.' . $clave,
                                        0
                                    ) }}"
                                    class="cantidad"
                                    data-denominacion="{{ $denominacion }}"
                                    data-tipo="billete"
                                >
                            </td>

                            <td
                                class="subtotal"
                                data-subtotal-for="billete-{{ $clave }}"
                            >
                                Q 0.00
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section>
            <h3 class="section-title">
                Monedas
            </h3>

            <table class="cash-table">
                <thead>
                    <tr>
                        <th>Denominación</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($monedas as $denominacion)
                        @php
                            $clave = number_format(
                                $denominacion,
                                2,
                                '.',
                                ''
                            );
                        @endphp

                        <tr>
                            <td>
                                Q {{ number_format(
                                    $denominacion,
                                    2
                                ) }}
                            </td>

                            <td>
                                <input
                                    type="number"
                                    min="0"
                                    step="1"
                                    name="monedas[{{ $clave }}]"
                                    value="{{ old(
                                        'monedas.' . $clave,
                                        0
                                    ) }}"
                                    class="cantidad"
                                    data-denominacion="{{ $denominacion }}"
                                    data-tipo="moneda"
                                >
                            </td>

                            <td
                                class="subtotal"
                                data-subtotal-for="moneda-{{ $clave }}"
                            >
                                Q 0.00
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>

    <div class="summary-grid">
        <article class="summary-card">
            <span>Total Billetes</span>
            <strong id="totalBilletes">
                Q 0.00
            </strong>
        </article>

        <article class="summary-card">
            <span>Total Monedas</span>
            <strong id="totalMonedas">
                Q 0.00
            </strong>
        </article>

        <article class="summary-card">
            <span>Total Arqueado</span>
            <strong id="totalArqueado">
                Q 0.00
            </strong>
        </article>

        <article class="summary-card">
            <span>Diferencia</span>
            <strong id="diferencia">
                Q 0.00
            </strong>
        </article>
    </div>

    <div
        class="field-grid"
        style="margin-top:18px;"
    >
        <div class="field">
            <label>Saldo Sistema</label>

            <input
                type="number"
                name="saldo_sistema"
                id="saldo_sistema"
                step="0.01"
                min="0"
                value="{{ old(
                    'saldo_sistema',
                    '0.00'
                ) }}"
                required
            >
        </div>

        <div class="field">
            <label>Resultado</label>

            <input
                type="text"
                id="resultado"
                value="Exacto"
                readonly
            >
        </div>

        <div
            class="field"
            style="grid-column:1/-1;"
        >
            <label>Observaciones</label>

            <textarea
                name="observaciones"
                placeholder="Observaciones del arqueo..."
            >{{ old('observaciones') }}</textarea>
        </div>
    </div>

    <div class="actions">
        <a
            href="{{ route(
                'auditoria.arqueos.index'
            ) }}"
            class="btn secondary"
        >
            Cancelar
        </a>

        <button
            type="submit"
            class="btn primary"
        >
            Finalizar Arqueo
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const agente = document.getElementById('agente_id');
    const propietario = document.getElementById('propietario');
    const region = document.getElementById('region');
    const ruta = document.getElementById('ruta');
    const direccion = document.getElementById('direccion');

    const cantidades = document.querySelectorAll('.cantidad');
    const saldo = document.getElementById('saldo_sistema');

    function actualizarAgente() {
        const option = agente.options[agente.selectedIndex];

        propietario.value = option?.dataset?.propietario || '';
        region.value = option?.dataset?.region || '';
        ruta.value = option?.dataset?.ruta || '';
        direccion.value = option?.dataset?.direccion || '';
    }

    function dinero(valor) {
        return 'Q ' + Number(valor || 0).toFixed(2);
    }

    function recalcular() {
        let totalBilletes = 0;
        let totalMonedas = 0;

        cantidades.forEach(function (input) {
            const denominacion = Number(input.dataset.denominacion || 0);
            const cantidad = Number(input.value || 0);
            const subtotal = denominacion * cantidad;
            const tipo = input.dataset.tipo;

            if (tipo === 'billete') {
                totalBilletes += subtotal;
            } else {
                totalMonedas += subtotal;
            }

            const clave =
                tipo
                + '-'
                + (
                    tipo === 'moneda'
                        ? denominacion.toFixed(2)
                        : String(denominacion)
                );

            const td = document.querySelector(
                '[data-subtotal-for="' + clave + '"]'
            );

            if (td) {
                td.textContent = dinero(subtotal);
            }
        });

        const total = totalBilletes + totalMonedas;
        const saldoSistema = Number(saldo.value || 0);
        const diferencia = total - saldoSistema;

        document.getElementById('totalBilletes').textContent =
            dinero(totalBilletes);

        document.getElementById('totalMonedas').textContent =
            dinero(totalMonedas);

        document.getElementById('totalArqueado').textContent =
            dinero(total);

        document.getElementById('diferencia').textContent =
            dinero(Math.abs(diferencia));

        document.getElementById('resultado').value =
            diferencia < 0
                ? 'Faltante'
                : (
                    diferencia > 0
                        ? 'Sobrante'
                        : 'Exacto'
                );
    }

    agente.addEventListener('change', actualizarAgente);

    cantidades.forEach(function (input) {
        input.addEventListener('input', recalcular);
    });

    saldo.addEventListener('input', recalcular);

    actualizarAgente();
    recalcular();
});
</script>
@endpush
