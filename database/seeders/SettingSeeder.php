<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name'   => 'QASA',
            'hero_kicker' => 'Inmobiliaria · Cochabamba, Bolivia',
            'hero_title'  => 'Tu próximo hogar ya tiene dirección.',
            'hero_text'   => 'Venta, alquiler y anticrético en Cochabamba y el valle. Galerías reales de 6+ fotos, precio claro y ubicación en el mapa.',
            'hero_image'  => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1800&q=70',

            'stat_years'      => '14',
            'stat_operations' => '320',
            'stat_properties' => '54',
            'stat_recommend'  => '98',

            'op_anticretico_desc'  => 'Contrato registrado en Derechos Reales, devolución del capital garantizada por escrito y verificación legal del inmueble antes de firmar.',
            'op_anticretico_price' => 'DESDE BS 280.000',
            'op_anticretico_image' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1600&q=70',
            'op_anticretico_points'=> "Registro en DD.RR.\nCapital protegido\nVerificación legal",
            'op_venta_desc'        => 'Tasación sin costo, acompañamiento notarial y opciones con crédito bancario pre-aprobado.',
            'op_venta_price'       => 'DESDE $US 85.000',
            'op_venta_image'       => 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=70',
            'op_alquiler_desc'     => 'Contratos claros en bolivianos o dólares, garantías flexibles e inventario fotográfico firmado.',
            'op_alquiler_price'    => 'DESDE BS 2.300/MES',
            'op_alquiler_image'    => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=70',

            'about_title'    => 'Somos de acá. Conocemos cada cuadra del valle.',
            'about_text'     => "QASA nació en 2012 en una oficina chiquita cerca del Prado, con una idea grande: que comprar, alquilar o dar en anticrético en Cochabamba sea tan claro como un apretón de manos.\n\nCada ficha con galería completa de fotos, precio verificable, pin en el mapa y papeles revisados antes de publicarse. El 98% de nuestros clientes llega recomendado.",
            'about_image_1'  => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1000&q=70',
            'about_image_2'  => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=700&q=70',

            'owner_cta_title' => '¿Tenés una propiedad? Publicala con nosotros.',
            'owner_cta_text'  => 'Tasación gratuita en 24 h, sesión de fotos profesional y publicación en los principales portales. Vos mostrás las llaves, nosotros hacemos el resto.',

            'owner_sell_title' => 'Vendé al mejor precio, sin dolores de cabeza.',
            'owner_sell_desc'  => 'Tasamos con datos comparables reales de Cochabamba, producimos fotos y video con dron, y filtramos visitantes para que solo entren compradores calificados.',
            'owner_sell_points'=> "Tasación gratuita y por escrito en 24 h\nPublicación en 6 portales + cartera propia\nNegociación profesional y papeleo notarial\nAcompañamiento bancario hasta el desembolso",
            'owner_sell_stat'  => 'PROMEDIO DE VENTA QASA: 45 DÍAS · 97% DEL PRECIO PEDIDO',

            'owner_rent_title' => 'Alquilá tranquilo: nosotros nos preocupamos.',
            'owner_rent_desc'  => 'Verificamos antecedentes y solvencia del inquilino, firmamos contrato con inventario fotográfico y hacemos seguimiento de pagos mes a mes.',
            'owner_rent_points'=> "Selección y verificación de inquilinos\nContrato digital + inventario firmado\nGestión de cobro mensual\nRespuesta legal ante incumplimientos",
            'owner_rent_stat'  => '0 DESALOJOS PERDIDOS EN LOS ÚLTIMOS 5 AÑOS',

            'owner_anti_title' => 'Anticrético seguro, capital protegido.',
            'owner_anti_desc'  => 'Valuamos el capital justo para tu inmueble, buscamos contrapartes solventes y registramos todo en Derechos Reales.',
            'owner_anti_points'=> "Valuación según mercado real\nContrapartes con solvencia verificada\nContrato registrado en Derechos Reales\nAcompañamiento hasta la devolución",
            'owner_anti_stat'  => '100% DE CONTRATOS REGISTRADOS Y SIN CONFLICTOS',

            'contact_address'  => 'Av. América 1234, Edif. Torre QASA · Cochabamba, Bolivia',
            'contact_whatsapp' => '59170012345',
            'contact_phone'    => '(4) 452 1234',
            'contact_email'    => 'hola@qasa.bo',
            'contact_hours'    => 'Lun – Sáb · 9:00 a 19:00',

            'footer_text' => 'Inmobiliaria cochabambina. Compra, venta, alquiler y anticrético con papeles claros desde 2012.',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}