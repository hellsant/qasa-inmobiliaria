<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $team = [
            ['Valeria Anteso', 'Fundadora & Gerente', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=500&q=70'],
            ['Marco Salinas', 'Jefe de Ventas', 'https://images.unsplash.com/photo-1560250097-0b93563167ca?auto=format&fit=crop&w=500&q=70'],
            ['Paola Justiniano', 'Alquileres & Anticréticos', 'https://images.unsplash.com/photo-1573497019940-1c2873416488?auto=format&fit=crop&w=500&q=70'],
            ['Rodrigo Camacho', 'Legal & Notaría', 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=500&q=70'],
        ];

        foreach ($team as $i => [$name, $role, $photo]) {
            TeamMember::create(['name' => $name, 'role' => $role, 'photo' => $photo, 'sort' => $i]);
        }
    }
}