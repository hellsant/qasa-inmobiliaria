<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['El Prado', 'centro', 890], ['Sarco', 'centro', 540],
            ['Cala Cala', 'norte', 760], ['Queru Queru', 'norte', 720], ['El Bosque', 'norte', 690],
            ['Av. América', 'oeste', 610], ['América Sud', 'sur', 480],
            ['Quillacollo', 'valle', 350], ['Tiquipaya', 'valle', 380], ['Colcapirhua', 'valle', 360],
        ];

        foreach ($zones as [$name, $group, $m2]) {
            Zone::create(['name' => $name, 'group' => $group, 'price_m2' => $m2]);
        }
    }
}