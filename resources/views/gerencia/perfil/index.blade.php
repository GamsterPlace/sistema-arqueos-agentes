@extends('layouts.gerencia')

@section('title', 'Perfil')
@section('module-title', 'Perfil')

@push('styles')
<style>
    .profile-grid {
        display: grid;
        grid-template-columns: 360px minmax(0, 1fr);
        gap: 20px
    }

    .profile-card,
    .summary-card {
        border: 1px solid #e0e8ee;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(20, 57, 83, .05)
    }

    .profile-card {
        overflow: hidden
    }

    .profile-header {
        padding: 26px 22px;
        background: linear-gradient(145deg, #0b315d, #164c96 68%, #0b6b7f);
        color: #fff;
        text-align: center
    }

    .profile-avatar {
        width: 78px;
        height: 78px;
        display: grid;
        place-items: center;
        margin: 0 auto 14px;
        border: 3px solid rgba(255, 255, 255, .30);
        border-radius: 22px;
        background: rgba(255, 255, 255, .15);
        font-size: 30px;
        font-weight: 800
    }

    .profile-header h3 {
        margin: 0;
        font-size: 18px
    }

    .profile-header p {
        margin: 7px 0 0;
        color: rgba(255, 255, 255, .72);
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase
    }

    .profile-body {
        padding: 18px 20px
    }

    .profile-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 13px 0;
        border-bottom: 1px solid #edf1f4
    }

    .profile-row:last-child {
        border-bottom: 0
    }

    .profile-row span {
        color: #738596;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase
    }

    .profile-row strong {
        color: #173b59;
        font-size: 11px;
        text-align: right
    }

    .badge {
        display: inline-flex;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase
    }

    .badge.active {
        background: #eaf8ef;
        color: #1d7b4e
    }

    .badge.inactive {
        background: #fdecec;
        color: #b13c3c
    }

    .summary-panel {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px
    }

    .summary-card {
        padding: 18px
    }

    .summary-card span {
        display: block;
        color: #758697;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase
    }

    .summary-card strong {
        display: block;
        margin-top: 8px;
        color: #082d55;
        font-size: 24px
    }

    .summary-card.success {
        border-color: #c5e5d1;
        background: #f7fcf9
    }

    .summary-card.warning {
        border-color: #efd99f;
        background: #fffdf6
    }

    .summary-card.danger {
        border-color: #efc5c5;
        background: #fffafa
    }

    .summary-card.info {
        border-color: #c9dceb;
        background: #f7fbff
    }

    .account-actions {
        margin-top: 20px;
        padding: 18px;
        border: 1px solid #e0e8ee;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(20, 57, 83, .05)
    }

    .account-actions h3 {
        margin: 0;
        color: #0a3158;
        font-size: 15px
    }

    .account-actions p {
        margin: 6px 0 14px;
        color: #82909a;
        font-size: 10px
    }

    .action-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 15px;
        border-radius: 10px;
        background: #164c96;
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        text-decoration: none
    }

    @media(max-width:1000px) {
        .profile-grid {
            grid-template-columns: 1fr
        }
    }

    @media(max-width:700px) {
        .summary-panel {
            grid-template-columns: 1fr
        }
    }
</style>
@endpush

@section('content')
@php
    $nombreCompleto = trim(
        ($perfil->nombres ?? '')
        . ' '
        . ($perfil->apellidos ?? '')
    );

    $nombreMostrar =
        $nombreCompleto !== ''
            ? $nombreCompleto
            : $perfil->usuario;

    $inicial = mb_strtoupper(mb_substr($nombreMostrar, 0, 1));
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>Perfil de Gerencia</h2>
        <p>Información de la cuenta y resumen general del sistema.</p>
    </div>
</div>

<div class="profile-grid">
    <aside class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">{{ $inicial }}</div>
            <h3>{{ $nombreMostrar }}</h3>
            <p>Gerencia MICOOPE</p>
        </div>

        <div class="profile-body">
            <div class="profile-row">
                <span>Usuario</span>
                <strong>{{ $perfil->usuario }}</strong>
            </div>

            <div class="profile-row">
                <span>Nombres</span>
                <strong>{{ $perfil->nombres ?: 'No registrado' }}</strong>
            </div>

            <div class="profile-row">
                <span>Apellidos</span>
                <strong>{{ $perfil->apellidos ?: 'No registrado' }}</strong>
            </div>

            <div class="profile-row">
                <span>Rol</span>
                <strong>Gerencia</strong>
            </div>

            <div class="profile-row">
                <span>Estado</span>
                <strong>
                    <span class="badge {{ $perfil->estado === 'ACTIVO' ? 'active' : 'inactive' }}">
                        {{ $perfil->estado }}
                    </span>
                </strong>
            </div>
        </div>
    </aside>

    <section>
        <div class="summary-panel">
            <article class="summary-card info">
                <span>Total Agentes</span>
                <strong>{{ $resumen->total_agentes }}</strong>
            </article>

            <article class="summary-card success">
                <span>Agentes Activos</span>
                <strong>{{ $resumen->agentes_activos }}</strong>
            </article>

            <article class="summary-card info">
                <span>Promotores</span>
                <strong>{{ $resumen->total_promotores }}</strong>
            </article>

            <article class="summary-card">
                <span>Rutas Activas</span>
                <strong>{{ $resumen->rutas_activas }}</strong>
            </article>

            <article class="summary-card">
                <span>Regiones Activas</span>
                <strong>{{ $resumen->regiones_activas }}</strong>
            </article>

            <article class="summary-card success">
                <span>Arqueos Hoy</span>
                <strong>{{ $resumen->arqueos_hoy }}</strong>
            </article>

            <article class="summary-card danger">
                <span>Faltantes Hoy</span>
                <strong>{{ $resumen->faltantes_hoy }}</strong>
            </article>

            <article class="summary-card warning">
                <span>Sobrantes Hoy</span>
                <strong>{{ $resumen->sobrantes_hoy }}</strong>
            </article>
        </div>

        <div class="account-actions">
            <h3>Seguridad de la Cuenta</h3>
            <p>Puede actualizar su contraseña desde la opción de seguridad.</p>

            <a href="{{ url('/cambiar-password') }}" class="action-button">
                Cambiar contraseña
            </a>
        </div>
    </section>
</div>
@endsection
