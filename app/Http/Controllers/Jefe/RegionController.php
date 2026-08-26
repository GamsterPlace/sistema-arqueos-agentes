<?php

namespace App\Http\Controllers\Jefe;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->validarJefe($usuario);

        $buscar = trim((string) $request->string('buscar'));
        $estado = trim((string) $request->string('estado'));

        $regiones = DB::table('regiones as reg')
            ->when($buscar !== '', fn ($q) => $q->where('reg.nombre', 'like', '%' . $buscar . '%'))
            ->when($estado === 'ACTIVA', fn ($q) => $q->where('reg.estado', true))
            ->when($estado === 'INACTIVA', fn ($q) => $q->where('reg.estado', false))
            ->select(['reg.id','reg.nombre','reg.estado'])
            ->selectSub(
                DB::table('rutas')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('rutas.region_id', 'reg.id'),
                'total_rutas'
            )
            ->selectSub(
                DB::table('rutas')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('rutas.region_id', 'reg.id')
                    ->where('rutas.estado', true),
                'rutas_activas'
            )
            ->selectSub(
                DB::table('agentes as a')
                    ->join('rutas as r', 'r.id', '=', 'a.ruta_id')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('r.region_id', 'reg.id')
                    ->where('a.estado', 'ACTIVO'),
                'agentes_activos'
            )
            ->selectSub(
                DB::table('agentes as a2')
                    ->join('rutas as r2', 'r2.id', '=', 'a2.ruta_id')
                    ->join('arqueos as arq', 'arq.agente_id', '=', 'a2.id')
                    ->selectRaw('COUNT(DISTINCT a2.id)')
                    ->whereColumn('r2.region_id', 'reg.id')
                    ->where('a2.estado', 'ACTIVO')
                    ->where('arq.tipo', 'DIARIO_AGENTE')
                    ->whereDate('arq.fecha_arqueo', today())
                    ->where('arq.estado', '!=', 'ANULADO'),
                'agentes_arqueados_hoy'
            )
            ->orderBy('reg.nombre')
            ->paginate(12)
            ->withQueryString();

        $totalRegiones = DB::table('regiones')->count();
        $regionesActivas = DB::table('regiones')->where('estado', true)->count();
        $regionesInactivas = DB::table('regiones')->where('estado', false)->count();

        $regionesSinRutas = DB::table('regiones as reg')
            ->whereNotExists(function ($q): void {
                $q->selectRaw('1')
                    ->from('rutas as r')
                    ->whereColumn('r.region_id', 'reg.id');
            })
            ->count();

        return view('jefe.regiones.index', compact(
            'regiones',
            'buscar',
            'estado',
            'totalRegiones',
            'regionesActivas',
            'regionesInactivas',
            'regionesSinRutas'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->validarJefe($usuario);

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('regiones', 'nombre'),
            ],
        ]);

        DB::table('regiones')->insert([
            'nombre' => trim($datos['nombre']),
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('jefe.regiones.index')
            ->with('success', 'La región fue creada correctamente.');
    }

    public function update(Request $request, int $region): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->validarJefe($usuario);

        abort_if(
            ! DB::table('regiones')->where('id', $region)->exists(),
            404,
            'La región solicitada no existe.'
        );

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('regiones', 'nombre')->ignore($region),
            ],
        ]);

        DB::table('regiones')
            ->where('id', $region)
            ->update([
                'nombre' => trim($datos['nombre']),
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('jefe.regiones.index')
            ->with('success', 'La región fue actualizada correctamente.');
    }

    public function cambiarEstado(Request $request, int $region): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->validarJefe($usuario);

        $registro = DB::table('regiones')->where('id', $region)->first();

        abort_if(! $registro, 404, 'La región solicitada no existe.');

        $nuevoEstado = ! (bool) $registro->estado;

        if (! $nuevoEstado) {
            $rutasActivas = DB::table('rutas')
                ->where('region_id', $region)
                ->where('estado', true)
                ->count();

            if ($rutasActivas > 0) {
                return redirect()
                    ->route('jefe.regiones.index')
                    ->with(
                        'warning',
                        'No se puede desactivar la región porque tiene rutas activas.'
                    );
            }
        }

        DB::table('regiones')
            ->where('id', $region)
            ->update([
                'estado' => $nuevoEstado,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('jefe.regiones.index')
            ->with(
                'success',
                $nuevoEstado
                    ? 'La región fue activada correctamente.'
                    : 'La región fue desactivada correctamente.'
            );
    }

    private function validarJefe(Usuario $usuario): void
    {
        $usuario->loadMissing('rol');

        abort_if(
            ! $usuario->rol || $usuario->rol->nombre !== 'jefedeAgentes',
            403,
            'No tiene autorización para acceder a esta sección.'
        );
    }
}
