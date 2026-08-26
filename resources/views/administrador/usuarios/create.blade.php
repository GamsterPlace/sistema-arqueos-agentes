@extends('layouts.administrador')

@section('title', 'Crear Usuario')
@section('module-title', 'Usuarios')

@push('styles')

<style>
    .form-card{max-width:900px;padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;box-shadow:0 8px 24px rgba(20,57,83,.05)}
    .form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
    .field label{display:block;margin-bottom:6px;color:#617586;font-size:9px;font-weight:800;text-transform:uppercase}
    .field input,.field select{width:100%;min-height:43px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;background:#fff;color:#30485b}
    .errors{margin-bottom:18px;padding:13px 15px;border:1px solid #efc5c5;border-radius:12px;background:#fff8f8;color:#a13b3b;font-size:10px}
    .actions{display:flex;justify-content:flex-end;gap:10px;margin-top:18px}
    .btn{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border:0;border-radius:10px;font-size:10px;font-weight:800;text-decoration:none;cursor:pointer}
    .primary{background:#164c96;color:#fff}.secondary{background:#edf2f5;color:#3d596f}
    @media(max-width:700px){.form-grid{grid-template-columns:1fr}.actions{flex-direction:column}.btn{width:100%}}
</style>

@endpush

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Crear Usuario</h2>
        <p>Registre una nueva cuenta y asigne su rol de acceso.</p>
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
    action="{{ route('administrador.usuarios.store') }}"
    class="form-card"
>
    @csrf

    <div class="form-grid">
        <div class="field">
            <label>Nombres</label>
            <input type="text" name="nombres" value="{{ old('nombres') }}" required>
        </div>

        <div class="field">
            <label>Apellidos</label>
            <input type="text" name="apellidos" value="{{ old('apellidos') }}" required>
        </div>

        <div class="field">
            <label>Usuario</label>
            <input type="text" name="usuario" value="{{ old('usuario') }}" required>
        </div>

        <div class="field">
            <label>Rol</label>
            <select name="rol_id" required>
                <option value="">Seleccione</option>
                @foreach($roles as $rol)
                    <option value="{{ $rol->id }}" @selected(old('rol_id') == $rol->id)>
                        {{ $rol->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Estado</label>
            <select name="estado" required>
                <option value="ACTIVO" @selected(old('estado','ACTIVO') === 'ACTIVO')>ACTIVO</option>
                <option value="INACTIVO" @selected(old('estado') === 'INACTIVO')>INACTIVO</option>
            </select>
        </div>

        <div></div>

        <div class="field">
            <label>Contraseña</label>
            <input type="password" name="password" required>
        </div>

        <div class="field">
            <label>Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" required>
        </div>
    </div>

    <div class="actions">
        <a href="{{ route('administrador.usuarios.index') }}" class="btn secondary">
            Cancelar
        </a>

        <button type="submit" class="btn primary">
            Guardar Usuario
        </button>
    </div>
</form>
@endsection
