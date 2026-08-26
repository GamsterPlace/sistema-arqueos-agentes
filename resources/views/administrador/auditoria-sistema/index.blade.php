@extends('layouts.administrador')

@section('title', 'Auditoría del Sistema')
@section('module-title', 'Auditoría del Sistema')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Auditoría del Sistema</h2>
        <p>
            Consulte la trazabilidad de acciones realizadas dentro del sistema.
        </p>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    @foreach([
        ['Total Registros',$resumen->total],
        ['Registros Hoy',$resumen->hoy],
        ['Usuarios con Actividad',$resumen->usuarios],
        ['Módulos Auditados',$resumen->modulos],
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
    action="{{ route('administrador.auditoria-sistema.index') }}"
    style="padding:18px;border:1px solid #e0e8ee;border-radius:18px;background:#fff;margin-bottom:20px;"
>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
        <input
            name="buscar"
            value="{{ $buscar }}"
            placeholder="Descripción, usuario, módulo, acción o tabla"
            style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;"
        >

        <select name="usuario_id" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todos los Usuarios</option>
            @foreach($usuarios as $usuario)
                @php
                    $nombre = trim(
                        ($usuario->nombres ?? '')
                        . ' '
                        . ($usuario->apellidos ?? '')
                    );
                @endphp
                <option value="{{ $usuario->id }}" @selected($usuarioId === (int)$usuario->id)>
                    {{ $nombre !== '' ? $nombre : $usuario->usuario }}
                </option>
            @endforeach
        </select>

        <select name="modulo" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todos los Módulos</option>
            @foreach($modulos as $opcion)
                <option value="{{ $opcion }}" @selected($modulo === $opcion)>{{ $opcion }}</option>
            @endforeach
        </select>

        <select name="accion" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todas las Acciones</option>
            @foreach($acciones as $opcion)
                <option value="{{ $opcion }}" @selected($accion === $opcion)>{{ $opcion }}</option>
            @endforeach
        </select>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-top:10px;">
        <select name="tabla" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
            <option value="">Todas las Tablas</option>
            @foreach($tablas as $opcion)
                <option value="{{ $opcion }}" @selected($tabla === $opcion)>{{ $opcion }}</option>
            @endforeach
        </select>

        <input type="date" name="desde" value="{{ $desde }}" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
        <input type="date" name="hasta" value="{{ $hasta }}" style="min-height:42px;padding:0 12px;border:1px solid #ced9e1;border-radius:10px;">
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
        <a href="{{ route('administrador.auditoria-sistema.index') }}" style="padding:10px 14px;border-radius:10px;background:#edf2f5;color:#3d596f;text-decoration:none;font-size:10px;font-weight:800;">Limpiar</a>
        <button type="submit" style="padding:10px 14px;border:0;border-radius:10px;background:#164c96;color:#fff;font-size:10px;font-weight:800;">Aplicar Filtros</button>
    </div>
</form>

<div style="overflow:hidden;border:1px solid #e0e8ee;border-radius:18px;background:#fff;">
    <div style="overflow-x:auto;">
        <table style="width:100%;min-width:1200px;border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f9fb;">
                    <th style="padding:12px;">Fecha</th>
                    <th style="padding:12px;">Usuario</th>
                    <th style="padding:12px;">Rol</th>
                    <th style="padding:12px;">Módulo</th>
                    <th style="padding:12px;">Acción</th>
                    <th style="padding:12px;">Tabla</th>
                    <th style="padding:12px;">Registro</th>
                    <th style="padding:12px;">Descripción</th>
                    <th style="padding:12px;">Acción</th>
                </tr>
            </thead>

            <tbody>
                @forelse($registros as $registro)
                    @php
                        $nombre = trim(
                            ($registro->nombres ?? '')
                            . ' '
                            . ($registro->apellidos ?? '')
                        );
                    @endphp

                    <tr style="border-top:1px solid #edf1f4;">
                        <td style="padding:12px;">{{ \Carbon\Carbon::parse($registro->created_at)->format('d/m/Y H:i:s') }}</td>
                        <td style="padding:12px;">{{ $nombre !== '' ? $nombre : ($registro->usuario ?? 'Sistema') }}</td>
                        <td style="padding:12px;">{{ $registro->rol_nombre ?? '—' }}</td>
                        <td style="padding:12px;">{{ $registro->modulo }}</td>
                        <td style="padding:12px;">{{ $registro->accion }}</td>
                        <td style="padding:12px;">{{ $registro->tabla_afectada ?? '—' }}</td>
                        <td style="padding:12px;">{{ $registro->registro_id ?? '—' }}</td>
                        <td style="padding:12px;">{{ \Illuminate\Support\Str::limit($registro->descripcion,90) }}</td>
                        <td style="padding:12px;">
                            <a href="{{ route('administrador.auditoria-sistema.show',$registro->id) }}" style="padding:8px 10px;border-radius:8px;background:#edf2f5;color:#31536e;text-decoration:none;font-size:9px;font-weight:800;">Ver detalle</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="padding:35px;text-align:center;">
                            No existen registros de auditoría.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($registros->hasPages())
        <div style="padding:16px 18px;">
            {{ $registros->links() }}
        </div>
    @endif
</div>
@endsection
