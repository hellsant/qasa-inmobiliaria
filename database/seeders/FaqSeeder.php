<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['¿Qué es el anticrético y cómo funciona?', 'Es una modalidad muy boliviana: entregás un capital como garantía, vivís en la propiedad sin pagar alquiler mensual y, al terminar el contrato, se te devuelve el monto completo. Registramos cada contrato en Derechos Reales y verificamos que el inmueble esté libre de gravámenes.'],
            ['¿Cobran por visitar propiedades?', 'Nunca. Las visitas son gratuitas e ilimitadas. Solo se paga comisión cuando se concreta la operación, y siempre lo sabés por escrito desde el primer día.'],
            ['¿Puedo comprar con crédito bancario?', 'Sí. Trabajamos con los principales bancos del país y te ayudamos con la pre-calificación, el papeleo y los tiempos para que no pierdas la propiedad que te gusta.'],
            ['¿Las fotos son reales?', 'Sí, siempre. Cada propiedad se publica con galería de 6 o más fotos y, cuando corresponde, video con dron. Lo que ves es lo que visitás.'],
            ['¿Cuánto tarda la tasación de mi propiedad?', '24 horas hábiles: un asesor visita tu inmueble, compara operaciones reales de tu zona y te entrega un informe por escrito, sin costo ni compromiso.'],
        ];

        foreach ($faqs as $i => [$q, $a]) {
            Faq::create(['question' => $q, 'answer' => $a, 'sort' => $i]);
        }
    }
}