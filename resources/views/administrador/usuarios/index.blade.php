@extends('layouts.administrador')

@section('title', 'Usuarios')
@section('module-title', 'Usuarios')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Gestión de Usuarios</h2>
        <p>
            Administre las cuentas que tienen acceso al Sistema de Arqueos.
        </p>
    </div>

    <a
        href="{{ route('administrador.usuarios.create') }}"
        style="display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;text-decoration:none;"
    >
        Crear Usuario
    </a>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    @foreach([
        ['Total', $resumen->total],
        ['Activos', $resumen->activos],
        ['Inactivos', $resumen->inactivos],
        ['Administradores', $resumen->administradores],
    ] as [$label,$value])
        <div style="padding:17px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;box-shadow:0 8px 24px rgba(20,57,83,.05);">
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
    action="{{ route('administrador.usuarios.index') }}"
    style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;margin-bottom:20px;"
>
    <div style="display:grid;grid-template-columns:minmax(260px,1fr) 230px 200px;gap:10px;">
        <input
            type="text"
            name="buscar"
            value="{{ $buscar }}"
            placeholder="Usuario, nombre, apellido o rol"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >

        <select
            name="rol_id"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >
            <option value="">Todos los Roles</option>

            @foreach($roles as $rol)
                <option
                    value="{{ $rol->id }}"
                    @selected($rolId === (int) $rol->id)
                >
                    {{ $rol->nombre }}
                </option>
            @endforeach
        </select>

        <select
            name="estado"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >
            <option value="">Todos los Estados</option>
            <option value="ACTIVO" @selected($estado === 'ACTIVO')>Activos</option>
            <option value="INACTIVO" @selected($estado === 'INACTIVO')>Inactivos</option>
        </select>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
        <a
            href="{{ route('administrador.usuarios.index') }}"
            style="padding:10px 14px;border-radius:10px;background:#edf2f5;color:#3d596f;font-size:10px;font-weight:800;text-decoration:none;"
        >
            Limpiar
        </a>

        <button
            type="submit"
            style="padding:10px 14px;border:0;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;"
        >
            Aplicar Filtros
        </button>
    </div>
</form>

<div style="overflow:hidden;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
    <div style="padding:18px 20px;border-bottom:1px solid #edf1f4;background:#fafcfd;">
        <h3 style="margin:0;color:#0a3158;font-size:15px;">
            Usuarios Registrados
        </h3>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;min-width:1000px;border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f9fb;">
                    <th style="padding:12px;">Nombre</th>
                    <th style="padding:12px;">Usuario</th>
                    <th style="padding:12px;">Rol</th>
                    <th style="padding:12px;">Estado</th>
                    <th style="padding:12px;">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($usuarios as $usuario)
                    @php
                        $nombre = trim(
                            ($usuario->nombres ?? '')
                            . ' '
                            . ($usuario->apellidos ?? '')
                        );
                    @endphp

                    <tr style="border-top:1px solid #edf1f4;">
                        <td style="padding:12px;">
                            <strong>
                                {{ $nombre !== ''
                                    ? $nombre
                                    : 'Sin nombre registrado' }}
                            </strong>
                        </td>

                        <td style="padding:12px;">
                            {{ $usuario->usuario }}
                        </td>

                        <td style="padding:12px;">
                            {{ $usuario->rol_nombre }}
                        </td>

                        <td style="padding:12px;">
                            {{ $usuario->estado }}
                        </td>

                        <td style="padding:12px;white-space:nowrap;">
                            <a
                                href="{{ route(
                                    'administrador.usuarios.show',
                                    $usuario->id
                                ) }}"
                                style="display:inline-flex;padding:8px 10px;border-radius:8px;background:#edf2f5;color:#31536e;text-decoration:none;font-size:9px;font-weight:800;"
                            >
                                Ver
                            </a>

                            <a
                                href="{{ route(
                                    'administrador.usuarios.edit',
                                    $usuario->id
                                ) }}"
                                style="display:inline-flex;padding:8px 10px;border-radius:8px;background:#164c96;color:#fff;text-decoration:none;font-size:9px;font-weight:800;"
                            >
                                Editar
                            </a>

                            @if(auth()->id() !== $usuario->id)
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'administrador.usuarios.estado',
                                        $usuario->id
                                    ) }}"
                                    style="display:inline;"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        style="padding:8px 10px;border:0;border-radius:8px;background:#f3f5f7;color:#4d6374;font-size:9px;font-weight:800;cursor:pointer;"
                                    >
                                        {{ $usuario->estado === 'ACTIVO'
                                            ? 'Desactivar'
                                            : 'Activar' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="5"
                            style="padding:35px;text-align:center;"
                        >
                            No se encontraron usuarios.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($usuarios->hasPages())
        <div style="padding:16px 18px;">
            {{ $usuarios->links() }}
        </div>
    @endif
</div>
@endsection
