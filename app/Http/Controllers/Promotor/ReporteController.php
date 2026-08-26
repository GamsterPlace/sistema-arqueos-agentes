<?php

namespace App\Http\Controllers\Promotor;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $usuario->loadMissing('rol');

        abort_if(
            ! $usuario->rol || $usuario->rol->nombre !== 'Promotor',
            403,
            'No tiene autorización para acceder a esta sección.'
        );

        $fechaInicio = $request->filled('fecha_inicio')
            ? Carbon::parse($request->input('fecha_inicio'))->startOfDay()
            : today()->startOfMonth();

        $fechaFin = $request->filled('fecha_fin')
            ? Carbon::parse($request->input('fecha_fin'))->endOfDay()
            : today()->endOfDay();

        abort_if(
            $fechaInicio->gt($fechaFin),
            422,
            'La fecha inicial no puede ser mayor que la fecha final.'
        );

        $rutaId = $request->integer('ruta_id');
        $agenteId = $request->integer('agente_id');
        $tipo = trim((string) $request->string('tipo'));
        $estado = trim((string) $request->string('estado'));

        $rutasAsignadas = DB::table('rutas as r')
            ->join(
                'asignaciones_promotor_ruta as apr',
                'apr.ruta_id',
                '=',
                'r.id'
            )
            ->where('apr.promotor_usuario_id', $usuario->id)
            ->where('apr.estado', true)
            ->whereDate('apr.fecha_inicio', '<=', today())
            ->where(function ($query): void {
                $query
                    ->whereNull('apr.fecha_fin')
                    ->orWhereDate('apr.fecha_fin', '>=', today());
            })
            ->select([
                'r.id',
                'r.codigo',
                'r.nombre',
            ])
            ->distinct()
            ->orderBy('r.nombre')
            ->get();

        $agentesAsignados = DB::table('agentes as a')
            ->join('rutas as r', 'r.id', '=', 'a.ruta_id')
            ->join(
                'asignaciones_promotor_ruta as apr',
                'apr.ruta_id',
                '=',
                'r.id'
            )
            ->where('apr.promotor_usuario_id', $usuario->id)
            ->where('apr.estado', true)
            ->whereDate('apr.fecha_inicio', '<=', today())
            ->where(function ($query): void {
                $query
                    ->whereNull('apr.fecha_fin')
                    ->orWhereDate('apr.fecha_fin', '>=', today());
            })
            ->when(
                $rutaId > 0,
                fn ($query) => $query->where('r.id', $rutaId)
            )
            ->select([
                'a.id',
                'a.codigo_agente',
                'a.nombre_negocio',
            ])
            ->distinct()
            ->orderBy('a.nombre_negocio')
            ->get();

        $baseQuery = DB::table('arqueos as arq')
            ->join('agentes as a', 'a.id', '=', 'arq.agente_id')
            ->join('rutas as r', 'r.id', '=', 'a.ruta_id')
            ->join('regiones as reg', 'reg.id', '=', 'r.region_id')
            ->join(
                'asignaciones_promotor_ruta as apr',
                'apr.ruta_id',
                '=',
                'r.id'
            )
            ->where('apr.promotor_usuario_id', $usuario->id)
            ->where('apr.estado', true)
            ->whereBetween('arq.fecha_arqueo', [
                $fechaInicio->toDateString(),
                $fechaFin->toDateString(),
            ])
            ->when(
                $rutaId > 0,
                fn ($query) => $query->where('r.id', $rutaId)
            )
            ->when(
                $agenteId > 0,
                fn ($query) => $query->where('a.id', $agenteId)
            )
            ->when(
                $tipo !== '',
                fn ($query) => $query->where('arq.tipo', $tipo)
            )
            ->when(
                $estado !== '',
                fn ($query) => $query->where('arq.estado', $estado)
            );

        $resumen = (clone $baseQuery)
            ->selectRaw('COUNT(DISTINCT arq.id) as total_arqueos')
            ->selectRaw(
                "SUM(CASE WHEN arq.tipo = 'DIARIO_AGENTE' THEN 1 ELSE 0 END) as arqueos_agentes"
            )
            ->selectRaw(
                "SUM(CASE WHEN arq.tipo = 'VISITA_PROMOTOR' THEN 1 ELSE 0 END) as arqueos_promotor"
            )
            ->selectRaw(
                "SUM(CASE WHEN arq.estado = 'CERTIFICADO' THEN 1 ELSE 0 END) as certificados"
            )
            ->selectRaw(
                "SUM(CASE WHEN arq.estado = 'PENDIENTE_CERTIFICACION' THEN 1 ELSE 0 END) as pendientes"
            )
            ->selectRaw(
                "SUM(CASE WHEN arq.estado = 'ANULADO' THEN 1 ELSE 0 END) as anulados"
            )
            ->first();

        $arqueos = (clone $baseQuery)
            ->select([
                'arq.id',
                'arq.numero_arqueo',
                'arq.fecha_arqueo',
                'arq.tipo',
                'arq.estado',
                'arq.total_arqueado',
                'arq.saldo_sistema',
                'arq.diferencia',
                'a.codigo_agente',
                'a.nombre_negocio',
                'r.codigo as ruta_codigo',
                'r.nombre as ruta_nombre',
                'reg.nombre as region_nombre',
            ])
            ->distinct()
            ->orderByDesc('arq.fecha_arqueo')
            ->orderByDesc('arq.id')
            ->paginate(15)
            ->withQueryString();

        return view('promotor.reportes.index', [
            'resumen' => $resumen,
            'arqueos' => $arqueos,
            'rutasAsignadas' => $rutasAsignadas,
            'agentesAsignados' => $agentesAsignados,
            'fechaInicio' => $fechaInicio->toDateString(),
            'fechaFin' => $fechaFin->toDateString(),
            'rutaId' => $rutaId,
            'agenteId' => $agenteId,
            'tipo' => $tipo,
            'estado' => $estado,
        ]);
    }
}
