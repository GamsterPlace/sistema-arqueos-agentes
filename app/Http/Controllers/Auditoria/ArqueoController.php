<?php

namespace App\Http\Controllers\Auditoria;

use App\Http\Controllers\Controller;
use App\Models\Arqueo;
use App\Models\Usuario;
use App\Services\AuditoriaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ArqueoController extends Controller
{
    private const BILLETES = [
        200,
        100,
        50,
        20,
        10,
        5,
        1,
    ];

    private const MONEDAS = [
        1.00,
        0.50,
        0.25,
        0.10,
        0.05,
    ];

    public function index(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $this->validarAuditoria($usuario);

        $buscar = trim((string) $request->string('buscar'));
        $estado = trim((string) $request->string('estado'));
        $resultado = trim((string) $request->string('resultado'));
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $arqueos = DB::table('arqueos as arq')
            ->where('arq.tipo', 'VISITA_AUDITORIA')
            ->where('arq.creado_por', $usuario->id)
            ->when(
                $buscar !== '',
                function ($query) use ($buscar): void {
                    $query->where(function ($subquery) use ($buscar): void {
                        $subquery
                            ->where(
                                'arq.numero_arqueo',
                                'like',
                                '%' . $buscar . '%'
                            )
                            ->orWhere(
                                'arq.codigo_agente_historico',
                                'like',
                                '%' . $buscar . '%'
                            )
                            ->orWhere(
                                'arq.nombre_negocio_historico',
                                'like',
                                '%' . $buscar . '%'
                            )
                            ->orWhere(
                                'arq.nombre_propietario_historico',
                                'like',
                                '%' . $buscar . '%'
                            )
                            ->orWhere(
                                'arq.ruta_historica',
                                'like',
                                '%' . $buscar . '%'
                            )
                            ->orWhere(
                                'arq.region_historica',
                                'like',
                                '%' . $buscar . '%'
                            );
                    });
                }
            )
            ->when(
                $estado !== '',
                fn ($query) => $query->where(
                    'arq.estado',
                    $estado
                )
            )
            ->when(
                $resultado === 'FALTANTE',
                fn ($query) => $query->where(
                    'arq.diferencia',
                    '<',
                    0
                )
            )
            ->when(
                $resultado === 'SOBRANTE',
                fn ($query) => $query->where(
                    'arq.diferencia',
                    '>',
                    0
                )
            )
            ->when(
                $resultado === 'EXACTO',
                fn ($query) => $query->where(
                    'arq.diferencia',
                    '=',
                    0
                )
            )
            ->when(
                $desde,
                fn ($query) => $query->whereDate(
                    'arq.fecha_arqueo',
                    '>=',
                    $desde
                )
            )
            ->when(
                $hasta,
                fn ($query) => $query->whereDate(
                    'arq.fecha_arqueo',
                    '<=',
                    $hasta
                )
            )
            ->select([
                'arq.id',
                'arq.numero_arqueo',
                'arq.fecha_arqueo',
                'arq.estado',
                'arq.fuera_fecha_ordinaria',
                'arq.codigo_agente_historico',
                'arq.nombre_negocio_historico',
                'arq.ruta_historica',
                'arq.region_historica',
                'arq.total_arqueado',
                'arq.saldo_sistema',
                'arq.diferencia',
            ])
            ->orderByDesc('arq.fecha_arqueo')
            ->orderByDesc('arq.id')
            ->paginate(15)
            ->withQueryString();

        $resumen = (object) [
            'total' => DB::table('arqueos')
                ->where('tipo', 'VISITA_AUDITORIA')
                ->where('creado_por', $usuario->id)
                ->count(),

            'certificados' => DB::table('arqueos')
                ->where('tipo', 'VISITA_AUDITORIA')
                ->where('creado_por', $usuario->id)
                ->where('estado', 'CERTIFICADO')
                ->count(),

            'pendientes' => DB::table('arqueos')
                ->where('tipo', 'VISITA_AUDITORIA')
                ->where('creado_por', $usuario->id)
                ->where('estado', 'PENDIENTE_CERTIFICACION')
                ->count(),

            'anulados' => DB::table('arqueos')
                ->where('tipo', 'VISITA_AUDITORIA')
                ->where('creado_por', $usuario->id)
                ->where('estado', 'ANULADO')
                ->count(),
        ];

        return view(
            'auditoria.arqueos.index',
            compact(
                'arqueos',
                'buscar',
                'estado',
                'resultado',
                'desde',
                'hasta',
                'resumen'
            )
        );
    }

    public function create(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $this->validarAuditoria($usuario);

        $agentes = DB::table('agentes as a')
            ->join('rutas as r', 'r.id', '=', 'a.ruta_id')
            ->join('regiones as reg', 'reg.id', '=', 'r.region_id')
            ->where('a.estado', 'ACTIVO')
            ->where('r.estado', true)
            ->where('reg.estado', true)
            ->orderBy('reg.nombre')
            ->orderBy('r.nombre')
            ->orderBy('a.nombre_negocio')
            ->get([
                'a.id',
                'a.codigo_agente',
                'a.nombre_negocio',
                'a.nombre_propietario',
                'a.direccion',
                'r.codigo as ruta_codigo',
                'r.nombre as ruta_nombre',
                'reg.nombre as region_nombre',
            ]);

        return view(
            'auditoria.arqueos.create',
            [
                'agentes' => $agentes,
                'billetes' => self::BILLETES,
                'monedas' => self::MONEDAS,
            ]
        );
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $this->validarAuditoria($usuario);

        $validated = $request->validate([
            'agente_id' => [
                'required',
                'integer',
                Rule::exists('agentes', 'id'),
            ],
            'saldo_sistema' => [
                'required',
                'numeric',
                'min:0',
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'billetes' => [
                'nullable',
                'array',
            ],
            'billetes.*' => [
                'nullable',
                'integer',
                'min:0',
                'max:1000000',
            ],
            'monedas' => [
                'nullable',
                'array',
            ],
            'monedas.*' => [
                'nullable',
                'integer',
                'min:0',
                'max:1000000',
            ],
        ]);

        $agente = DB::table('agentes as a')
            ->join('rutas as r', 'r.id', '=', 'a.ruta_id')
            ->join('regiones as reg', 'reg.id', '=', 'r.region_id')
            ->where('a.id', $validated['agente_id'])
            ->where('a.estado', 'ACTIVO')
            ->select([
                'a.id',
                'a.codigo_agente',
                'a.nombre_negocio',
                'a.nombre_propietario',
                'a.direccion',
                'r.codigo as ruta_codigo',
                'r.nombre as ruta_nombre',
                'reg.nombre as region_nombre',
            ])
            ->first();

        abort_if(
            ! $agente,
            422,
            'El agente seleccionado no se encuentra activo o no existe.'
        );

        $detalleBilletes = $this->calcularDetalles(
            self::BILLETES,
            $validated['billetes'] ?? [],
            'BILLETE'
        );

        $detalleMonedas = $this->calcularDetalles(
            self::MONEDAS,
            $validated['monedas'] ?? [],
            'MONEDA'
        );

        $totalBilletes = collect($detalleBilletes)
            ->sum('subtotal');

        $totalMonedas = collect($detalleMonedas)
            ->sum('subtotal');

        $totalArqueado = round(
            $totalBilletes + $totalMonedas,
            2
        );

        $saldoSistema = round(
            (float) $validated['saldo_sistema'],
            2
        );

        $diferencia = round(
            $totalArqueado - $saldoSistema,
            2
        );

        $arqueoId = DB::transaction(
            function () use (
                $usuario,
                $validated,
                $agente,
                $detalleBilletes,
                $detalleMonedas,
                $totalBilletes,
                $totalMonedas,
                $totalArqueado,
                $saldoSistema,
                $diferencia
            ): int {
                $arqueoId = DB::table('arqueos')
                    ->insertGetId([
                        'numero_arqueo' => $this->generarNumeroArqueo(),
                        'agente_id' => $agente->id,
                        'creado_por' => $usuario->id,
                        'habilitacion_atrasada_id' => null,
                        'tipo' => 'VISITA_AUDITORIA',
                        'estado' => 'PENDIENTE_CERTIFICACION',
                        'fecha_arqueo' => today(),
                        'hora_inicio' => now(),
                        'hora_fin' => now(),
                        'fuera_fecha_ordinaria' => false,

                        'codigo_agente_historico' =>
                            $agente->codigo_agente,

                        'nombre_negocio_historico' =>
                            $agente->nombre_negocio,

                        'nombre_propietario_historico' =>
                            $agente->nombre_propietario,

                        'direccion_historica' =>
                            $agente->direccion,

                        'ruta_historica' =>
                            trim(
                                $agente->ruta_codigo
                                . ' - '
                                . $agente->ruta_nombre
                            ),

                        'region_historica' =>
                            $agente->region_nombre,

                        'total_billetes' =>
                            $totalBilletes,

                        'total_monedas' =>
                            $totalMonedas,

                        'total_arqueado' =>
                            $totalArqueado,

                        'saldo_sistema' =>
                            $saldoSistema,

                        'diferencia' =>
                            $diferencia,

                        'certificacion' => null,

                        'observaciones' =>
                            $validated['observaciones'] ?? null,

                        'pendiente_certificacion_at' =>
                            now(),

                        'certificado_at' => null,
                        'anulado_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                foreach (
                    array_merge(
                        $detalleBilletes,
                        $detalleMonedas
                    ) as $detalle
                ) {
                    DB::table('arqueo_detalles')
                        ->insert([
                            'arqueo_id' => $arqueoId,
                            'tipo' => $detalle['tipo'],
                            'denominacion' =>
                                $detalle['denominacion'],
                            'cantidad' =>
                                $detalle['cantidad'],
                            'subtotal' =>
                                $detalle['subtotal'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                }

                $arqueoCreado = DB::table('arqueos')
                    ->where('id', $arqueoId)
                    ->first();

                app(AuditoriaService::class)->registrar(
                    usuario: $usuario,
                    modulo: 'Auditoría de Agentes',
                    accion: 'CREAR_ARQUEO_AUDITORIA',
                    tablaAfectada: 'arqueos',
                    registroId: $arqueoId,
                    descripcion:
                        'El usuario de Auditoría registró el arqueo '
                        . ($arqueoCreado->numero_arqueo ?? ('#' . $arqueoId))
                        . ' para el Agente '
                        . $agente->codigo_agente
                        . ' — '
                        . $agente->nombre_negocio
                        . '.',
                    valoresAnteriores: null,
                    valoresNuevos: [
                        'id' => (int) $arqueoId,
                        'numero_arqueo' =>
                            $arqueoCreado->numero_arqueo ?? null,
                        'agente_id' => (int) $agente->id,
                        'creado_por' => (int) $usuario->id,
                        'tipo' => 'VISITA_AUDITORIA',
                        'estado' => 'PENDIENTE_CERTIFICACION',
                        'fecha_arqueo' =>
                            $arqueoCreado->fecha_arqueo ?? today()->format('Y-m-d'),
                        'fuera_fecha_ordinaria' => false,
                        'codigo_agente_historico' =>
                            $agente->codigo_agente,
                        'nombre_negocio_historico' =>
                            $agente->nombre_negocio,
                        'nombre_propietario_historico' =>
                            $agente->nombre_propietario,
                        'direccion_historica' =>
                            $agente->direccion,
                        'ruta_historica' =>
                            trim(
                                $agente->ruta_codigo
                                . ' - '
                                . $agente->ruta_nombre
                            ),
                        'region_historica' =>
                            $agente->region_nombre,
                        'total_billetes' =>
                            (float) $totalBilletes,
                        'total_monedas' =>
                            (float) $totalMonedas,
                        'total_arqueado' =>
                            (float) $totalArqueado,
                        'saldo_sistema' =>
                            (float) $saldoSistema,
                        'diferencia' =>
                            (float) $diferencia,
                        'observaciones' =>
                            $validated['observaciones'] ?? null,
                        'detalle' => array_merge(
                            $detalleBilletes,
                            $detalleMonedas
                        ),
                    ]
                );

                return $arqueoId;
            }
        );

        return redirect()
            ->route(
                'auditoria.arqueos.show',
                $arqueoId
            )
            ->with(
                'success',
                'Arqueo de Auditoría registrado correctamente.'
            );
    }

    public function show(
        Request $request,
        Arqueo $arqueo
    ): View {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $this->validarAuditoria($usuario);

        $this->validarArqueoPropio(
            $arqueo,
            $usuario
        );

        $arqueo->loadMissing([
            'detalles',
            'firmas',
        ]);

        $billetes = $arqueo->detalles
            ->where('tipo', 'BILLETE')
            ->sortByDesc('denominacion')
            ->values();

        $monedas = $arqueo->detalles
            ->where('tipo', 'MONEDA')
            ->sortByDesc('denominacion')
            ->values();

        return view(
            'auditoria.arqueos.show',
            compact(
                'arqueo',
                'billetes',
                'monedas'
            )
        );
    }

    public function imprimir(
        Request $request,
        Arqueo $arqueo
    ): Response {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $this->validarAuditoria($usuario);

        $this->validarArqueoPropio(
            $arqueo,
            $usuario
        );

        $arqueo->loadMissing([
            'detalles',
            'firmas',
        ]);

        $billetes = $arqueo->detalles
            ->where('tipo', 'BILLETE')
            ->sortByDesc('denominacion')
            ->values();

        $monedas = $arqueo->detalles
            ->where('tipo', 'MONEDA')
            ->sortByDesc('denominacion')
            ->values();

        $pdf = Pdf::loadView(
            'auditoria.arqueos.pdf',
            compact(
                'arqueo',
                'billetes',
                'monedas'
            )
        )->setPaper(
            'letter',
            'portrait'
        );

        return $pdf->stream(
            $arqueo->numero_arqueo . '.pdf'
        );
    }

    private function calcularDetalles(
        array $denominaciones,
        array $cantidades,
        string $tipo
    ): array {
        $detalles = [];

        foreach (
            $denominaciones as $denominacion
        ) {
            /*
             * IMPORTANTE:
             * Los inputs de monedas del formulario se envían con dos decimales
             * (1.00, 0.50, 0.25, 0.10, 0.05). Al convertir un float directamente
             * a string, PHP transforma 1.00 en "1", 0.50 en "0.5" y 0.10 en "0.1",
             * por lo que esas cantidades no se encontraban en el request.
             *
             * Para billetes mantenemos la clave entera usada por la vista.
             * Para monedas usamos siempre dos decimales, igual que create.blade.php.
             */
            $clave = $tipo === 'MONEDA'
                ? number_format(
                    (float) $denominacion,
                    2,
                    '.',
                    ''
                )
                : (string) $denominacion;

            $cantidad = max(
                0,
                (int) ($cantidades[$clave] ?? 0)
            );

            $subtotal = round(
                (float) $denominacion * $cantidad,
                2
            );

            $detalles[] = [
                'tipo' => $tipo,
                'denominacion' =>
                    (float) $denominacion,
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
            ];
        }

        return $detalles;
    }

    private function generarNumeroArqueo(): string
    {
        do {
            $numero =
                'AUD-'
                . now()->format('Ymd-His')
                . '-'
                . Str::upper(
                    Str::random(4)
                );
        } while (
            DB::table('arqueos')
                ->where(
                    'numero_arqueo',
                    $numero
                )
                ->exists()
        );

        return $numero;
    }

    private function validarArqueoPropio(
        Arqueo $arqueo,
        Usuario $usuario
    ): void {
        abort_if(
            $arqueo->tipo !== 'VISITA_AUDITORIA'
            || (int) $arqueo->creado_por
                !== (int) $usuario->id,
            404,
            'El arqueo solicitado no pertenece a su historial de Auditoría.'
        );
    }

    private function validarAuditoria(
        Usuario $usuario
    ): void {
        $usuario->loadMissing('rol');

        abort_if(
            ! $usuario->rol
            || $usuario->rol->nombre !== 'Auditoria',
            403,
            'No tiene autorización para acceder a esta sección.'
        );
    }
}
