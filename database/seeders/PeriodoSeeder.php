<?php

namespace Database\Seeders;

use App\Models\PeriodoModelo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeriodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PeriodoModelo::create([
            'id_periodo' => 1,
            'periodo' => '01-2023'
        ]);
    }
}
