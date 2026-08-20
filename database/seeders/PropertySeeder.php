<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $img = fn(string $id, int $w = 1200) => "https://images.unsplash.com/{$id}?auto=format&fit=crop&w={$w}&q=70";

        $items = [
            ['Residencia moderna · Zona Norte', 'venta', 'casa', 'Cala Cala', 245000, 'USD', null, 4, 3, 320, 2, true,
                ['1600596542815-ffad4c1539a9', '1600585154340-be6161a56a0c', '1600607687939-ce8a6c25118c', '1600566753190-17f0baa2a6c3', '1600585154526-990dced4db0d', '1512917774080-9991f1c4c750']],
            ['Departamento amoblado a una cuadra del Prado', 'alquiler', 'departamento', 'El Prado', 3500, 'BS', '/mes', 2, 2, 95, 1, true,
                ['1522708323590-d24dbb6b0267', '1502672260266-1c1ef2d93688', '1493809842364-78817add7ffb', '1484154218962-a197022b5858', '1554995207-c18c203602cb', '1560448204-e02f11c3d0e2']],
            ['Casa familiar en Queru Queru', 'venta', 'casa', 'Queru Queru', 158000, 'USD', null, 3, 2, 210, 1, false,
                ['1570129477492-45c003edd2be', '1568605114967-8130f3a36994', '1600585154340-be6161a56a0c', '1600566753190-17f0baa2a6c3', '1600607687939-ce8a6c25118c', '1600585154526-990dced4db0d']],
            ['Anticrético luminoso en El Bosque', 'anticretico', 'departamento', 'El Bosque', 320000, 'BS', null, 3, 2, 140, 1, true,
                ['1613490493576-7fde63acd811', '1613977257363-707ba9348227', '1560448204-e02f11c3d0e2', '1493809842364-78817add7ffb', '1502672260266-1c1ef2d93688', '1522708323590-d24dbb6b0267']],
            ['Penthouse con vista al valle', 'venta', 'penthouse', 'Av. América', 310000, 'USD', null, 4, 4, 380, 2, true,
                ['1512917774080-9991f1c4c750', '1600596542815-ffad4c1539a9', '1600607687939-ce8a6c25118c', '1600585154526-990dced4db0d', '1554995207-c18c203602cb', '1484154218962-a197022b5858']],
            ['Garzonier práctico en Sarco', 'alquiler', 'garzonier', 'Sarco', 2300, 'BS', '/mes', 1, 1, 45, 0, false,
                ['1502672260266-1c1ef2d93688', '1522708323590-d24dbb6b0267', '1493809842364-78817add7ffb', '1484154218962-a197022b5858', '1560448204-e02f11c3d0e2', '1554995207-c18c203602cb']],
            ['Terreno con papeles al día en Quillacollo', 'venta', 'terreno', 'Quillacollo', 48000, 'USD', null, 0, 0, 600, 0, false,
                ['1500382017468-9049fed747ef', '1464226184884-fa280b87c399', '1500530855697-b586d89ba3ee', '1470071459604-3b5ec3a7fe05', '1501785888041-af3ef285b470', '1441974231531-c6227db76b6e']],
            ['Casa en condominio · Tiquipaya', 'venta', 'condominio', 'Tiquipaya', 120000, 'USD', null, 3, 3, 190, 2, false,
                ['1568605114967-8130f3a36994', '1570129477492-45c003edd2be', '1600585154340-be6161a56a0c', '1600566753190-17f0baa2a6c3', '1600596542815-ffad4c1539a9', '1600607687939-ce8a6c25118c']],
        ];

        foreach ($items as [$title, $op, $type, $zoneName, $price, $cur, $suffix, $bed, $bath, $area, $park, $featured, $photos]) {
            $property = Property::create([
                'title' => $title, 'operation' => $op, 'type' => $type,
                'zone_id' => Zone::where('name', $zoneName)->value('id'),
                'price' => $price, 'currency' => $cur, 'price_suffix' => $suffix,
                'bedrooms' => $bed, 'bathrooms' => $bath, 'area_m2' => $area, 'parking' => $park,
                'is_featured' => $featured,
                'description' => 'Propiedad publicada por QASA con galería completa, precio verificable y papeles revisados antes de publicarse. Coordiná tu visita: te mostramos la zona cuadra por cuadra.',
                'features' => ['Cocina equipada', 'Patio / jardín', 'Zona tranquila', 'Todos los servicios', 'Documentación al día'],
            ]);

            foreach ($photos as $i => $id) {
                PropertyImage::create([
                    'property_id' => $property->id,
                    'path' => $img($id),
                    'is_cover' => $i === 0,
                    'sort' => $i,
                ]);
            }
        }
    }
}