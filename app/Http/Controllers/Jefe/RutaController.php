<?php

namespace App\Http\Controllers\Jefe;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RutaController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->validarJefe($usuario);

        $buscar = trim((string) $request->string('buscar'));
        $regionId = $request->integer('region_id');
        $estado = trim((string) $request->string('estado'));

        $regiones = DB::table('regiones')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'estado']);

        $rutas = DB::table('rutas as r')
            ->join('regiones as reg', 'reg.id', '=', 'r.region_id')
            ->leftJoin('asignaciones_promotor_ruta as apr', function ($join): void {
                $join->on('apr.ruta_id', '=', 'r.id')
                    ->where('apr.estado', true)
                    ->whereDate('apr.fecha_inicio', '<=', today())
                    ->where(function ($q): void {
                        $q->whereNull('apr.fecha_fin')
                          ->orWhereDate('apr.fecha_fin', '>=', today());
                    });
            })
            ->leftJoin('usuarios as up', 'up.id', '=', 'apr.promotor_usuario_id')
            ->leftJoin('datos_personales as dp', 'dp.usuario_id', '=', 'up.id')
            ->when($buscar !== '', function ($q) use ($buscar): void {
                $q->where(function ($s) use ($buscar): void {
                    $s->where('r.codigo', 'like', '%' . $buscar . '%')
                      ->orWhere('r.nombre', 'like', '%' . $buscar . '%')
                      ->orWhere('reg.nombre', 'like', '%' . $buscar . '%');
                });
            })
            ->when($regionId > 0, fn ($q) => $q->where('r.region_id', $regionId))
            ->when($estado === 'ACTIVA', fn ($q) => $q->where('r.estado', true))
            ->when($estado === 'INACTIVA', fn ($q) => $q->where('r.estado', false))
            ->select([
                'r.id',
                'r.codigo',
                'r.nombre',
                'r.region_id',
                'r.estado',
                'reg.nombre as region_nombre',
                'up.nombre_usuario as promotor_usuario',
                'dp.nombres as promotor_nombres',
                'dp.apellidos as promotor_apellidos',
            ])
            ->selectSub(
                DB::table('agentes')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('agentes.ruta_id', 'r.id'),
                'total_agentes'
            )
            ->selectSub(
                DB::table('agentes')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('agentes.ruta_id', 'r.id')
                    ->where('agentes.estado', 'ACTIVO'),
                'agentes_activos'
            )
            ->selectSub(
                DB::table('agentes as a2')
                    ->join('arqueos as arq2', 'arq2.agente_id', '=', 'a2.id')
                    ->selectRaw('COUNT(DISTINCT a2.id)')
                    ->whereColumn('a2.ruta_id', 'r.id')
                    ->where('a2.estado', 'ACTIVO')
                    ->where('arq2.tipo', 'DIARIO_AGENTE')
                    ->whereDate('arq2.fecha_arqueo', today())
                    ->where('arq2.estado', '!=', 'ANULADO'),
                'arqueados_hoy'
            )
            ->distinct()
            ->orderBy('reg.nombre')
            ->orderBy('r.nombre')
            ->paginate(15)
            ->withQueryString();

        $totalRutas = DB::table('rutas')->count();
        $rutasActivas = DB::table('rutas')->where('estado', true)->count();
        $rutasInactivas = DB::table('rutas')->where('estado', false)->count();

        $rutasSinPromotor = DB::table('rutas as r')
            ->where('r.estado', true)
            ->whereNotExists(function ($q): void {
                $q->selectRaw('1')
                  ->from('asignaciones_promotor_ruta as apr')
                  ->whereColumn('apr.ruta_id', 'r.id')
                  ->where('apr.estado', true)
                  ->whereDate('apr.fecha_inicio', '<=', today())
                  ->where(function ($s): void {
                      $s->whereNull('apr.fecha_fin')
                        ->orWhereDate('apr.fecha_fin', '>=', today());
                  });
            })
            ->count();

        return view('jefe.rutas.index', compact(
            'rutas',
            'regiones',
            'buscar',
            'regionId',
            'estado',
            'totalRutas',
            'rutasActivas',
            'rutasInactivas',
            'rutasSinPromotor'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->validarJefe($usuario);

        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:50', Rule::unique('rutas', 'codigo')],
            'nombre' => ['required', 'string', 'max:150'],
            'region_id' => ['required', 'integer', Rule::exists('regiones', 'id')],
        ]);

        $regionActiva = DB::table('regiones')
            ->where('id', $datos['region_id'])
            ->where('estado', true)
            ->exists();

        if (! $regionActiva) {
            return back()->withInput()->with(
                'warning',
                'La región seleccionada está inactiva.'
            );
        }

        DB::table('rutas')->insert([
            'codigo' => trim($datos['codigo']),
            'nombre' => trim($datos['nombre']),
            'region_id' => $datos['region_id'],
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('jefe.rutas.index')
            ->with('success', 'La ruta fue creada correctamente.');
    }

    public function update(Request $request, int $ruta): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->validarJefe($usuario);

        abort_if(
            ! DB::table('rutas')->where('id', $ruta)->exists(),
            404,
            'La ruta solicitada no existe.'
        );

        $datos = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rutas', 'codigo')->ignore($ruta),
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'region_id' => ['required', 'integer', Rule::exists('regiones', 'id')],
        ]);

        DB::table('rutas')
            ->where('id', $ruta)
            ->update([
                'codigo' => trim($datos['codigo']),
                'nombre' => trim($datos['nombre']),
                'region_id' => $datos['region_id'],
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('jefe.rutas.index')
            ->with('success', 'La ruta fue actualizada correctamente.');
    }

    public function cambiarEstado(Request $request, int $ruta): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->validarJefe($usuario);

        $registro = DB::table('rutas')->where('id', $ruta)->first();

        abort_if(! $registro, 404, 'La ruta solicitada no existe.');

        $nuevoEstado = ! (bool) $registro->estado;

        if (! $nuevoEstado) {
            $agentesActivos = DB::table('agentes')
                ->where('ruta_id', $ruta)
                ->where('estado', 'ACTIVO')
                ->count();

            if ($agentesActivos > 0) {
                return redirect()
                    ->route('jefe.rutas.index')
                    ->with(
                        'warning',
                        'No se puede desactivar la ruta porque tiene agentes activos asignados.'
                    );
            }

            $asignacionActiva = DB::table('asignaciones_promotor_ruta')
                ->where('ruta_id', $ruta)
                ->where('estado', true)
                ->whereDate('fecha_inicio', '<=', today())
                ->where(function ($q): void {
                    $q->whereNull('fecha_fin')
                      ->orWhereDate('fecha_fin', '>=', today());
                })
                ->exists();

            if ($asignacionActiva) {
                return redirect()
                    ->route('jefe.rutas.index')
                    ->with(
                        'warning',
                        'No se puede desactivar la ruta mientras tenga un Promotor asignado.'
                    );
            }
        }

        DB::table('rutas')
            ->where('id', $ruta)
            ->update([
                'estado' => $nuevoEstado,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('jefe.rutas.index')
            ->with(
                'success',
                $nuevoEstado
                    ? 'La ruta fue activada correctamente.'
                    : 'La ruta fue desactivada correctamente.'
            );
    }

    private function validarJefe(Usuario $usuario): void
    {
        $usuario->loadMissing('rol');

        abort_if(
            ! $usuario->rol
            || $usuario->rol->nombre !== 'jefedeAgentes',
            403,
            'No tiene autorización para acceder a esta sección.'
        );
    }
}
