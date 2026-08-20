<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Vendimos la casa de mis papás en Queru Queru en seis semanas, al precio que queríamos.', 'Marcela Q.', 'VENTA', 'QUERU QUERU', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=100&q=70'],
            ['Alquilé a una cuadra del Prado sin garante y con contrato digital. Cero estrés.', 'Diego A.', 'ALQUILER', 'EL PRADO', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=70'],
            ['El anticrético me salvó cuando volví del exterior. Todo registrado en Derechos Reales.', 'Rosario M.', 'ANTICRÉTICO', 'CALA CALA', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=100&q=70'],
            ['Nos mostraron tres casas un sábado y el lunes ya teníamos llaves en El Vergel.', 'Familia Pereira', 'VENTA', 'COLQUIRI SUD', 'https://images.unsplash.com/photo-1560250097-0b93563167ca?auto=format&fit=crop&w=100&q=70'],
            ['Tasaron mi terreno en Quillacollo gratis y me explicaron cada paso con paciencia.', 'Andrés C.', 'TASACIÓN', 'QUILLACOLLO', 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=100&q=70'],
        ];

        foreach ($items as $i => [$quote, $author, $op, $loc, $photo]) {
            Testimonial::create(['quote' => $quote, 'author' => $author, 'operation' => $op, 'location' => $loc, 'photo' => $photo, 'sort' => $i]);
        }
    }
}