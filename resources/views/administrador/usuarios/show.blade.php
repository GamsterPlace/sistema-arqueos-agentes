@extends('layouts.administrador')

@section('title', 'Detalle de Usuario')
@section('module-title', 'Usuarios')

@section('content')
@php
    $nombre = trim(
        ($usuario->nombres ?? '')
        . ' '
        . ($usuario->apellidos ?? '')
    );
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>{{ $nombre !== '' ? $nombre : $usuario->usuario }}</h2>
        <p>Información y administración de la cuenta seleccionada.</p>
    </div>

    <a
        href="{{ route('administrador.usuarios.edit',$usuario->id) }}"
        style="display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;text-decoration:none;"
    >
        Editar Usuario
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
    <section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Datos de la Cuenta</h3>

        <p><strong>Usuario:</strong> {{ $usuario->usuario }}</p>
        <p><strong>Nombres:</strong> {{ $usuario->nombres ?: '—' }}</p>
        <p><strong>Apellidos:</strong> {{ $usuario->apellidos ?: '—' }}</p>
        <p><strong>Rol:</strong> {{ $usuario->rol_nombre }}</p>
        <p><strong>Estado:</strong> {{ $usuario->estado }}</p>
    </section>

    <section style="padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
        <h3 style="margin-top:0;color:#0a3158;">Restablecer Contraseña</h3>

        <p style="color:#758697;font-size:10px;">
            Defina una nueva contraseña para esta cuenta.
        </p>

        <form
            method="POST"
            action="{{ route('administrador.usuarios.password',$usuario->id) }}"
        >
            @csrf
            @method('PATCH')

            <div style="display:grid;gap:10px;">
                <input
                    type="password"
                    name="password"
                    placeholder="Nueva contraseña"
                    required
                    style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
                >

                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirmar contraseña"
                    required
                    style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
                >

                <button
                    type="submit"
                    style="min-height:40px;border:0;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;cursor:pointer;"
                >
                    Actualizar Contraseña
                </button>
            </div>
        </form>
    </section>
</div>
