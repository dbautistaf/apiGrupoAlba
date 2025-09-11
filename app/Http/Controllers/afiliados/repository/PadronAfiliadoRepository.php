<?php

namespace App\Http\Controllers\Afiliados\Repository;


use App\Http\Controllers\afiliados\dto\AfiliadoDatosPersonalesDTO;
use App\Http\Controllers\afiliados\dto\PersonaAfipDTO;
use App\Models\afiliado\AfiliadoPadronEntity;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PadronAfiliadoRepository
{
    public function findByBuscarAfiliadoAfip($dni)
    {
        if (strlen($dni) == 8) {
            $client = new Client();
            $response = $client->get('http://179.43.125.22/HC/Api/Api/Values/ConsultaCiudadano?dni=' . $dni . '&sexo=');
            $data = json_decode($response->getBody(), true);
            $per = collect($data['responseConsultaCiudadano']);
            $fechaNacimiento = Carbon::parse($per['fechaNacimiento']);
            $edad = $fechaNacimiento->age;

            return new PersonaAfipDTO(
                $per['apellido'] ?? '',
                $per['calle'] ?? '',
                $per['ciudad'] ?? '',
                $per['cpostal'] ?? '',
                $per['cuil'] ?? '',
                $per['departamento'] ?? '',
                $per['fechaNacimiento'] ?? '',
                $per['fechaf'] ?? '',
                $per['municipio'] ?? '',
                $per['nombres'] ?? '',
                $per['numeroDocumento'] ?? '',
                $per['sexo'] ?? '',
                $per['piso'] ?? '',
                $per['provincia'] ?? '',
                $edad
            );
        }
    }

    public function findByListDniLike($dni)
    {
        return AfiliadoPadronEntity::with(['certificado', 'tipoParentesco'])
            //  ->where('cuil_tit', 'LIKE', '%' . $dni . '%')
            ->where(function ($query) use ($dni) {
                $query->where('cuil_tit', 'LIKE', '%' . $dni . '%')
                    ->orWhere('cuil_benef', 'LIKE', '%' . $dni . '%')
                    ->orWhere('dni', 'LIKE', '%' . $dni . '%');
            })
            ->limit(50)
            ->get()
            ->map(function ($per) {
                $edad = 0;
                if (!is_null($per->fe_nac)) {
                    $fechaNacimiento = Carbon::parse($per->fe_nac);
                    $edad = $fechaNacimiento->age;
                }
                return $this->mapperToAfiliado($per, $edad);
            });
    }

    public function findByListCuilLike($search)
    {
        return AfiliadoPadronEntity::with(['certificado', 'tipoParentesco'])
            ->where('cuil_tit', 'LIKE', '%' . $search . '%')
            ->limit(50)
            ->get()
            ->map(function ($per) {
                $edad = 0;
                if (!is_null($per->fe_nac)) {
                    $fechaNacimiento = Carbon::parse($per->fe_nac);
                    $edad = $fechaNacimiento->age;
                }

                return $this->mapperToAfiliado($per, $edad);
            });
    }

    public function findByListApellidosNombresLike($search)
    {
        return AfiliadoPadronEntity::with(['certificado', 'tipoParentesco'])
            ->where('apellidos', 'LIKE', '%' . $search . '%')
            ->orWhere('nombre', 'LIKE', '%' . $search . '%')
            ->limit(50)
            ->get()
            ->map(function ($per) {
                $edad = 0;
                if (!is_null($per->fe_nac)) {
                    $fechaNacimiento = Carbon::parse($per->fe_nac);
                    $edad = $fechaNacimiento->age;
                }
                return $this->mapperToAfiliado($per, $edad);
            });
    }

    public function findByListPaginate($pageTop)
    {
        return AfiliadoPadronEntity::with(['certificado', 'detalleplan', 'detalleplan.TipoPlan', 'tipoParentesco'])
            ->whereRaw('LENGTH(dni) = 8')
            ->limit($pageTop)
            ->get()
            ->map(function ($per) {
                $edad = null;
                if (!is_null($per->fe_nac)) {
                    $fechaNacimiento = Carbon::parse($per->fe_nac);
                    $edad = $fechaNacimiento->age;
                }
                return $this->mapperToAfiliado($per, $edad);
            });
    }

    public function mapperToAfiliado($row, $edad)
    {
        $planes = [];
        foreach ($row->detalleplan as $key) {
            if (isset($key->TipoPlan) && isset($key->TipoPlan->tipo)) {
                $planes[] = [
                    "plan" => $key->TipoPlan->tipo,
                    "id" => $key->TipoPlan->id_tipo_plan
                ];
            }
        }

        return new AfiliadoDatosPersonalesDTO(
            $row->id,
            $row->dni,
            $row->cuil_tit,
            $row->cuil_benef,
            $row->apellidos . ' ' . $row->nombre,
            $row->fe_nac,
            $row->activo,
            $edad,
            $row->certificado ? $row->certificado->certificado : null,
            $row->certificado ? $row->certificado->fecha_vto : null,
            $row->email,
            $row->id_locatorio,
            $planes,
            $row->celular ?? '-',
            $row->domicilio_postal ?? '-',
            $row->tipo_parentesco->parentesco ?? '-',
            $row->id_sexo
        );
    }

    public function findByExistsDni($dni)
    {
        return AfiliadoPadronEntity::where('dni', $dni)->exists();
    }

    public function findByIdAfiliado($id)
    {
        return AfiliadoPadronEntity::find($id);
    }

    public function findByDni($dni)
    {
        return AfiliadoPadronEntity::where('dni', $dni)->first();
    }

    public function findByUpdateFlash($dni, $sexo, $estadoCivil, $fechaNaci, $activo, $parentesco)
    {
        $afi = AfiliadoPadronEntity::where('dni', $dni)->first();
        $afi->id_sexo = $sexo;
        $afi->fe_nac = $fechaNaci;
        $afi->activo = $activo;
        $afi->id_estado_civil = $estadoCivil;
        $afi->id_parentesco = $parentesco;
        $afi->update();
    }

    public function findByCreate($param)
    {
        AfiliadoPadronEntity::create([
            'cuil_tit' => $param->cuil_titular,
            'cuil_benef' => $param->padron_afiliado,
            'dni' => $param->dni,
            'nombre' => $param->nombres,
            'apellidos' => $param->apellido,
            'id_locatorio' => 0,
            'patologia' => 0,
            'medicacion' => 0,
            'activo' => $param->activo,
            'id_estado_civil' => ($param->estado_civil == '99' ? '00' : '0' . $param->estado_civil),
            'id_parentesco' => ($param->id_parentesco == '99' ? $param->id_parentesco : '0' . $param->id_parentesco)
        ]);
    }
}
