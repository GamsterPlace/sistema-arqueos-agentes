<?php

namespace App\Http\Controllers\Auditoria;

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
        $this->validarAuditoria($usuario);

        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $resultado = trim((string) $request->string('resultado'));

        $base = DB::table('arqueos as arq')
            ->where('arq.tipo', 'VISITA_AUDITORIA')
            ->where('arq.creado_por', $usuario->id)
            ->where('arq.estado', '!=', 'ANULADO')
            ->when($desde, fn ($q) => $q->whereDate('arq.fecha_arqueo', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('arq.fecha_arqueo', '<=', $hasta))
            ->when($resultado === 'FALTANTE', fn ($q) => $q->where('arq.diferencia', '<', 0))
            ->when($resultado === 'SOBRANTE', fn ($q) => $q->where('arq.diferencia', '>', 0))
            ->when($resultado === 'EXACTO', fn ($q) => $q->where('arq.diferencia', 0));

        $metricas = (clone $base)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN arq.diferencia < 0 THEN 1 ELSE 0 END) as faltantes')
            ->selectRaw('SUM(CASE WHEN arq.diferencia > 0 THEN 1 ELSE 0 END) as sobrantes')
            ->selectRaw('SUM(CASE WHEN arq.diferencia = 0 THEN 1 ELSE 0 END) as exactos')
            ->selectRaw('SUM(CASE WHEN arq.diferencia < 0 THEN ABS(arq.diferencia) ELSE 0 END) as monto_faltantes')
            ->selectRaw('SUM(CASE WHEN arq.diferencia > 0 THEN arq.diferencia ELSE 0 END) as monto_sobrantes')
            ->first();

        $rankingFaltantes = (clone $base)
            ->where('arq.diferencia', '<', 0)
            ->groupBy('arq.agente_id', 'arq.codigo_agente_historico', 'arq.nombre_negocio_historico')
            ->select('arq.agente_id', 'arq.codigo_agente_historico', 'arq.nombre_negocio_historico')
            ->selectRaw('COUNT(*) as incidencias')
            ->selectRaw('SUM(ABS(arq.diferencia)) as monto')
            ->orderByDesc('monto')
            ->limit(10)
            ->get();

        $rankingSobrantes = (clone $base)
            ->where('arq.diferencia', '>', 0)
            ->groupBy('arq.agente_id', 'arq.codigo_agente_historico', 'arq.nombre_negocio_historico')
            ->select('arq.agente_id', 'arq.codigo_agente_historico', 'arq.nombre_negocio_historico')
            ->selectRaw('COUNT(*) as incidencias')
            ->selectRaw('SUM(arq.diferencia) as monto')
            ->orderByDesc('monto')
            ->limit(10)
            ->get();

        $porRegionFaltantes = (clone $base)
            ->where('arq.diferencia', '<', 0)
            ->groupBy('arq.region_historica')
            ->select('arq.region_historica')
            ->selectRaw('COUNT(*) as incidencias')
            ->selectRaw('SUM(ABS(arq.diferencia)) as monto')
            ->orderByDesc('monto')
            ->limit(10)
            ->get();

        $porRegionSobrantes = (clone $base)
            ->where('arq.diferencia', '>', 0)
            ->groupBy('arq.region_historica')
            ->select('arq.region_historica')
            ->selectRaw('COUNT(*) as incidencias')
            ->selectRaw('SUM(arq.diferencia) as monto')
            ->orderByDesc('monto')
            ->limit(10)
            ->get();

        return view('auditoria.reportes.index', compact(
            'metricas',
            'rankingFaltantes',
            'rankingSobrantes',
            'porRegionFaltantes',
            'porRegionSobrantes',
            'desde',
            'hasta',
            'resultado'
        ));
    }

    private function validarAuditoria(Usuario $usuario): void
    {
        $usuario->loadMissing('rol');

        abort_if(
            ! $usuario->rol || $usuario->rol->nombre !== 'Auditoria',
            403,
            'No tiene autorización para acceder a esta sección.'
        );
    }
}
