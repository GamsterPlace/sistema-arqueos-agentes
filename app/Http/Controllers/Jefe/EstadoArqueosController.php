<?php

namespace App\Http\Controllers\Jefe;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EstadoArqueosController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $usuario->loadMissing('rol');

        abort_if(
            ! $usuario->rol
            || $usuario->rol->nombre !== 'jefedeAgentes',
            403,
            'No tiene autorización para acceder a esta sección.'
        );

        $fecha = $request->filled('fecha')
            ? Carbon::parse($request->input('fecha'))->toDateString()
            : today()->toDateString();

        $regionId = $request->integer('region_id');
        $rutaId = $request->integer('ruta_id');
        $promotorId = $request->integer('promotor_id');
        $estado = trim((string) $request->string('estado'));
        $buscar = trim((string) $request->string('buscar'));

        $regiones = DB::table('regiones')
            ->where('estado', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $rutas = DB::table('rutas')
            ->when(
                $regionId > 0,
                fn ($query) => $query->where('region_id', $regionId)
            )
            ->where('estado', true)
            ->orderBy('nombre')
            ->get([
                'id',
                'codigo',
                'nombre',
                'region_id',
            ]);

        $promotores = DB::table('usuarios as u')
            ->join('roles as rol', 'rol.id', '=', 'u.rol_id')
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
            ->join('regiones as reg', 'reg.id', '=', 'r.region_id')
            ->leftJoin(
                'asignaciones_promotor_ruta as apr',
                function ($join) use ($fecha): void {
                    $join
                        ->on('apr.ruta_id', '=', 'r.id')
                        ->where('apr.estado', true)
                        ->whereDate('apr.fecha_inicio', '<=', $fecha)
                        ->where(function ($query) use ($fecha): void {
                            $query
                                ->whereNull('apr.fecha_fin')
                                ->orWhereDate('apr.fecha_fin', '>=', $fecha);
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
                'datos_personales as dpp',
                'dpp.usuario_id',
                '=',
                'up.id'
            )
            ->where('a.estado', 'ACTIVO')
            ->where('r.estado', true)
            ->when(
                $regionId > 0,
                fn ($query) => $query->where('reg.id', $regionId)
            )
            ->when(
                $rutaId > 0,
                fn ($query) => $query->where('r.id', $rutaId)
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
                    $query->where(function ($subquery) use ($buscar): void {
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
                            );
                    });
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
                'apr.promotor_usuario_id',
                'up.usuario as promotor_usuario',
                'dpp.nombres as promotor_nombres',
                'dpp.apellidos as promotor_apellidos',
            ])
            ->selectSub(
                DB::table('arqueos')
                    ->select('id')
                    ->whereColumn('arqueos.agente_id', 'a.id')
                    ->whereDate('arqueos.fecha_arqueo', $fecha)
                    ->where('arqueos.tipo', 'DIARIO_AGENTE')
                    ->where('arqueos.estado', '!=', 'ANULADO')
                    ->orderByDesc('arqueos.id')
                    ->limit(1),
                'arqueo_id'
            )
            ->selectSub(
                DB::table('arqueos')
                    ->select('numero_arqueo')
                    ->whereColumn('arqueos.agente_id', 'a.id')
                    ->whereDate('arqueos.fecha_arqueo', $fecha)
                    ->where('arqueos.tipo', 'DIARIO_AGENTE')
                    ->where('arqueos.estado', '!=', 'ANULADO')
                    ->orderByDesc('arqueos.id')
                    ->limit(1),
                'numero_arqueo'
            )
            ->selectSub(
                DB::table('arqueos')
                    ->select('estado')
                    ->whereColumn('arqueos.agente_id', 'a.id')
                    ->whereDate('arqueos.fecha_arqueo', $fecha)
                    ->where('arqueos.tipo', 'DIARIO_AGENTE')
                    ->orderByDesc('arqueos.id')
                    ->limit(1),
                'estado_arqueo'
            )
            ->selectSub(
                DB::table('arqueos')
                    ->select('fuera_fecha_ordinaria')
                    ->whereColumn('arqueos.agente_id', 'a.id')
                    ->whereDate('arqueos.fecha_arqueo', $fecha)
                    ->where('arqueos.tipo', 'DIARIO_AGENTE')
                    ->where('arqueos.estado', '!=', 'ANULADO')
                    ->orderByDesc('arqueos.id')
                    ->limit(1),
                'fuera_fecha_ordinaria'
            )
            ->selectSub(
                DB::table('controles_diarios')
                    ->select('tipo')
                    ->whereColumn('controles_diarios.agente_id', 'a.id')
                    ->whereDate('controles_diarios.fecha', $fecha)
                    ->where('controles_diarios.vigente', true)
                    ->orderByDesc('controles_diarios.id')
                    ->limit(1),
                'estado_control'
            )
            ->distinct();

        if ($estado === 'ARQUEADO') {
            $query->whereExists(function ($subquery) use ($fecha): void {
                $subquery
                    ->selectRaw('1')
                    ->from('arqueos as arq_f')
                    ->whereColumn('arq_f.agente_id', 'a.id')
                    ->whereDate('arq_f.fecha_arqueo', $fecha)
                    ->where('arq_f.tipo', 'DIARIO_AGENTE')
                    ->where('arq_f.estado', '!=', 'ANULADO')
                    ->where(function ($inner): void {
                        $inner
                            ->whereNull('arq_f.fuera_fecha_ordinaria')
                            ->orWhere('arq_f.fuera_fecha_ordinaria', false);
                    });
            });
        } elseif ($estado === 'EXTEMPORANEO') {
            $query->whereExists(function ($subquery) use ($fecha): void {
                $subquery
                    ->selectRaw('1')
                    ->from('arqueos as arq_f')
                    ->whereColumn('arq_f.agente_id', 'a.id')
                    ->whereDate('arq_f.fecha_arqueo', $fecha)
                    ->where('arq_f.tipo', 'DIARIO_AGENTE')
                    ->where('arq_f.estado', '!=', 'ANULADO')
                    ->where('arq_f.fuera_fecha_ordinaria', true);
            });
        } elseif ($estado === 'ANULADO') {
            $query->whereExists(function ($subquery) use ($fecha): void {
                $subquery
                    ->selectRaw('1')
                    ->from('arqueos as arq_f')
                    ->whereColumn('arq_f.agente_id', 'a.id')
                    ->whereDate('arq_f.fecha_arqueo', $fecha)
                    ->where('arq_f.tipo', 'DIARIO_AGENTE')
                    ->where('arq_f.estado', 'ANULADO');
            });
        } elseif ($estado === 'NO_ATENDIO') {
            $query->whereExists(function ($subquery) use ($fecha): void {
                $subquery
                    ->selectRaw('1')
                    ->from('controles_diarios as cd_f')
                    ->whereColumn('cd_f.agente_id', 'a.id')
                    ->whereDate('cd_f.fecha', $fecha)
                    ->where('cd_f.tipo', 'NO_ATENDIO')
                    ->where('cd_f.vigente', true);
            });
        } elseif ($estado === 'PENDIENTE') {
            $query
                ->whereNotExists(function ($subquery) use ($fecha): void {
                    $subquery
                        ->selectRaw('1')
                        ->from('arqueos as arq_f')
                        ->whereColumn('arq_f.agente_id', 'a.id')
                        ->whereDate('arq_f.fecha_arqueo', $fecha)
                        ->where('arq_f.tipo', 'DIARIO_AGENTE')
                        ->where('arq_f.estado', '!=', 'ANULADO');
                })
                ->whereNotExists(function ($subquery) use ($fecha): void {
                    $subquery
                        ->selectRaw('1')
                        ->from('controles_diarios as cd_f')
                        ->whereColumn('cd_f.agente_id', 'a.id')
                        ->whereDate('cd_f.fecha', $fecha)
                        ->where('cd_f.tipo', 'NO_ATENDIO')
                    ->where('cd_f.vigente', true);
                });
        }

        $agentes = $query
            ->orderBy('reg.nombre')
            ->orderBy('r.nombre')
            ->orderBy('a.nombre_negocio')
            ->paginate(15)
            ->withQueryString();

        $baseAgentes = DB::table('agentes as a')
            ->join('rutas as r', 'r.id', '=', 'a.ruta_id')
            ->join('regiones as reg', 'reg.id', '=', 'r.region_id')
            ->where('a.estado', 'ACTIVO')
            ->where('r.estado', true)
            ->when(
                $regionId > 0,
                fn ($query) => $query->where('reg.id', $regionId)
            )
            ->when(
                $rutaId > 0,
                fn ($query) => $query->where('r.id', $rutaId)
            );

        $totalAgentes = (clone $baseAgentes)->count();

        $arqueadosHoy = (clone $baseAgentes)
            ->whereExists(function ($query) use ($fecha): void {
                $query
                    ->selectRaw('1')
                    ->from('arqueos as arq')
                    ->whereColumn('arq.agente_id', 'a.id')
                    ->whereDate('arq.fecha_arqueo', $fecha)
                    ->where('arq.tipo', 'DIARIO_AGENTE')
                    ->where('arq.estado', '!=', 'ANULADO');
            })
            ->count();

        $extemporaneos = (clone $baseAgentes)
            ->whereExists(function ($query) use ($fecha): void {
                $query
                    ->selectRaw('1')
                    ->from('arqueos as arq')
                    ->whereColumn('arq.agente_id', 'a.id')
                    ->whereDate('arq.fecha_arqueo', $fecha)
                    ->where('arq.tipo', 'DIARIO_AGENTE')
                    ->where('arq.estado', '!=', 'ANULADO')
                    ->where('arq.fuera_fecha_ordinaria', true);
            })
            ->count();

        $noAtendieron = (clone $baseAgentes)
            ->whereExists(function ($query) use ($fecha): void {
                $query
                    ->selectRaw('1')
                    ->from('controles_diarios as cd')
                    ->whereColumn('cd.agente_id', 'a.id')
                    ->whereDate('cd.fecha', $fecha)
                    ->where('cd.tipo', 'NO_ATENDIO')
                    ->where('cd.vigente', true);
            })
            ->count();

        $anulados = (clone $baseAgentes)
            ->whereExists(function ($query) use ($fecha): void {
                $query
                    ->selectRaw('1')
                    ->from('arqueos as arq')
                    ->whereColumn('arq.agente_id', 'a.id')
                    ->whereDate('arq.fecha_arqueo', $fecha)
                    ->where('arq.tipo', 'DIARIO_AGENTE')
                    ->where('arq.estado', 'ANULADO');
            })
            ->count();

        $pendientes = max(
            0,
            $totalAgentes - $arqueadosHoy - $noAtendieron
        );

        return view('jefe.estado-arqueos.index', [
            'agentes' => $agentes,
            'regiones' => $regiones,
            'rutas' => $rutas,
            'promotores' => $promotores,
            'fecha' => $fecha,
            'regionId' => $regionId,
            'rutaId' => $rutaId,
            'promotorId' => $promotorId,
            'estado' => $estado,
            'buscar' => $buscar,
            'totalAgentes' => $totalAgentes,
            'arqueadosHoy' => $arqueadosHoy,
            'pendientes' => $pendientes,
            'noAtendieron' => $noAtendieron,
            'extemporaneos' => $extemporaneos,
            'anulados' => $anulados,
        ]);
    }
}
