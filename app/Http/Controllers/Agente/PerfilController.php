<?php

namespace App\Http\Controllers\Agente;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PerfilController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $usuario->loadMissing([
            'rol',
            'datosPersonales',
            'agente.ruta.region',
        ]);

        $agente = $usuario->agente;

        abort_if(
            ! $agente,
            403,
            'La cuenta no tiene un agente asociado.'
        );

        return view('agente.perfil.index', [
            'usuario' => $usuario,
            'datosPersonales' => $usuario->datosPersonales,
            'agente' => $agente,
            'ruta' => $agente->ruta,
            'region' => $agente->ruta?->region,
        ]);
    }

    public function actualizar(
        Request $request
    ): RedirectResponse {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $usuario->loadMissing([
            'datosPersonales',
            'agente',
        ]);

        abort_if(
            ! $usuario->agente,
            403,
            'La cuenta no tiene un agente asociado.'
        );

        $datosValidados = $request->validate(
            [
                'nombre_usuario' => [
                    'required',
                    'string',
                    'max:80',
                    Rule::unique(
                        'usuarios',
                        'nombre_usuario'
                    )->ignore($usuario->id),
                ],
                'nombres' => [
                    'required',
                    'string',
                    'max:120',
                ],
                'apellidos' => [
                    'required',
                    'string',
                    'max:120',
                ],
            ],
            [
                'nombre_usuario.required' =>
                    'El nombre de usuario es obligatorio.',

                'nombre_usuario.unique' =>
                    'El nombre de usuario ya está en uso.',

                'nombres.required' =>
                    'Los nombres son obligatorios.',

                'apellidos.required' =>
                    'Los apellidos son obligatorios.',
            ]
        );

        DB::transaction(function () use (
            $usuario,
            $datosValidados
        ): void {
            $usuario->update([
                'nombre_usuario' =>
                    trim($datosValidados['nombre_usuario']),
            ]);

            $usuario->datosPersonales()->updateOrCreate(
                [
                    'usuario_id' => $usuario->id,
                ],
                [
                    'nombres' =>
                        trim($datosValidados['nombres']),

                    'apellidos' =>
                        trim($datosValidados['apellidos']),
                ]
            );
        });

        return redirect()
            ->route('agente.perfil.index')
            ->with(
                'success',
                'El perfil fue actualizado correctamente.'
            );
    }
}
