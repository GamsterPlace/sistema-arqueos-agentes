<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Arqueo;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ArqueoAnuladoController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->validarAdministrador($usuario);

        $buscar = trim((string)$request->string('buscar'));
        $tipo = trim((string)$request->string('tipo'));
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $arqueos = DB::table('arqueos as arq')
            ->join('agentes as a','a.id','=','arq.agente_id')
            ->leftJoin('rutas as r','r.id','=','a.ruta_id')
            ->leftJoin('regiones as reg','reg.id','=','r.region_id')
            ->where('arq.estado','ANULADO')
            ->when($tipo !== '', fn($q) => $q->where('arq.tipo',$tipo))
            ->when($desde, fn($q) => $q->whereDate('arq.fecha_arqueo','>=',$desde))
            ->when($hasta, fn($q) => $q->whereDate('arq.fecha_arqueo','<=',$hasta))
            ->when($buscar !== '', function($q) use ($buscar) {
                $q->where(function($s) use ($buscar) {
                    $s->where('arq.numero_arqueo','like','%'.$buscar.'%')
                      ->orWhere('a.codigo_agente','like','%'.$buscar.'%')
                      ->orWhere('a.nombre_negocio','like','%'.$buscar.'%')
                      ->orWhere('r.nombre','like','%'.$buscar.'%')
                      ->orWhere('reg.nombre','like','%'.$buscar.'%');
                });
            })
            ->select([
                'arq.id','arq.numero_arqueo','arq.fecha_arqueo','arq.tipo',
                'arq.estado','arq.diferencia','arq.fuera_fecha_ordinaria',
                'a.codigo_agente','a.nombre_negocio',
                'r.nombre as ruta_nombre','reg.nombre as region_nombre'
            ])
            ->orderByDesc('arq.fecha_arqueo')
            ->orderByDesc('arq.id')
            ->paginate(20)
            ->withQueryString();

        $resumen = (object)[
            'total' => DB::table('arqueos')->where('estado','ANULADO')->count(),
            'agente' => DB::table('arqueos')->where('estado','ANULADO')->where('tipo','DIARIO_AGENTE')->count(),
            'promotor' => DB::table('arqueos')->where('estado','ANULADO')->where('tipo','VISITA_PROMOTOR')->count(),
            'extemporaneos' => DB::table('arqueos')->where('estado','ANULADO')->where('fuera_fecha_ordinaria',true)->count(),
        ];

        return view('administrador.arqueos-anulados.index',compact(
            'arqueos','buscar','tipo','desde','hasta','resumen'
        ));
    }

    public function anular(Request $request, Arqueo $arqueo): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->validarAdministrador($usuario);

        abort_if($arqueo->estado === 'ANULADO',422,'El arqueo ya se encuentra anulado.');

        $validated = $request->validate([
            'motivo' => ['required','string','min:5','max:1000'],
        ]);

        $update = [
            'estado' => 'ANULADO',
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('arqueos','motivo_anulacion')) {
            $update['motivo_anulacion'] = $validated['motivo'];
        }

        if (Schema::hasColumn('arqueos','anulado_por')) {
            $update['anulado_por'] = $usuario->id;
        }

        if (Schema::hasColumn('arqueos','anulado_at')) {
            $update['anulado_at'] = now();
        }

        DB::table('arqueos')->where('id',$arqueo->id)->update($update);

        return back()->with('success','Arqueo anulado correctamente.');
    }

    private function validarAdministrador(Usuario $usuario): void
    {
        $usuario->loadMissing('rol');
        abort_if(!$usuario->rol || $usuario->rol->nombre !== 'Administrador',403,'No tiene autorización para acceder a esta sección.');
    }
}
