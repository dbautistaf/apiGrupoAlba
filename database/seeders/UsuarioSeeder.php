<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'cod_usuario' => 1,
            'nombre_apellidos' => 'Usuario Mesa Entrada',
            'documento' => '00000000',
            'telefono' => '999999999',
            'direccion' => 'x',
            'fecha_alta' => '2023-01-01',
            'estado_cuenta' => true,
            'fecha_cambio_clave'=> '2023-01-01',
            'email' => 'mesaentrada@ospf.org.ar',
            'codigo_verificacion' => '02054',
            'password' => bcrypt('admin'),
            'cod_perfil' => 1
        ]);
    }
}
