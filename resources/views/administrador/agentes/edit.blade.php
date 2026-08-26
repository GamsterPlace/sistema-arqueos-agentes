@extends('layouts.administrador')

@section('title','Editar Agente')
@section('module-title','Agentes')

@push('styles')

<style>
    .card{padding:20px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;box-shadow:0 8px 24px rgba(20,57,83,.05)}
    .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
    .field label{display:block;margin-bottom:6px;color:#617586;font-size:9px;font-weight:800;text-transform:uppercase}
    .field input,.field select,.field textarea{width:100%;min-height:43px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;background:#fff;color:#30485b}
    .field textarea{min-height:90px;padding-top:10px;resize:vertical}
    .actions{display:flex;justify-content:flex-end;gap:10px;margin-top:18px}
    .btn{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border:0;border-radius:10px;font-size:10px;font-weight:800;text-decoration:none;cursor:pointer}
    .primary{background:#164c96;color:#fff}.secondary{background:#edf2f5;color:#3d596f}
    .errors{margin-bottom:18px;padding:13px 15px;border:1px solid #efc5c5;border-radius:12px;background:#fff8f8;color:#a13b3b;font-size:10px}
    @media(max-width:700px){.grid{grid-template-columns:1fr}.actions{flex-direction:column}.btn{width:100%}}
</style>

@endpush

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Editar Agente</h2>
        <p>
            Actualice la información comercial, cuenta y Ruta asignada.
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

<form method="POST" action="{{ route('administrador.agentes.update',$agente->id) }}" class="card">
    @csrf
    @method('PUT')

    <div class="grid">
        <div class="field">
            <label>Código de Agente</label>
            <input type="text" name="codigo_agente" value="{{ old('codigo_agente',$agente->codigo_agente) }}" required>
        </div>

        <div class="field">
            <label>Nombre del Negocio</label>
            <input type="text" name="nombre_negocio" value="{{ old('nombre_negocio',$agente->nombre_negocio) }}" required>
        </div>

        <div class="field">
            <label>Nombre del Propietario</label>
            <input type="text" name="nombre_propietario" value="{{ old('nombre_propietario',$agente->nombre_propietario) }}" required>
        </div>

        <div class="field">
            <label>Ruta</label>
            <select name="ruta_id" required>
                @foreach($rutas as $ruta)
                    <option
                        value="{{ $ruta->id }}"
                        @selected((int)old('ruta_id',$agente->ruta_id) === (int)$ruta->id)
                    >
                        {{ $ruta->region_nombre }} — {{ $ruta->codigo }} — {{ $ruta->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field" style="grid-column:1/-1;">
            <label>Dirección</label>
            <textarea name="direccion" required>{{ old('direccion',$agente->direccion) }}</textarea>
        </div>

        <div class="field">
            <label>Nombres</label>
            <input type="text" name="nombres" value="{{ old('nombres',$agente->nombres) }}" required>
        </div>

        <div class="field">
            <label>Apellidos</label>
            <input type="text" name="apellidos" value="{{ old('apellidos',$agente->apellidos) }}" required>
        </div>

        <div class="field">
            <label>Usuario</label>
            <input type="text" name="usuario" value="{{ old('usuario',$agente->usuario) }}" required>
        </div>
    </div>

    <div class="actions">
        <a href="{{ route('administrador.agentes.show',$agente->id) }}" class="btn secondary">Cancelar</a>
        <button type="submit" class="btn primary">Guardar Cambios</button>
    </div>
</form>
@endsection
