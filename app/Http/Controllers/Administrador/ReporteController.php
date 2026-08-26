<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->validarAdministrador($usuario);

        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $regionId = $request->integer('region_id');
        $rutaId = $request->integer('ruta_id');
        $tipo = trim((string)$request->string('tipo'));

        $regiones = DB::table('regiones')->orderBy('nombre')->get(['id','nombre']);

        $rutas = DB::table('rutas as r')
            ->join('regiones as reg','reg.id','=','r.region_id')
            ->when($regionId > 0, fn($q) => $q->where('r.region_id',$regionId))
            ->orderBy('reg.nombre')->orderBy('r.nombre')
            ->get(['r.id','r.codigo','r.nombre','r.region_id']);

        $base = DB::table('arqueos as arq')
            ->join('agentes as a','a.id','=','arq.agente_id')
            ->leftJoin('rutas as r','r.id','=','a.ruta_id')
            ->leftJoin('regiones as reg','reg.id','=','r.region_id')
            ->where('arq.estado','!=','ANULADO')
            ->when($desde, fn($q) => $q->whereDate('arq.fecha_arqueo','>=',$desde))
            ->when($hasta, fn($q) => $q->whereDate('arq.fecha_arqueo','<=',$hasta))
            ->when($regionId > 0, fn($q) => $q->where('reg.id',$regionId))
            ->when($rutaId > 0, fn($q) => $q->where('r.id',$rutaId))
            ->when($tipo !== '', fn($q) => $q->where('arq.tipo',$tipo));

        $metricas = (clone $base)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN arq.diferencia < 0 THEN 1 ELSE 0 END) as faltantes')
            ->selectRaw('SUM(CASE WHEN arq.diferencia > 0 THEN 1 ELSE 0 END) as sobrantes')
            ->selectRaw('SUM(CASE WHEN arq.diferencia = 0 THEN 1 ELSE 0 END) as exactos')
            ->selectRaw('SUM(CASE WHEN arq.diferencia < 0 THEN ABS(arq.diferencia) ELSE 0 END) as monto_faltantes')
            ->selectRaw('SUM(CASE WHEN arq.diferencia > 0 THEN arq.diferencia ELSE 0 END) as monto_sobrantes')
            ->first();

        $rankingFaltantes = (clone $base)
            ->where('arq.diferencia','<',0)
            ->groupBy('a.id','a.codigo_agente','a.nombre_negocio')
            ->select(['a.id','a.codigo_agente','a.nombre_negocio'])
            ->selectRaw('COUNT(*) as incidencias')
            ->selectRaw('SUM(ABS(arq.diferencia)) as monto')
            ->orderByDesc('monto')
            ->limit(10)
            ->get();

        $rankingSobrantes = (clone $base)
            ->where('arq.diferencia','>',0)
            ->groupBy('a.id','a.codigo_agente','a.nombre_negocio')
            ->select(['a.id','a.codigo_agente','a.nombre_negocio'])
            ->selectRaw('COUNT(*) as incidencias')
            ->selectRaw('SUM(arq.diferencia) as monto')
            ->orderByDesc('monto')
            ->limit(10)
            ->get();

        $regionesFaltantes = (clone $base)
            ->where('arq.diferencia','<',0)
            ->groupBy('reg.id','reg.nombre')
            ->select(['reg.id','reg.nombre'])
            ->selectRaw('COUNT(*) as incidencias')
            ->selectRaw('SUM(ABS(arq.diferencia)) as monto')
            ->orderByDesc('monto')
            ->limit(10)
            ->get();

        $regionesSobrantes = (clone $base)
            ->where('arq.diferencia','>',0)
            ->groupBy('reg.id','reg.nombre')
            ->select(['reg.id','reg.nombre'])
            ->selectRaw('COUNT(*) as incidencias')
            ->selectRaw('SUM(arq.diferencia) as monto')
            ->orderByDesc('monto')
            ->limit(10)
            ->get();

        return view('administrador.reportes.index',compact(
            'metricas','rankingFaltantes','rankingSobrantes',
            'regionesFaltantes','regionesSobrantes','regiones','rutas',
            'desde','hasta','regionId','rutaId','tipo'
        ));
    }

    private function validarAdministrador(Usuario $usuario): void
    {
        $usuario->loadMissing('rol');
        abort_if(!$usuario->rol || $usuario->rol->nombre !== 'Administrador',403,'No tiene autorización para acceder a esta sección.');
    }
}
