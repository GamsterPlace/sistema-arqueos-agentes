<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->upsert([
            [
                'nombre' => 'agente',
                'descripcion' => 'Usuario responsable del arqueo diario del agente',
                'estado' => true,
            ],
            [
                'nombre' => 'Promotor',
                'descripcion' => 'Promotor de Agentes MICOOPE',
                'estado' => true,
            ],
            [
                'nombre' => 'jefedeAgentes',
                'descripcion' => 'Jefatura de Agentes',
                'estado' => true,
            ],
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Control administrativo total del sistema',
                'estado' => true,
            ],
            [
                'nombre' => 'Auditoria',
                'descripcion' => 'Departamento de Auditoría',
                'estado' => true,
            ],
            [
                'nombre' => 'Gerencia',
                'descripcion' => 'Consulta gerencial y reportes',
                'estado' => true,
            ],
        ], ['nombre'], ['descripcion', 'estado']);
    }
}
