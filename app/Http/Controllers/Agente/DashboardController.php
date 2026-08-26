<?php

namespace App\Http\Controllers\Agente;

use App\Http\Controllers\Controller;
use App\Models\Arqueo;
use App\Models\FirmaArqueo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $usuario->loadMissing([
            'agente.ruta.region',
            'datosPersonales',
        ]);

        $agente = $usuario->agente;

        abort_if(
            ! $agente,
            403,
            'La cuenta no tiene un agente asociado.'
        );

        abort_if(
            ! $agente->ruta,
            422,
            'El agente no tiene una ruta asignada.'
        );

        abort_if(
            ! $agente->ruta->region,
            422,
            'La ruta del agente no tiene una región asignada.'
        );

        $arqueoHoy = Arqueo::query()
            ->where('agente_id', $agente->id)
            ->where('tipo', 'DIARIO_AGENTE')
            ->whereDate('fecha_arqueo', today())
            ->where('estado', '!=', 'ANULADO')
            ->latest('id')
            ->first();

        $totalArqueos = Arqueo::query()
            ->where('agente_id', $agente->id)
            ->where('tipo', 'DIARIO_AGENTE')
            ->where('estado', '!=', 'ANULADO')
            ->count();

        $ultimoArqueoAgente = Arqueo::query()
            ->where('agente_id', $agente->id)
            ->where('tipo', 'DIARIO_AGENTE')
            ->latest('fecha_arqueo')
            ->latest('id')
            ->first();

        $ultimoArqueoPromotor = Arqueo::query()
            ->where('agente_id', $agente->id)
            ->where('tipo', 'VISITA_PROMOTOR')
            ->where('estado', '!=', 'ANULADO')
            ->latest('fecha_arqueo')
            ->latest('id')
            ->first();

        $pendientesFirmaPromotor = Arqueo::query()
            ->where('arqueos.agente_id', $agente->id)
            ->where('arqueos.tipo', 'VISITA_PROMOTOR')
            ->where('arqueos.estado', 'PENDIENTE_CERTIFICACION')
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('firmas_arqueos')
                    ->whereColumn(
                        'firmas_arqueos.arqueo_id',
                        'arqueos.id'
                    )
                    ->where(
                        'firmas_arqueos.tipo_firma',
                        'VALIDADOR'
                    )
                    ->where(
                        'firmas_arqueos.valida',
                        true
                    );
            })
            ->count();

        $ultimoArqueoPromotorPendienteFirma = false;

        if ($ultimoArqueoPromotor) {
            $ultimoArqueoPromotorPendienteFirma = ! FirmaArqueo::query()
                ->where('arqueo_id', $ultimoArqueoPromotor->id)
                ->where('tipo_firma', 'VALIDADOR')
                ->where('valida', true)
                ->exists();
        }

        $estadoArqueoHoy = $this->textoEstado(
            $arqueoHoy?->estado
        );

        $descripcionArqueoHoy = match ($arqueoHoy?->estado) {
            'PENDIENTE_CERTIFICACION' =>
                'El arqueo fue enviado y está pendiente de certificación.',

            'CERTIFICADO' =>
                'El arqueo diario ya fue certificado.',

            'BORRADOR' =>
                'Existe un arqueo en borrador.',

            default =>
                'Aún no ha realizado el arqueo diario.',
        };

        return view('dashboards.agente', [
            'agente' => $agente,
            'arqueoHoy' => $arqueoHoy,
            'estadoArqueoHoy' => $estadoArqueoHoy,
            'descripcionArqueoHoy' => $descripcionArqueoHoy,
            'totalArqueos' => $totalArqueos,
            'pendientesFirmaPromotor' => $pendientesFirmaPromotor,
            'ultimoArqueoAgente' => $ultimoArqueoAgente,
            'estadoUltimoArqueoAgente' => $this->textoEstado(
                $ultimoArqueoAgente?->estado
            ),
            'ultimoArqueoPromotor' => $ultimoArqueoPromotor,
            'estadoUltimoArqueoPromotor' => $this->textoEstado(
                $ultimoArqueoPromotor?->estado
            ),
            'ultimoArqueoPromotorPendienteFirma' =>
                $ultimoArqueoPromotorPendienteFirma,
        ]);
    }

    private function textoEstado(?string $estado): string
    {
        return match ($estado) {
            'BORRADOR' => 'Borrador',
            'PENDIENTE_CERTIFICACION' => 'Pendiente',
            'CERTIFICADO' => 'Certificado',
            'ANULADO' => 'Anulado',
            default => 'Pendiente',
        };
    }
}
