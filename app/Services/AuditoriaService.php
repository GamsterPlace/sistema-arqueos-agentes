<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class AuditoriaService
{
    /**
     * Registra una acción sensible en la bitácora de auditoría.
     *
     * IMPORTANTE:
     * - Nunca almacena contraseñas, tokens, hashes de contraseña ni secretos.
     * - Los valores anteriores/nuevos se guardan como JSON.
     * - El usuario responsable queda relacionado mediante usuario_id.
     */
    public function registrar(
        ?Usuario $usuario,
        string $modulo,
        string $accion,
        ?string $tablaAfectada = null,
        ?int $registroId = null,
        string $descripcion = '',
        ?array $valoresAnteriores = null,
        ?array $valoresNuevos = null
    ): void {
        DB::table('auditoria')->insert([
            'usuario_id' => $usuario?->id,
            'modulo' => trim($modulo),
            'accion' => trim($accion),
            'tabla_afectada' => $tablaAfectada,
            'registro_id' => $registroId,
            'descripcion' => trim($descripcion) !== ''
                ? trim($descripcion)
                : 'Acción registrada automáticamente por el sistema.',
            'valores_anteriores' => $this->prepararJson($valoresAnteriores),
            'valores_nuevos' => $this->prepararJson($valoresNuevos),
            'created_at' => now(),
        ]);
    }

    /**
     * Convierte los valores a JSON después de eliminar información sensible.
     */
    private function prepararJson(?array $valores): ?string
    {
        if ($valores === null) {
            return null;
        }

        $valores = $this->sanitizar($valores);

        if ($valores === []) {
            return null;
        }

        return json_encode(
            $valores,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_PRESERVE_ZERO_FRACTION
        );
    }

    /**
     * Elimina de forma recursiva claves que nunca deben llegar a auditoría.
     */
    private function sanitizar(array $datos): array
    {
        $clavesSensibles = [
            'password',
            'password_confirmation',
            'password_actual',
            'current_password',
            'new_password',
            'new_password_confirmation',
            'remember_token',
            'token',
            'api_token',
            'access_token',
            'refresh_token',
            'secret',
            'firma_electronica',
            'hash_documento',
        ];

        $resultado = [];

        foreach ($datos as $clave => $valor) {
            $claveNormalizada = strtolower((string) $clave);

            if (in_array($claveNormalizada, $clavesSensibles, true)) {
                continue;
            }

            if (is_array($valor)) {
                $resultado[$clave] = $this->sanitizar($valor);
                continue;
            }

            if ($valor instanceof \DateTimeInterface) {
                $resultado[$clave] = $valor->format('Y-m-d H:i:s');
                continue;
            }

            if (is_bool($valor)) {
                $resultado[$clave] = $valor;
                continue;
            }

            if (is_scalar($valor) || $valor === null) {
                $resultado[$clave] = $valor;
                continue;
            }

            $resultado[$clave] = (string) $valor;
        }

        return $resultado;
    }
}
