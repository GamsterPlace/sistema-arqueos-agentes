<?php

namespace App\Http\Controllers\Gerencia;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EstadoArqueosController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $this->validarGerencia($usuario);

        $fecha = $request->input(
            'fecha',
            today()->format('Y-m-d')
        );

        $regionId = $request->integer('region_id');
        $rutaId = $request->integer('ruta_id');
        $promotorId = $request->integer('promotor_id');
        $estado = trim((string) $request->string('estado'));
        $buscar = trim((string) $request->string('buscar'));

        $regiones = DB::table('regiones')
            ->where('estado', true)
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
            ]);

        $rutas = DB::table('rutas')
            ->where('estado', true)
            ->when(
                $regionId > 0,
                fn ($query) => $query->where(
                    'region_id',
                    $regionId
                )
            )
            ->orderBy('nombre')
            ->get([
                'id',
                'codigo',
                'nombre',
                'region_id',
            ]);

        $promotores = DB::table('usuarios as u')
            ->join(
                'roles as rol',
                'rol.id',
                '=',
                'u.rol_id'
            )
            ->leftJoin(
                'datos_personales as dp',
                'dp.usuario_id',
                '=',
                'u.id'
            )
            ->where('rol.nombre', 'Promotor')
            ->where('u.estado', 'ACTIVO')
            ->orderBy('dp.nombres')
            ->orderBy('dp.apellidos')
            ->get([
                'u.id',
                'u.usuario',
                'dp.nombres',
                'dp.apellidos',
            ]);

        $query = DB::table('agentes as a')
            ->join('rutas as r', 'r.id', '=', 'a.ruta_id')
            ->join(
                'regiones as reg',
                'reg.id',
                '=',
                'r.region_id'
            )
            ->leftJoin(
                'asignaciones_promotor_ruta as apr',
                function ($join) use ($fecha): void {
                    $join
                        ->on('apr.ruta_id', '=', 'r.id')
                        ->where('apr.estado', true)
                        ->whereDate(
                            'apr.fecha_inicio',
                            '<=',
                            $fecha
                        )
                        ->where(function ($subquery) use ($fecha): void {
                            $subquery
                                ->whereNull('apr.fecha_fin')
                                ->orWhereDate(
                                    'apr.fecha_fin',
                                    '>=',
                                    $fecha
                                );
                        });
                }
            )
            ->leftJoin(
                'usuarios as up',
                'up.id',
                '=',
                'apr.promotor_usuario_id'
            )
            ->leftJoin(
                'datos_personales as dp',
                'dp.usuario_id',
                '=',
                'up.id'
            )
            ->where('a.estado', 'ACTIVO')
            ->where('r.estado', true)
            ->when(
                $regionId > 0,
                fn ($query) => $query->where(
                    'reg.id',
                    $regionId
                )
            )
            ->when(
                $rutaId > 0,
                fn ($query) => $query->where(
                    'r.id',
                    $rutaId
                )
            )
            ->when(
                $promotorId > 0,
                fn ($query) => $query->where(
                    'apr.promotor_usuario_id',
                    $promotorId
                )
            )
            ->when(
                $buscar !== '',
                function ($query) use ($buscar): void {
                    $query->where(
                        function ($subquery) use ($buscar): void {
                            $subquery
                                ->where(
                                    'a.codigo_agente',
                                    'like',
                                    '%' . $buscar . '%'
                                )
                                ->orWhere(
                                    'a.nombre_negocio',
                                    'like',
                                    '%' . $buscar . '%'
                                )
                                ->orWhere(
                                    'a.nombre_propietario',
                                    'like',
                                    '%' . $buscar . '%'
                                )
                                ->orWhere(
                                    'r.nombre',
                                    'like',
                                    '%' . $buscar . '%'
                                )
                                ->orWhere(
                                    'reg.nombre',
                                    'like',
                                    '%' . $buscar . '%'
                                )
                                ->orWhere(
                                    'up.usuario',
                                    'like',
                                    '%' . $buscar . '%'
                                )
                                ->orWhere(
                                    'dp.nombres',
                                    'like',
                                    '%' . $buscar . '%'
                                )
                                ->orWhere(
                                    'dp.apellidos',
                                    'like',
                                    '%' . $buscar . '%'
                                );
                        }
                    );
                }
            )
            ->select([
                'a.id',
                'a.codigo_agente',
                'a.nombre_negocio',
                'a.nombre_propietario',
                'a.direccion',
                'r.id as ruta_id',
                'r.codigo as ruta_codigo',
                'r.nombre as ruta_nombre',
                'reg.id as region_id',
                'reg.nombre as region_nombre',
                'up.id as promotor_id',
                'up.usuario as promotor_usuario',
                'dp.nombres as promotor_nombres',
                'dp.apellidos as promotor_apellidos',
            ])
            ->selectSub(
                DB::table('arqueos')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn(
                        'arqueos.agente_id',
                        'a.id'
                    )
                    ->where(
                        'arqueos.tipo',
                        'DIARIO_AGENTE'
                    )
                    ->whereDate(
                        'arqueos.fecha_arqueo',
                        $fecha
                    )
                    ->where(
                        'arqueos.estado',
                        '!=',
                        'ANULADO'
                    ),
                'arqueo_agente'
            )
            ->selectSub(
                DB::table('arqueos')
                    ->select('estado')
                    ->whereColumn(
                        'arqueos.agente_id',
                        'a.id'
                    )
                    ->where(
                        'arqueos.tipo',
                        'DIARIO_AGENTE'
                    )
                    ->whereDate(
                        'arqueos.fecha_arqueo',
                        $fecha
                    )
                    ->where(
                        'arqueos.estado',
                        '!=',
                        'ANULADO'
                    )
                    ->orderByDesc('arqueos.id')
                    ->limit(1),
                'estado_arqueo'
            )
            ->selectSub(
                DB::table('arqueos')
                    ->select('diferencia')
                    ->whereColumn(
                        'arqueos.agente_id',
                        'a.id'
                    )
                    ->where(
                        'arqueos.tipo',
                        'DIARIO_AGENTE'
                    )
                    ->whereDate(
                        'arqueos.fecha_arqueo',
                        $fecha
                    )
                    ->where(
                        'arqueos.estado',
                        '!=',
                        'ANULADO'
                    )
                    ->orderByDesc('arqueos.id')
                    ->limit(1),
                'diferencia'
            )
            ->selectSub(
                DB::table('controles_diarios')
                    ->select('tipo')
                    ->whereColumn(
                        'controles_diarios.agente_id',
                        'a.id'
                    )
                    ->whereDate(
                        'controles_diarios.fecha',
                        $fecha
                    )
                    ->where(
                        'controles_diarios.vigente',
                        true
                    )
                    ->orderByDesc(
                        'controles_diarios.id'
                    )
                    ->limit(1),
                'control_diario'
            );

        if ($estado !== '') {
            if ($estado === 'REALIZADO') {
                $query->whereExists(
                    function ($subquery) use ($fecha): void {
                        $subquery
                            ->selectRaw('1')
                            ->from('arqueos as arq_f')
                            ->whereColumn(
                                'arq_f.agente_id',
                                'a.id'
                            )
                            ->where(
                                'arq_f.tipo',
                                'DIARIO_AGENTE'
                            )
                            ->whereDate(
                                'arq_f.fecha_arqueo',
                                $fecha
                            )
                            ->where(
                                'arq_f.estado',
                                '!=',
                                'ANULADO'
                            );
                    }
                );
            }

            if ($estado === 'PENDIENTE') {
                $query
                    ->whereNotExists(
                        function ($subquery) use ($fecha): void {
                            $subquery
                                ->selectRaw('1')
                                ->from('arqueos as arq_f')
                                ->whereColumn(
                                    'arq_f.agente_id',
                                    'a.id'
                                )
                                ->where(
                                    'arq_f.tipo',
                                    'DIARIO_AGENTE'
                                )
                                ->whereDate(
                                    'arq_f.fecha_arqueo',
                                    $fecha
                                )
                                ->where(
                                    'arq_f.estado',
                                    '!=',
                                    'ANULADO'
                                );
                        }
                    )
                    ->whereNotExists(
                        function ($subquery) use ($fecha): void {
                            $subquery
                                ->selectRaw('1')
                                ->from('controles_diarios as cd_f')
                                ->whereColumn(
                                    'cd_f.agente_id',
                                    'a.id'
                                )
                                ->whereDate(
                                    'cd_f.fecha',
                                    $fecha
                                )
                                ->where(
                                    'cd_f.tipo',
                                    'NO_ATENDIO'
                                )
                                ->where(
                                    'cd_f.vigente',
                                    true
                                );
                        }
                    );
            }

            if ($estado === 'NO_ATENDIO') {
                $query->whereExists(
                    function ($subquery) use ($fecha): void {
                        $subquery
                            ->selectRaw('1')
                            ->from('controles_diarios as cd_f')
                            ->whereColumn(
                                'cd_f.agente_id',
                                'a.id'
                            )
                            ->whereDate(
                                'cd_f.fecha',
                                $fecha
                            )
                            ->where(
                                'cd_f.tipo',
                                'NO_ATENDIO'
                            )
                            ->where(
                                'cd_f.vigente',
                                true
                            );
                    }
                );
            }
        }

        $agentes = $query
            ->distinct()
            ->orderBy('reg.nombre')
            ->orderBy('r.nombre')
            ->orderBy('a.nombre_negocio')
            ->paginate(15)
            ->withQueryString();

        $totalActivos = DB::table('agentes as a')
            ->join('rutas as r', 'r.id', '=', 'a.ruta_id')
            ->where('a.estado', 'ACTIVO')
            ->where('r.estado', true)
            ->count();

        $realizados = DB::table('agentes as a')
            ->join('rutas as r', 'r.id', '=', 'a.ruta_id')
            ->where('a.estado', 'ACTIVO')
            ->where('r.estado', true)
            ->whereExists(
                function ($query) use ($fecha): void {
                    $query
                        ->selectRaw('1')
                        ->from('arqueos as arq')
                        ->whereColumn(
                            'arq.agente_id',
                            'a.id'
                        )
                        ->where(
                            'arq.tipo',
                            'DIARIO_AGENTE'
                        )
                        ->whereDate(
                            'arq.fecha_arqueo',
                            $fecha
                        )
                        ->where(
                            'arq.estado',
                            '!=',
                            'ANULADO'
                        );
                }
            )
            ->count();

        $noAtendio = DB::table('agentes as a')
            ->join('rutas as r', 'r.id', '=', 'a.ruta_id')
            ->where('a.estado', 'ACTIVO')
            ->where('r.estado', true)
            ->whereExists(
                function ($query) use ($fecha): void {
                    $query
                        ->selectRaw('1')
                        ->from('controles_diarios as cd')
                        ->whereColumn(
                            'cd.agente_id',
                            'a.id'
                        )
                        ->whereDate(
                            'cd.fecha',
                            $fecha
                        )
                        ->where(
                            'cd.tipo',
                            'NO_ATENDIO'
                        )
                        ->where(
                            'cd.vigente',
                            true
                        );
                }
            )
            ->count();

        $pendientes = max(
            0,
            $totalActivos - $realizados - $noAtendio
        );

        $cumplimiento = $totalActivos > 0
            ? round(
                (($realizados + $noAtendio)
                    / $totalActivos)
                * 100,
                1
            )
            : 0;

        return view(
            'gerencia.estado-arqueos.index',
            compact(
                'agentes',
                'regiones',
                'rutas',
                'promotores',
                'fecha',
                'regionId',
                'rutaId',
                'promotorId',
                'estado',
                'buscar',
                'totalActivos',
                'realizados',
                'noAtendio',
                'pendientes',
                'cumplimiento'
            )
        );
    }

    private function validarGerencia(
        Usuario $usuario
    ): void {
        $usuario->loadMissing('rol');

        abort_if(
            ! $usuario->rol
            || $usuario->rol->nombre !== 'Gerencia',
            403,
            'No tiene autorización para acceder a esta sección.'
        );
    }
}
