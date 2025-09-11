<?php

namespace App\Http\Controllers\Discapacidad;

use App\Http\Controllers\Discapacidad\Repository\DiscapacidadDrEnvioRepository;
use App\Models\Discapacidad\DiscapacidadDrEnvioEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RendicionFondosController extends Controller
{

    public function getExportarRendicionFondos(DiscapacidadDrEnvioRepository $repo, Request $request)
    {
        $contenido = "";
        $data = DiscapacidadDrEnvioEntity::where('periodo_presentacion', str_replace('-', '', $request->periodo))
            ->get();
        foreach ($data as $key) {
            $tesoreria = $repo->findByCuitAndCuilAndNumComprobanteAndCodPracticaAndPeriodoPresentacion(
                $key->cuit_prestador,
                $key->cuil,
                $key->numero_comprobante,
                $key->cod_practica,
                $key->periodo_prestacion
            );

            if (!is_null($tesoreria)) {
                $rendicion = $tesoreria;
                /* $sumImporteAplicado = ($rendicion->importe_transferido + $rendicion->retencion_ganancias + $rendicion->retencion_ingresos_brutos + $rendicion->otras_retenciones);
                $sumImporteSolicitado = ($rendicion->fondos_propios_cuenta_discapacidad + $rendicion->fondos_propios_otra_cuenta + $rendicion->importe_aplicado_sss + $rendicion->importe_reversion + $rendicion->importe_devuelto_cuenta_sss + $rendicion->saldo_no_aplicado + $rendicion->recupero_fondos_propios);
                $sumSubsidiado = ($rendicion->importe_aplicado_sss + $rendicion->importe_reversion + $rendicion->importe_devuelto_cuenta_sss + $rendicion->saldo_no_aplicado + $rendicion->recupero_fondos_propios);
                if ($sumImporteAplicado == $rendicion->importe_aplicado_sss && $sumImporteSolicitado == $key->importe_solicitado && $sumSubsidiado == $key->importe_subsidiado) { */
                //@CLAVE RENDICION
                $contenido .= $key->clave_rendicion . '|';
                //@RNOS
                $contenido .= $key->rnos . '|';
                //@TIPO ARCHIVO
                $contenido .= $key->tipo_archivo . '|';
                //@PERIODO PRESENTACIÓN
                $contenido .= $key->periodo_presentacion . '|';
                //@PERIODO PRESTACION
                $contenido .= $key->periodo_prestacion . '|';
                //@CUIL
                $contenido .= $key->cuil . '|';
                //@CODIGO PRACTICA
                $contenido .= $key->cod_practica . '|';
                //@IMPORTE SUBSIDIADO
                $contenido .= $this->completarConCeros($key->importe_subsidiado, 13) . '|';
                //@IMPORTE SOLICITADO
                $contenido .= $this->completarConCeros($key->importe_solicitado, 13) . '|';
                //@NRO ENVIO AFIP
                $contenido .= $key->numero_envio_afip . '|';
                //------------------------------------------------------------------
                //@CUIT DEL CBU => 11
                $contenido .= $key->cuit_prestador . '|';
                //@CBU => 22
                $contenido .= ($rendicion->cbu == '0' ? $this->completarCeroValorNumerico('', 22) : $this->completarCeroValorNumerico($rendicion->cbu, 22)) . '|';
                //@ORDEN DE PAGO => 20 I
                $contenido .= ($rendicion->orden_pago_1 == 0 ? $this->completarConEspacios('', 20) : $this->completarConEspacios($rendicion->orden_pago_1, 20)) . '|';
                //@ORDEN DE PAGO II => 20
                $contenido .= ($rendicion->orden_pago_2 == 0 ? $this->completarConEspacios('', 20) : $this->completarConEspacios($rendicion->orden_pago_2, 20)) . '|';
                //@FECHA TRANSFERENCIA I => 10
                $contenido .= (!is_null($rendicion->fecha_transferencia_1) ? Carbon::parse($rendicion->fecha_transferencia_1)->format('d/m/Y') : $this->completarConEspacios('', 10)) . '|';
                //@FECHA TRANSFERENCIA II => 10
                $contenido .= (!is_null($rendicion->fecha_transferencia_2) ? Carbon::parse($rendicion->fecha_transferencia_2)->format('d/m/Y') : $this->completarConEspacios('', 10)) . '|';
                //@CHEQUE => 10
                $contenido .= ($rendicion->cheque == 0 ? $this->completarConEspacios('', 10) : $this->completarCeroValorNumerico($rendicion->cheque, 10)) . '|';
                //@IMPORTE TRANSFERIDO
                $contenido .= $this->importeTransferido($rendicion->importe_transferido, $key->importe_solicitado, $key->importe_subsidiado) . '|';
                //@RETENCION DE GANANCIAS
                $contenido .= $this->completarConCeros(str_replace('.', ',', $rendicion->retencion_ganancias), 13) . '|';
                //@RETENCION IIBB
                $contenido .= $this->completarConCeros(str_replace('.', ',', $rendicion->retencion_ingresos_brutos), 13) . '|';
                //@OTRAS RETENCIONES
                $contenido .= $this->completarConCeros(str_replace('.', ',', $rendicion->otras_retenciones), 13) . '|';
                //@IMPORTE APLICADO SSS
                $contenido .= $this->importeAplicadoSS($rendicion->importe_aplicado_sss, $key->importe_solicitado, $key->importe_subsidiado)  . '|';
                //@FONDOS PROPIOS INGRESADOS A LA CUENTA DISCAPACIDAD
                $contenido .= $this->fondosPropiosIngresadoCuentaDisca($rendicion->fondos_propios_cuenta_discapacidad, $key->importe_solicitado, $key->importe_subsidiado) . '|';
                //@FONDOS PROPIOS OTRA CUENTA
                $contenido .= $this->completarConCeros(str_replace('.', ',', $rendicion->fondos_propios_otra_cuenta), 13) . '|';
                //@NRO RECIBO => 8
                $contenido .= ($rendicion->numero_recibo == '0' ? $this->completarConEspacios('', 8) : $this->completarCeroValorNumerico($rendicion->numero_recibo, 8)) . '|';
                //@IMPORTE TRASLADADO (REVERSION)
                $contenido .= $this->completarConCeros(str_replace('.', ',', $rendicion->importe_reversion), 13) . '|';
                //@IMPORTE DEVUELTO CUENTA SSS
                $contenido .= $this->completarConCeros(str_replace('.', ',', $rendicion->importe_devuelto_cuenta_sss), 13) . '|';
                //@SALDO NO APLICADO
                $contenido .= $this->completarConCeros(str_replace('.', ',', $rendicion->saldo_no_aplicado), 13) . '|';
                //@RECUPERO FONDOS PROPIOS
                $contenido .= $this->completarConCeros(str_replace('.', ',', $rendicion->recupero_fondos_propios), 13) . '|';
                //@OBSERVACIONES => 150
                $contenido .= "\n";

                $key->estado_validado_tesoreria = '1';
                $key->update();
                $repo->findByAgregarClaveRendicion($key->clave_rendicion, $rendicion->id_discapacidad_tesoreria);
                //}
            } else {
                $contenido .= $key->clave_rendicion . '|';
                //@RNOS
                $contenido .= $key->rnos . '|';
                //@TIPO ARCHIVO
                $contenido .= $key->tipo_archivo . '|';
                //@PERIODO PRESENTACIÓN
                $contenido .= $key->periodo_presentacion . '|';
                //@PERIODO PRESTACION
                $contenido .= $key->periodo_prestacion . '|';
                //@CUIL
                $contenido .= $key->cuil . '|';
                //@CODIGO PRACTICA
                $contenido .= $key->cod_practica . '|';
                //@IMPORTE SUBSIDIADO
                $contenido .= $this->completarConCeros($key->importe_subsidiado, 13) . '|';
                //@IMPORTE SOLICITADO
                $contenido .= $this->completarConCeros($key->importe_solicitado, 13) . '|';
                //@NRO ENVIO AFIP
                $contenido .= $key->numero_envio_afip . '|';
                //------------------------------------------------------------------
                //@CUIT DEL CBU => 11
                $contenido .= $this->completarCeroValorNumerico($key->cuit_prestador, 11) . '|';
                //@CBU => 22
                $contenido .= $this->completarCeroValorNumerico('', 22) . '|';
                //@ORDEN DE PAGO => 20 I
                $contenido .= $this->completarConEspacios('', 20) . '|';
                //@ORDEN DE PAGO II => 20
                $contenido .= $this->completarConEspacios('', 20) . '|';
                //@FECHA TRANSFERENCIA I => 10
                $contenido .= $this->completarConEspacios('', 10) . '|';
                //@FECHA TRANSFERENCIA II => 10
                $contenido .= $this->completarConEspacios('', 10) . '|';
                //@CHEQUE => 10
                $contenido .= $this->completarConEspacios('', 10) . '|';
                //@IMPORTE TRANSFERIDO
                $contenido .= $this->completarConCeros(str_replace('.', ',', 0), 13) . '|';
                //@RETENCION DE GANANCIAS
                $contenido .= $this->completarConCeros(str_replace('.', ',', 0), 13) . '|';
                //@RETENCION IIBB
                $contenido .= $this->completarConCeros(str_replace('.', ',', 0), 13) . '|';
                //@OTRAS RETENCIONES
                $contenido .= $this->completarConCeros(str_replace('.', ',', 0), 13) . '|';
                //@IMPORTE APLICADO SSS
                $contenido .= $this->completarConCeros(str_replace('.', ',', 0), 13) . '|';
                //@FONDOS PROPIOS INGRESADOS A LA CUENTA DISCAPACIDAD
                $contenido .= $this->completarConCeros(str_replace('.', ',', 0), 13) . '|';
                //@FONDOS PROPIOS OTRA CUENTA
                $contenido .= $this->completarConCeros(str_replace('.', ',', 0), 13) . '|';
                //@NRO RECIBO => 8
                $contenido .= $this->completarConEspacios('', 8) . '|';
                //@IMPORTE TRASLADADO (REVERSION)
                $contenido .= $this->completarConCeros(str_replace('.', ',', 0), 13) . '|';
                //@IMPORTE DEVUELTO CUENTA SSS
                $contenido .= $this->completarConCeros(str_replace('.', ',', 0), 13) . '|';
                //@SALDO NO APLICADO
                $contenido .= $this->completarConCeros(str_replace('.', ',', 0), 13) . '|';
                //@RECUPERO FONDOS PROPIOS
                $contenido .= $this->completarConCeros(str_replace('.', ',', 0), 13) . '|';
                //@OBSERVACIONES => 150
                $contenido .= "\n";
            }
        }

        $nombreArchivo = '107404-' . str_replace('-', '', $request->periodo) . '_DR.DEVOLUCION.txt';

        return response($contenido, 200)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"');
    }

    public function completarConCeros($cadena, $longitud = 12)
    {
        // Verificar si la cadena contiene una coma o punto decimal
        if (strpos($cadena, ',') !== false || strpos($cadena, '.') !== false) {
            // Reemplazar la coma con punto decimal para asegurar formato decimal correcto
            $cadena = str_replace(',', '.', $cadena);
            // Formatear la cadena para tener 2 decimales
            $cadena = number_format(floatval($cadena), 2, ',', '');
            // Rellenar con ceros a la izquierda
            return str_pad($cadena, $longitud, '0', STR_PAD_LEFT);
        } else {
            // Si no tiene decimales, agregar ',00' al final y completar con ceros a la izquierda
            $cadenaConCeros = str_pad($cadena, $longitud - 3, '0', STR_PAD_LEFT) . ',00';
            return $cadenaConCeros;
        }
    }
    public function completarConEspacios($cadena, $longitud)
    {
        $espaciosNecesarios = $longitud - strlen($cadena);
        if ($espaciosNecesarios > 0) {
            $cadena .= str_repeat(' ', $espaciosNecesarios);
        }

        return $cadena;
    }

    public function completarCeroValorNumerico($cadena, $longitud = 12)
    {
        return str_pad($cadena, $longitud, '0', STR_PAD_LEFT);
    }

    public function importeTransferido($montoTranferido, $montoSolicitado, $montoSubsidiado)
    {
        if ($montoSubsidiado > 0 && $montoSubsidiado < $montoSolicitado) {
            return $this->completarConCeros(str_replace('.', ',', $montoSubsidiado), 13);
        } else {
            return $this->completarConCeros(str_replace('.', ',', $montoTranferido), 13);
        }
    }

    public function importeAplicadoSS($montoAplicadoSS, $montoSolicitado, $montoSubsidiado)
    {
        if ($montoSubsidiado > 0 && $montoSubsidiado < $montoSolicitado) {
            return $this->completarConCeros(str_replace('.', ',', $montoSubsidiado), 13);
        } else {
            return  $this->completarConCeros(str_replace('.', ',', $montoAplicadoSS), 13);
        }
    }


    public function fondosPropiosIngresadoCuentaDisca($montoFondosPropios, $montoSolicitado, $montoSubsidiado)
    {
        if ($montoSubsidiado > 0 && $montoSubsidiado < $montoSolicitado) {
            $diferencia = $montoSolicitado - $montoSubsidiado;
            return $this->completarConCeros(str_replace('.', ',', $diferencia), 13);
        } else {
            return $this->completarConCeros(str_replace('.', ',', $montoFondosPropios), 13);
        }
    }
}
