@extends('layouts.administrador')

@section('title', 'Agentes')
@section('module-title', 'Agentes')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Gestión de Agentes</h2>
        <p>
            Administre los Agentes, sus cuentas de acceso y su asignación de Ruta.
        </p>
    </div>

    <a
        href="{{ route('administrador.agentes.create') }}"
        style="display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;text-decoration:none;"
    >
        Crear Agente
    </a>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    @foreach([
        ['Total',$resumen->total],
        ['Activos',$resumen->activos],
        ['Inactivos',$resumen->inactivos],
        ['Cuenta Inactiva',$resumen->sin_usuario_activo],
    ] as [$label,$value])
        <div style="padding:17px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
            <span style="display:block;color:#758697;font-size:8px;font-weight:800;text-transform:uppercase;">
                {{ $label }}
            </span>

            <strong style="display:block;margin-top:8px;color:#082d55;font-size:23px;">
                {{ $value }}
            </strong>
        </div>
    @endforeach
</div>

<form
    method="GET"
    action="{{ route('administrador.agentes.index') }}"
    style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;margin-bottom:20px;"
>
    <div style="display:grid;grid-template-columns:minmax(240px,1fr) 200px 220px 220px;gap:10px;">
        <input
            type="text"
            name="buscar"
            value="{{ $buscar }}"
            placeholder="Código, negocio, propietario o usuario"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >

        <select name="estado" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todos los Estados</option>
            <option value="ACTIVO" @selected($estado === 'ACTIVO')>ACTIVO</option>
            <option value="INACTIVO" @selected($estado === 'INACTIVO')>INACTIVO</option>
        </select>

        <select name="region_id" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todas las Regiones</option>
            @foreach($regiones as $region)
                <option value="{{ $region->id }}" @selected($regionId === (int)$region->id)>
                    {{ $region->nombre }}
                </option>
            @endforeach
        </select>

        <select name="ruta_id" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todas las Rutas</option>
            @foreach($rutas as $ruta)
                <option value="{{ $ruta->id }}" @selected($rutaId === (int)$ruta->id)>
                    {{ $ruta->codigo }} — {{ $ruta->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
        <a href="{{ route('administrador.agentes.index') }}" style="padding:10px 14px;border-radius:10px;background:#edf2f5;color:#3d596f;text-decoration:none;font-size:10px;font-weight:800;">Limpiar</a>
        <button type="submit" style="padding:10px 14px;border:0;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;">Aplicar Filtros</button>
    </div>
</form>

<div style="overflow:hidden;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
    <div style="overflow-x:auto;">
        <table style="width:100%;min-width:1200px;border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f9fb;">
                    <th style="padding:12px;">Código</th>
                    <th style="padding:12px;">Negocio</th>
                    <th style="padding:12px;">Propietario</th>
                    <th style="padding:12px;">Usuario</th>
                    <th style="padding:12px;">Región</th>
                    <th style="padding:12px;">Ruta</th>
                    <th style="padding:12px;">Estado</th>
                    <th style="padding:12px;">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($agentes as $agente)
                    <tr style="border-top:1px solid #edf1f4;">
                        <td style="padding:12px;"><strong>{{ $agente->codigo_agente }}</strong></td>
                        <td style="padding:12px;">{{ $agente->nombre_negocio }}</td>
                        <td style="padding:12px;">{{ $agente->nombre_propietario }}</td>
                        <td style="padding:12px;">{{ $agente->usuario }}</td>
                        <td style="padding:12px;">{{ $agente->region_nombre }}</td>
                        <td style="padding:12px;">{{ $agente->ruta_codigo }} — {{ $agente->ruta_nombre }}</td>
                        <td style="padding:12px;">{{ $agente->estado }}</td>
                        <td style="padding:12px;white-space:nowrap;">
                            <a href="{{ route('administrador.agentes.show',$agente->id) }}" style="padding:8px 10px;border-radius:8px;background:#edf2f5;color:#31536e;text-decoration:none;font-size:9px;font-weight:800;">Ver</a>

                            <a href="{{ route('administrador.agentes.edit',$agente->id) }}" style="padding:8px 10px;border-radius:8px;background:#164c96;color:#fff;text-decoration:none;font-size:9px;font-weight:800;">Editar</a>

                            <form method="POST" action="{{ route('administrador.agentes.estado',$agente->id) }}" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="padding:8px 10px;border:0;border-radius:8px;background:#f3f5f7;color:#4d6374;font-size:9px;font-weight:800;cursor:pointer;">
                                    {{ $agente->estado === 'ACTIVO' ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding:35px;text-align:center;">
                            No se encontraron Agentes.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($agentes->hasPages())
        <div style="padding:16px 18px;">
            {{ $agentes->links() }}
        </div>
    @endif
</div>
@endsection
