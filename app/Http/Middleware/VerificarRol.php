<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $usuario = $request->user();

        if (! $usuario) {
            return redirect()->route('login');
        }

        $usuario->loadMissing('rol');

        if (! $usuario->rol) {
            abort(403, 'El usuario no tiene un rol asignado.');
        }

        if (! in_array($usuario->rol->nombre, $roles, true)) {
            abort(403, 'No tiene permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
