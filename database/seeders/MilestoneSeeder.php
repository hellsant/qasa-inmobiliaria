<?php

namespace Database\Seeders;

use App\Models\Milestone;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [2012, 'Abrimos cerca del Prado con dos escritorios y una promesa: datos claros.'],
            [2016, 'Operación Nº 100: una casa en Cala Cala que aún visitamos cada enero.'],
            [2020, 'Anticrético registrado en Derechos Reales como estándar de la casa.'],
            [2023, 'Llegamos al valle: Quillacollo, Tiquipaya y Colcapirhua al catálogo.'],
            [2026, 'Mapa verificado, galerías de 6+ fotos y tasaciones online en 24 h.'],
        ];

        foreach ($data as [$year, $description]) {
            Milestone::create(compact('year', 'description'));
        }
    }
}