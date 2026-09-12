<?php

use App\Http\Controllers\Auth\CambiarPasswordController;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Agente\ArqueoController;
use App\Http\Controllers\Agente\ArqueoPromotorController;
use App\Http\Controllers\Agente\DashboardController as DashboardAgenteController;
use App\Http\Controllers\Agente\ArqueoExtemporaneoController;
use App\Http\Controllers\Agente\PerfilController as PerfilAgenteController;
use App\Http\Controllers\Agente\CumplimientoArqueoController;

use App\Http\Controllers\Promotor\ArqueoAgenteController;
use App\Http\Controllers\Promotor\ArqueoController as PromotorArqueoController;
use App\Http\Controllers\Promotor\DashboardController as DashboardPromotorController;
use App\Http\Controllers\Promotor\MisAgentesController;
use App\Http\Controllers\Promotor\RutasAsignadasController;
use App\Http\Controllers\Promotor\ReporteController;
use App\Http\Controllers\Promotor\PerfilController as PerfilPromotorController;
use App\Http\Controllers\Promotor\HabilitacionArqueoAtrasadoController;
use App\Http\Controllers\Promotor\CumplimientoArqueoController as CumplimientoPromotorController;

use App\Http\Controllers\Jefe\DashboardController as DashboardJefeController;
use App\Http\Controllers\Jefe\EstadoArqueosController;
use App\Http\Controllers\Jefe\AgenteController as JefeAgenteController;
use App\Http\Controllers\Jefe\ArqueoAgenteController as JefeArqueoAgenteController;
use App\Http\Controllers\Jefe\ArqueoPromotorController as JefeArqueoPromotorController;
use App\Http\Controllers\Jefe\AgentesPorRutaController;
use App\Http\Controllers\Jefe\AgentesPorRegionController;
use App\Http\Controllers\Jefe\PromotorController as JefePromotorController;
use App\Http\Controllers\Jefe\RutasPromotoresController;
use App\Http\Controllers\Jefe\RutaController as JefeRutaController;
use App\Http\Controllers\Jefe\RegionController as JefeRegionController;
use App\Http\Controllers\Jefe\ArqueoGeneralController as JefeArqueoGeneralController;
use App\Http\Controllers\Jefe\HabilitacionArqueoAtrasadoController as JefeHabilitacionArqueoAtrasadoController;
use App\Http\Controllers\Jefe\ReporteController as JefeReporteController;
use App\Http\Controllers\Jefe\PerfilController as JefePerfilController;
use App\Http\Controllers\Jefe\AnulacionController;
use App\Http\Controllers\Jefe\CertificacionController;

use App\Http\Controllers\Gerencia\DashboardController as DashboardGerenciaController;
use App\Http\Controllers\Gerencia\EstadoArqueosController as EstadoArqueosGerenciaController;
use App\Http\Controllers\Gerencia\AgenteController;
use App\Http\Controllers\Gerencia\ArqueoAgenteController as ArqueoAgenteGerenciaController;
use App\Http\Controllers\Gerencia\ArqueoPromotorController as ArqueoPromotorGerenciaController;
use App\Http\Controllers\Gerencia\AgentesPorRutaController as AgentesPorRutaGerenciaController;
use App\Http\Controllers\Gerencia\AgentesPorRegionController as AgentesPorRegionGerenciaController;
use App\Http\Controllers\Gerencia\PromotorController;
use App\Http\Controllers\Gerencia\RutasPromotoresController as RutasPromotoresGerenciaController;
use App\Http\Controllers\Gerencia\ArqueoExtemporaneoController as ArqueoExtemporaneoGerenciaController;
use App\Http\Controllers\Gerencia\ArqueoAnuladoController as ArqueoAnuladoGerenciaController;
use App\Http\Controllers\Gerencia\ArqueoGeneralController;
use App\Http\Controllers\Gerencia\ReporteController as ReporteGerenciaController;
use App\Http\Controllers\Gerencia\PerfilController as PerfilGerenciaController;


use App\Http\Controllers\Administrador\DashboardController as DashboardAdministradorController;
use App\Http\Controllers\Administrador\UsuarioController as UsuarioAdministradorController;
use App\Http\Controllers\Administrador\ArqueoGeneralController as ArqueoGeneralAdministradorController;
use App\Http\Controllers\Administrador\ArqueoAgenteController as ArqueoAgenteAdministradorController;
use App\Http\Controllers\Administrador\ArqueoPromotorController as ArqueoPromotorAdministradorController;
use App\Http\Controllers\Administrador\ArqueoAnuladoController as ArqueoAnuladoAdministradorController;
use App\Http\Controllers\Administrador\AuditoriaController as AuditoriaAdministradorController;
use App\Http\Controllers\Administrador\ReporteController as ReporteAdministradorController;
use App\Http\Controllers\Administrador\PerfilController as PerfilAdministradorController;
use App\Http\Controllers\Administrador\AgenteController as AgenteAdministradorController;
use App\Http\Controllers\Administrador\AuditoriaAgentesController as AuditoriaAgentesAdministradorController;
use App\Http\Controllers\Administrador\AuditoriaSistemaController as AuditoriaSistemaAdministradorController;


use App\Http\Controllers\Auditoria\DashboardController as DashboardAuditoriaController;
use App\Http\Controllers\Auditoria\ArqueoController as ArqueoAuditoriaController;
use App\Http\Controllers\Auditoria\ReporteController as ReporteAuditoriaController;
use App\Http\Controllers\Auditoria\PerfilController as PerfilAuditoriaController;
use App\Http\Controllers\Auditoria\CumplimientoArqueoController as CumplimientoAuditoriaController;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas para invitados
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get(
        '/login',
        [LoginController::class, 'mostrarLogin']
    )->name('login');

    Route::post(
        '/login',
        [LoginController::class, 'iniciarSesion']
    )->name('login.iniciar');
});

/*
|--------------------------------------------------------------------------
| Rutas de autenticación
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post(
        '/logout',
        [LoginController::class, 'cerrarSesion']
    )->name('logout');

    Route::get(
        '/cambiar-password',
        [CambiarPasswordController::class, 'index']
    )->name('password.cambiar');

    Route::put(
        '/cambiar-password',
        [CambiarPasswordController::class, 'actualizar']
    )->name('password.actualizar');
});

/*
|--------------------------------------------------------------------------
| Ruta principal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| Redirección central según rol
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    /** @var \App\Models\Usuario $usuario */
    $usuario = Auth::user();

    $usuario->loadMissing('rol');

    if (! $usuario->rol) {
        abort(403, 'El usuario no tiene un rol asignado.');
    }

    return match ($usuario->rol->nombre) {
        'Administrador' => redirect()
            ->route('administrador.dashboard'),

        'Promotor' => redirect()
            ->route('promotor.dashboard'),

        'jefedeAgentes' => redirect()
            ->route('jefe.dashboard'),

        'Auditoria' => redirect()
            ->route('auditoria.dashboard'),

        'Gerencia' => redirect()
            ->route('gerencia.dashboard'),

        'agente' => redirect()
            ->route('agente.dashboard'),

        default => abort(403, 'Rol no reconocido.'),
    };
})
    ->middleware([
        'auth',
        'password.actualizada',
    ])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Administrador
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'password.actualizada',
    'rol:Administrador',
])
    ->prefix('administrador')
    ->name('administrador.')
    ->group(function () {

        Route::get(
            '/dashboard',
            [DashboardAdministradorController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/usuarios',
            [UsuarioAdministradorController::class, 'index']
        )->name('usuarios.index');

        Route::get(
            '/usuarios/crear',
            [UsuarioAdministradorController::class, 'create']
        )->name('usuarios.create');

        Route::post(
            '/usuarios',
            [UsuarioAdministradorController::class, 'store']
        )->name('usuarios.store');

        Route::get(
            '/usuarios/{usuario}',
            [UsuarioAdministradorController::class, 'show']
        )->name('usuarios.show');

        Route::get(
            '/usuarios/{usuario}/editar',
            [UsuarioAdministradorController::class, 'edit']
        )->name('usuarios.edit');

        Route::put(
            '/usuarios/{usuario}',
            [UsuarioAdministradorController::class, 'update']
        )->name('usuarios.update');

        Route::patch(
            '/usuarios/{usuario}/estado',
            [UsuarioAdministradorController::class, 'cambiarEstado']
        )->name('usuarios.estado');

        Route::patch(
            '/usuarios/{usuario}/password',
            [UsuarioAdministradorController::class, 'actualizarPassword']
        )->name('usuarios.password');


        Route::get('/arqueos',[ArqueoGeneralAdministradorController::class,'index'])->name('arqueos.index');
        Route::get('/arqueos/{arqueo}',[ArqueoGeneralAdministradorController::class,'show'])->name('arqueos.show');
        Route::get('/arqueos/{arqueo}/imprimir',[ArqueoGeneralAdministradorController::class,'imprimir'])->name('arqueos.imprimir');

        Route::get('/arqueos-agentes',[ArqueoAgenteAdministradorController::class,'index'])->name('arqueos-agentes.index');
        Route::get('/arqueos-agentes/{arqueo}',[ArqueoAgenteAdministradorController::class,'show'])->name('arqueos-agentes.show');
        Route::get('/arqueos-agentes/{arqueo}/imprimir',[ArqueoAgenteAdministradorController::class,'imprimir'])->name('arqueos-agentes.imprimir');

        Route::get('/arqueos-promotores',[ArqueoPromotorAdministradorController::class,'index'])->name('arqueos-promotores.index');
        Route::get('/arqueos-promotores/{arqueo}',[ArqueoPromotorAdministradorController::class,'show'])->name('arqueos-promotores.show');
        Route::get('/arqueos-promotores/{arqueo}/imprimir',[ArqueoPromotorAdministradorController::class,'imprimir'])->name('arqueos-promotores.imprimir');

        Route::get('/arqueos-anulados',[ArqueoAnuladoAdministradorController::class,'index'])->name('arqueos-anulados.index');
        Route::patch('/arqueos/{arqueo}/anular',[ArqueoAnuladoAdministradorController::class,'anular'])->name('arqueos.anular');

        Route::get('/auditoria',[AuditoriaAdministradorController::class,'index'])->name('auditoria.index');

        Route::get('/reportes',[ReporteAdministradorController::class,'index'])->name('reportes.index');

        Route::get(
            '/reportes/imprimir',
            [\App\Http\Controllers\Administrador\ReporteController::class, 'imprimir']
        )->name('reportes.imprimir');

        Route::get('/perfil',[PerfilAdministradorController::class,'index'])->name('perfil.index');

        Route::get(
            '/agentes',
            [AgenteAdministradorController::class, 'index']
        )->name('agentes.index');

        Route::get(
            '/agentes/crear',
            [AgenteAdministradorController::class, 'create']
        )->name('agentes.create');

        Route::post(
            '/agentes',
            [AgenteAdministradorController::class, 'store']
        )->name('agentes.store');

        Route::get(
            '/agentes/{agente}',
            [AgenteAdministradorController::class, 'show']
        )->name('agentes.show');

        Route::get(
            '/agentes/{agente}/editar',
            [AgenteAdministradorController::class, 'edit']
        )->name('agentes.edit');

        Route::put(
            '/agentes/{agente}',
            [AgenteAdministradorController::class, 'update']
        )->name('agentes.update');

        Route::patch(
            '/agentes/{agente}/estado',
            [AgenteAdministradorController::class, 'cambiarEstado']
        )->name('agentes.estado');


        Route::get(
            '/auditoria/agentes',
            [AuditoriaAgentesAdministradorController::class, 'index']
        )->name('auditoria-agentes.index');

        Route::get(
            '/auditoria/agentes/{arqueo}',
            [AuditoriaAgentesAdministradorController::class, 'show']
        )->name('auditoria-agentes.show');

        Route::get(
            '/auditoria/agentes/{arqueo}/imprimir',
            [AuditoriaAgentesAdministradorController::class, 'imprimir']
        )->name('auditoria-agentes.imprimir');

        Route::get(
            '/auditoria/sistema',
            [AuditoriaSistemaAdministradorController::class, 'index']
        )->name('auditoria-sistema.index');

        Route::get(
            '/auditoria/sistema/{auditoria}',
            [AuditoriaSistemaAdministradorController::class, 'show']
        )->name('auditoria-sistema.show');





    });

/*
|--------------------------------------------------------------------------
| Promotor
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'password.actualizada',
    'rol:Promotor',
])
    ->prefix('promotor')
    ->name('promotor.')
    ->group(function () {
        Route::get(
            '/dashboard',
            [DashboardPromotorController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/arqueos',
            [PromotorArqueoController::class, 'index']
        )->name('arqueos.index');

        Route::get(
            '/arqueos/crear',
            [PromotorArqueoController::class, 'create']
        )->name('arqueos.create');

        Route::post(
            '/arqueos',
            [PromotorArqueoController::class, 'store']
        )->name('arqueos.store');

        Route::get(
            '/arqueos/{arqueo}',
            [PromotorArqueoController::class, 'show']
        )->name('arqueos.show');

        Route::get(
            '/arqueos/{arqueo}/imprimir',
            [PromotorArqueoController::class, 'imprimir']
        )->name('arqueos.imprimir');

        Route::get(
            '/arqueos-agentes',
            [ArqueoAgenteController::class, 'index']
        )->name('arqueos-agentes.index');

        Route::get(
            '/arqueos-agentes/{agente}/arqueos',
            [ArqueoAgenteController::class, 'arqueos']
        )->name('arqueos-agentes.arqueos');

        Route::get(
            '/arqueos-agentes/arqueo/{arqueo}',
            [ArqueoAgenteController::class, 'show']
        )->name('arqueos-agentes.show');

        Route::get(
            '/arqueos-agentes/arqueo/{arqueo}/imprimir',
            [ArqueoAgenteController::class, 'imprimir']
        )->name('arqueos-agentes.imprimir');

        Route::post(
            '/arqueos-agentes/arqueo/{arqueo}/certificar',
            [ArqueoAgenteController::class, 'certificar']
        )->name('arqueos-agentes.certificar');

        Route::post(
            '/arqueos-agentes/arqueo/{arqueo}/anular',
            [ArqueoAgenteController::class, 'anular']
        )->name('arqueos-agentes.anular');

        Route::get(
            '/cumplimientos',
            [CumplimientoPromotorController::class, 'index']
        )->name('cumplimientos.index');


        Route::get(
            '/habilitaciones-atrasadas',
            [HabilitacionArqueoAtrasadoController::class, 'index']
        )->name('habilitaciones-atrasadas.index');

        Route::post(
            '/habilitaciones-atrasadas',
            [HabilitacionArqueoAtrasadoController::class, 'store']
        )->name('habilitaciones-atrasadas.store');

        Route::post(
            '/habilitaciones-atrasadas/{habilitacion}/cancelar',
            [HabilitacionArqueoAtrasadoController::class, 'cancelar']
        )->name('habilitaciones-atrasadas.cancelar');

        Route::get(
            '/mis-agentes',
            [MisAgentesController::class, 'index']
        )->name('mis-agentes.index');

        Route::get(
            '/rutas-asignadas',
            [RutasAsignadasController::class, 'index']
        )->name('rutas-asignadas.index');

        Route::get(
            '/reportes',
            [ReporteController::class, 'index']
        )->name('reportes.index');

        Route::get(
            '/perfil',
            [PerfilPromotorController::class, 'index']
        )->name('perfil.index');

        Route::put(
            '/perfil',
            [PerfilPromotorController::class, 'actualizar']
        )->name('perfil.actualizar');


    });

/*
|--------------------------------------------------------------------------
| Jefe de Agentes
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'password.actualizada',
    'rol:jefedeAgentes',
])
    ->prefix('jefe-agentes')
    ->name('jefe.')
    ->group(function () {
        Route::get(
            '/dashboard',
            [DashboardJefeController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/estado-arqueos',
            [EstadoArqueosController::class, 'index']
        )->name('estado-arqueos.index');

        Route::get(
            '/estado-arqueos/detalle',
            [EstadoArqueosController::class, 'detalle']
        )->name('estado-arqueos.detalle');

        Route::get(
            '/agentes',
            [JefeAgenteController::class, 'index']
        )->name('agentes.index');

        Route::get(
            '/agentes/{agente}',
            [JefeAgenteController::class, 'show']
        )->name('agentes.show');

        Route::get(
            '/agentes/{agente}/imprimir',
            [JefeAgenteController::class, 'imprimirFicha']
        )->name('agentes.imprimir');

        Route::get(
            '/agentes/{agente}/arqueos/{arqueo}/imprimir',
            [JefeAgenteController::class, 'imprimir']
        )->name('agentes.arqueos.imprimir');

        Route::get(
            '/arqueos-agentes',
            [JefeArqueoAgenteController::class, 'index']
        )->name('arqueos-agentes.index');

        Route::get(
            '/arqueos-agentes/{arqueo}',
            [JefeArqueoAgenteController::class, 'show']
        )->name('arqueos-agentes.show');

        Route::get(
            '/arqueos-agentes/{arqueo}/imprimir',
            [JefeArqueoAgenteController::class, 'imprimir']
        )->name('arqueos-agentes.imprimir');

        Route::get(
            '/arqueos-promotores',
            [JefeArqueoPromotorController::class, 'index']
        )->name('arqueos-promotores.index');

        Route::get(
            '/arqueos-promotores/{arqueo}',
            [JefeArqueoPromotorController::class, 'show']
        )->name('arqueos-promotores.show');

        Route::get(
            '/arqueos-promotores/{arqueo}/imprimir',
            [JefeArqueoPromotorController::class, 'imprimir']
        )->name('arqueos-promotores.imprimir');

        Route::get(
            '/agentes-ruta',
            [AgentesPorRutaController::class, 'index']
        )->name('agentes-ruta.index');

        Route::get(
            '/agentes-region',
            [AgentesPorRegionController::class, 'index']
        )->name('agentes-region.index');

        Route::get(
            '/promotores',
            [JefePromotorController::class, 'index']
        )->name('promotores.index');

        Route::get(
            '/promotores/{promotor}',
            [JefePromotorController::class, 'show']
        )->name('promotores.show');

        Route::get(
            '/rutas-promotores',
            [RutasPromotoresController::class, 'index']
        )->name('rutas-promotores.index');

        Route::get(
            '/rutas',
            [JefeRutaController::class, 'index']
        )->name('rutas.index');

        Route::post(
            '/rutas',
            [JefeRutaController::class, 'store']
        )->name('rutas.store');

        Route::put(
            '/rutas/{ruta}',
            [JefeRutaController::class, 'update']
        )->name('rutas.update');

        Route::patch(
            '/rutas/{ruta}/estado',
            [JefeRutaController::class, 'cambiarEstado']
        )->name('rutas.estado');

        Route::get(
            '/rutas/{ruta}/agentes',
            [JefeRutaController::class, 'agentes']
        )->name('rutas.agentes');

        Route::patch(
            '/rutas/{ruta}/agentes/reasignar',
            [JefeRutaController::class, 'reasignarAgentes']
        )->name('rutas.agentes.reasignar');

        Route::get(
            '/regiones',
            [JefeRegionController::class, 'index']
        )->name('regiones.index');

        Route::post(
            '/regiones',
            [JefeRegionController::class, 'store']
        )->name('regiones.store');

        Route::put(
            '/regiones/{region}',
            [JefeRegionController::class, 'update']
        )->name('regiones.update');

        Route::patch(
            '/regiones/{region}/estado',
            [JefeRegionController::class, 'cambiarEstado']
        )->name('regiones.estado');

        Route::get(
            '/regiones/{region}/rutas',
            [JefeRegionController::class, 'rutas']
        )->name('regiones.rutas');

        Route::patch(
            '/regiones/{region}/rutas/reasignar',
            [JefeRegionController::class, 'reasignarRutas']
        )->name('regiones.rutas.reasignar');

        Route::get(
        '/arqueos',
        [JefeArqueoGeneralController::class, 'index']
        )->name('arqueos.index');

        Route::get(
            '/arqueos/{arqueo}',
            [JefeArqueoGeneralController::class, 'show']
        )->name('arqueos.show');

        Route::get(
            '/arqueos/{arqueo}/imprimir',
            [JefeArqueoGeneralController::class, 'imprimir']
        )->name('arqueos.imprimir');

        Route::post(
            '/arqueos/{arqueo}/anular',
            [JefeArqueoGeneralController::class, 'anular']
        )->name('arqueos.anular');


        Route::get(
            '/habilitaciones-atrasadas',
            [
                JefeHabilitacionArqueoAtrasadoController::class,
                'index',
            ]
        )->name('habilitaciones-atrasadas.index');

        Route::post(
            '/habilitaciones-atrasadas',
            [
                JefeHabilitacionArqueoAtrasadoController::class,
                'store',
            ]
        )->name('habilitaciones-atrasadas.store');

        Route::post(
            '/habilitaciones-atrasadas/{habilitacion}/cancelar',
            [
                JefeHabilitacionArqueoAtrasadoController::class,
                'cancelar',
            ]
        )->name('habilitaciones-atrasadas.cancelar');

        Route::get(
            '/reportes',
            [JefeReporteController::class, 'index']
        )->name('reportes.index');

        Route::get(
            '/reportes/imprimir',
            [JefeReporteController::class, 'imprimir']
        )->name('reportes.imprimir');

        Route::get(
            '/certificaciones',
            [CertificacionController::class, 'index']
        )->name('certificaciones.index');

        Route::get(
            '/certificaciones/{arqueo}',
            [CertificacionController::class, 'show']
        )->name('certificaciones.show');

        Route::post(
            '/certificaciones/{arqueo}/certificar',
            [CertificacionController::class, 'certificar']
        )->name('certificaciones.certificar');

        Route::get(
            '/anulaciones',
            [AnulacionController::class, 'index']
        )->name('anulaciones.index');

        Route::get(
            '/anulaciones/{arqueo}',
            [AnulacionController::class, 'show']
        )->name('anulaciones.show');

         Route::get(
            '/perfil',
            [JefePerfilController::class, 'index']
        )->name('perfil.index');

    });

/*
|--------------------------------------------------------------------------
| Auditoría
|--------------------------------------------------------------------------
*/

 Route::middleware([
    'auth',
    'password.actualizada',
    'rol:Auditoria',
])
    ->prefix('auditoria')
    ->name('auditoria.')
    ->group(function () {
        Route::get(
            '/dashboard',
            [DashboardAuditoriaController::class,'index']
        )->name('dashboard');

        Route::get(
            '/arqueos',
            [ArqueoAuditoriaController::class, 'index']
        )->name('arqueos.index');

        Route::get(
            '/arqueos/crear',
            [ArqueoAuditoriaController::class, 'create']
        )->name('arqueos.create');

        Route::post(
            '/arqueos',
            [ArqueoAuditoriaController::class, 'store']
        )->name('arqueos.store');

        Route::get(
            '/arqueos/{arqueo}',
            [ArqueoAuditoriaController::class, 'show']
        )->name('arqueos.show');

        Route::get(
            '/arqueos/{arqueo}/imprimir',
            [ArqueoAuditoriaController::class, 'imprimir']
        )->name('arqueos.imprimir');

        Route::get(
            '/reportes',
            [ReporteAuditoriaController::class, 'index']
        )->name('reportes.index');

        Route::get(
            '/perfil',
            [PerfilAuditoriaController::class, 'index']
        )->name('perfil.index');


        Route::get(
            '/reportes/imprimir',
            [ReporteAuditoriaController::class, 'imprimir']
        )->name('reportes.imprimir');

        Route::get(
            '/cumplimientos',
            [CumplimientoAuditoriaController::class, 'index']
        )->name('cumplimientos.index');

        Route::get(
            '/cumplimientos/detalle',
            [CumplimientoAuditoriaController::class, 'detalle']
        )->name('cumplimientos.detalle');





    });

/*
|--------------------------------------------------------------------------
| Gerencia
|--------------------------------------------------------------------------
*/

 Route::middleware([
        'auth',
        'password.actualizada',
        'rol:Gerencia',
    ])
        ->prefix('gerencia')
        ->name('gerencia.')
        ->group(function () {

            Route::get(
                '/dashboard',
                [DashboardGerenciaController::class, 'index']
            )->name('dashboard');

            Route::get(
                '/estado-arqueos',
                [EstadoArqueosGerenciaController::class, 'index']
            )->name('estado-arqueos.index');

            Route::get(
                '/estado-arqueos/detalle',
                [EstadoArqueosGerenciaController::class, 'detalle']
            )->name('estado-arqueos.detalle');

            Route::get(
                '/agentes',
                [AgenteController::class, 'index']
            )->name('agentes.index');

            Route::get(
                '/agentes/{agente}',
                [AgenteController::class, 'show']
            )->name('agentes.show');

            Route::get(
                '/arqueos-agentes',
                [ArqueoAgenteGerenciaController::class, 'index']
            )->name('arqueos-agentes.index');

            Route::get(
                '/arqueos-agentes/{arqueo}',
                [ArqueoAgenteGerenciaController::class, 'show']
            )->name('arqueos-agentes.show');

            Route::get(
                '/arqueos-agentes/{arqueo}/imprimir',
                [ArqueoAgenteGerenciaController::class, 'imprimir']
            )->name('arqueos-agentes.imprimir');


            Route::get(
                '/arqueos-promotores',
                [ArqueoPromotorGerenciaController::class, 'index']
            )->name('arqueos-promotores.index');

            Route::get(
                '/arqueos-promotores/{arqueo}',
                [ArqueoPromotorGerenciaController::class, 'show']
            )->name('arqueos-promotores.show');

            Route::get(
                '/arqueos-promotores/{arqueo}/imprimir',
                [ArqueoPromotorGerenciaController::class, 'imprimir']
            )->name('arqueos-promotores.imprimir');

            Route::get(
                '/agentes-ruta',
                [AgentesPorRutaGerenciaController::class, 'index']
            )->name('agentes-ruta.index');

            Route::get(
                '/agentes-region',
                [AgentesPorRegionGerenciaController::class, 'index']
            )->name('agentes-region.index');

            Route::get(
                '/promotores',
                [PromotorController::class, 'index']
            )->name('promotores.index');

            Route::get(
                '/promotores/{promotor}',
                [PromotorController::class, 'show']
            )->name('promotores.show');

            Route::get(
                '/rutas-promotores',
                [RutasPromotoresGerenciaController::class, 'index']
            )->name('rutas-promotores.index');


            Route::get(
                '/arqueos',
                [ArqueoGeneralController::class, 'index']
            )->name('arqueos.index');

            Route::get(
                '/arqueos/{arqueo}',
                [ArqueoGeneralController::class, 'show']
            )->name('arqueos.show');

            Route::get(
                '/arqueos/{arqueo}/imprimir',
                [ArqueoGeneralController::class, 'imprimir']
            )->name('arqueos.imprimir');


            Route::get(
                '/arqueos-extemporaneos',
                [ArqueoExtemporaneoGerenciaController::class, 'index']
            )->name('arqueos-extemporaneos.index');


            Route::get(
                '/arqueos-anulados',
                [ArqueoAnuladoGerenciaController::class, 'index']
            )->name('arqueos-anulados.index');



            Route::get(
                '/reportes',
                [ReporteGerenciaController::class, 'index']
            )->name('reportes.index');

            Route::get(
                '/reportes/imprimir',
                [ReporteGerenciaController::class, 'imprimir']
            )->name('reportes.imprimir');

            Route::get(
                '/perfil',
                [PerfilGerenciaController::class, 'index']
            )->name('perfil.index');






        });

/*
|--------------------------------------------------------------------------
| Agente
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'password.actualizada',
    'rol:agente',
])
    ->prefix('agente')
    ->name('agente.')
    ->group(function () {
        Route::get(
            '/dashboard',
            [DashboardAgenteController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/arqueos',
            [ArqueoController::class, 'index']
        )->name('arqueos.index');

        Route::get(
            '/arqueos/crear',
            [ArqueoController::class, 'create']
        )->name('arqueos.create');

        Route::post(
            '/arqueos',
            [ArqueoController::class, 'store']
        )->name('arqueos.store');

        Route::get(
            '/arqueos/{arqueo}',
            [ArqueoController::class, 'show']
        )->name('arqueos.show');

        Route::get(
            '/arqueos/{arqueo}/imprimir',
            [ArqueoController::class, 'imprimir']
        )->name('arqueos.imprimir');

        Route::get(
            '/arqueos-promotor',
            [ArqueoPromotorController::class, 'index']
        )->name('arqueos-promotor.index');

        Route::get(
            '/arqueos-promotor/{arqueo}',
            [ArqueoPromotorController::class, 'show']
        )->name('arqueos-promotor.show');

        Route::get(
            '/arqueos-promotor/{arqueo}/imprimir',
            [ArqueoPromotorController::class, 'imprimir']
        )->name('arqueos-promotor.imprimir');

        Route::post(
            '/arqueos-promotor/{arqueo}/firmar',
            [ArqueoPromotorController::class, 'firmar']
        )->name('arqueos-promotor.firmar');


        Route::get(
            '/arqueos-extemporaneos',
            [ArqueoExtemporaneoController::class, 'index']
        )->name('arqueos-extemporaneos.index');

        Route::get(
            '/cumplimientos',
            [CumplimientoArqueoController::class, 'index']
        )->name('cumplimientos.index');

        Route::get(
            '/perfil',
            [PerfilAgenteController::class, 'index']
        )->name('perfil.index');

        Route::put(
            '/perfil',
            [PerfilAgenteController::class, 'actualizar']
        )->name('perfil.actualizar');





    });

