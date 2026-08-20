<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallOriginalDesign extends Command
{
    protected $signature = 'landing:install-original {file : Ruta al HTML original}';
    protected $description = 'Instala el diseño original del HTML para que se vea idéntico al EC2';

    public function handle(): int
    {
        $path = $this->argument('file');
        if (!File::exists($path)) {
            $this->error("No encuentro: {$path}");
            return self::FAILURE;
        }

        $html = File::get($path);

        // 1. Extraer CSS
        preg_match('/<style[^>]*>(.*?)<\/style>/s', $html, $css);
        if ($css[1]) {
            File::put(public_path('css/qasa.css'), $css[1]);
            $this->info('✔ CSS original instalado → public/css/qasa.css');
        }

        // 2. Extraer JS (los 3 bloques después del JSON-LD)
        preg_match_all('/<script(?![^>]*src)[^>]*>(.*?)<\/script>/s', $html, $js);
        if (count($js[1]) >= 4) {
            $jsCombined = implode("\n\n", array_slice($js[1], 1));
            File::put(public_path('js/qasa.js'), $jsCombined);
            $this->info('✔ JS original instalado → public/js/qasa.js');
        }

        // 3. Extraer body y convertir a Blade
        preg_match('/<body[^>]*>([\s\S]*)<\/body>/s', $html, $body);
        if ($body[1]) {
            $blade = $body[1];
            $blade = preg_replace('/<style[^>]*>[\s\S]*?<\/style>/', '', $blade);
            $blade = preg_replace('/<script[^>]*>[\s\S]*?<\/script>/', '', $blade);
            $blade = str_replace('@', '@@', $blade);

            // Agregar script de inyección de datos al inicio
            $injectScript = $this->buildInjectScript();
            $blade = $injectScript . $blade;

            File::put(resource_path('views/home.blade.php'), $blade);
            $this->info('✔ Markup Blade instalado → resources/views/home.blade.php');
        }

        $this->newLine();
        $this->line('✅ Listo: la landing se ve idéntica al EC2 y está conectada a tu BD.');
        return self::SUCCESS;
    }

    private function buildInjectScript(): string
    {
        return <<<'BLADE'
@php
use App\Models\Property;
use App\Models\Setting;
use App\Models\Zone;

// Pool de Unsplash (34 fotos del original)
$unsplashPool = [
    "1568605114967-8130f3a36994","1570129477492-45c003edd2be","1564013799919-ab600027ffc6",
    "1600596542815-ffad4c1539a9","1600585154340-be6161a56a0c","1580587771525-78b9dba3b914",
    "1512917774080-9991f1c4c750","1600607687939-ce8a6c25118c","1600566753190-17f0baa2a6c3",
    "1600047509807-ba8f99d2cdde","1600210492486-724fe5c67fb0","1556912167-f556f1f39fdf",
    "1556911220-bff31c812dba","1522708323590-d24dbb6b0267","1502672260266-1c1ef2d93688",
    "1493809842364-78817add7ffb","1484154218962-a197022b5858","1501183638710-841dd1904471",
    "1512292656030-2e8ef0ef4499","1494522855154-9297ac14b55f","1560448204-e02f11c3d0e2",
    "1505843477623-42db2849cb14","1580587771525-78b9dba3b914","1613490493576-7fde63acd811",
    "1613977257363-707ba9348227","1554995207-c18c203602cb","1505843477623-42db2849cb14",
    "1600585154526-990dced4db0d","1556909114-f6e7ad7d3136","1512917774080-9991f1c4c750",
    "1500382017468-9049fed747ef","1464226184884-fa280b87c399","1500530855697-b586d89ba3ee",
    "1470071459604-3b5ec3a7fe05","1501785888041-af3ef285b470","1441974231531-c6227db76b6e"
];

// Mapear zonas de la BD al formato del JS original
$zonas = Zone::where('active', true)->get()->map(function($z) {
    return [
        'g' => $z->group,
        'n' => $z->name,
        'd' => strtoupper($z->name),
        'p' => 'US$ ' . number_format($z->price_m2 ?? 0, 0, ',', '.') . '/m²'
    ];
})->values();

// Mapear propiedades de la BD al formato del JS original
$props = Property::active()->with(['zone', 'images'])->get()->map(function($p, $idx) use ($unsplashPool) {
    $imgs = $p->images->count() > 0
        ? $p->images->map(fn($i) => $i->url)->values()
        : array_slice($unsplashPool, 0, 6);

    return [
        'id' => $p->id,
        'titulo' => $p->title,
        'zona' => $p->zone->name ?? 'Cochabamba',
        'grupo' => $p->zone->group ?? 'centro',
        'cat' => $p->type_label,
        'tipo' => $p->operation,
        'precio' => (float)$p->price,
        'cur' => $p->currency === 'USD' ? '$us' : 'Bs',
        'per' => $p->price_suffix ?? '',
        'hab' => $p->bedrooms,
        'banos' => $p->bathrooms,
        'm2' => (float)$p->area_m2,
        'lat' => (float)($p->lat ?? -17.389),
        'lng' => (float)($p->lng ?? -66.156),
        'dest' => $p->is_featured,
        'imgs' => $imgs,
        'desc' => $p->description ?? '',
        'feats' => $p->features ?? [],
        'anio' => $p->created_at->year
    ];
})->values();

$config = [
    'nombre' => Setting::get('site_name', 'QASA'),
    'wa' => Setting::get('contact_whatsapp', '59170012345'),
    'tel' => Setting::get('contact_phone', '+591 700 12 345'),
    'email' => Setting::get('contact_email', 'hola@qasa.bo'),
    'dir' => Setting::get('contact_address', 'Av. América 1234, Cochabamba')
];
@endphp

<script>
(function(){
  window.U = @json($unsplashPool);
  window.PROPS = @json($props);
  window.ZONAS = @json($zonas);
  window.TIPO_COLOR = {venta:"#b64a26",alquiler:"#3f5c46",anticretico:"#9a742b"};
  window.TIPO_LABEL = {venta:"Venta",alquiler:"Alquiler",anticretico:"Anticrético"};
  window.CONFIG = @json($config);
})();
</script>

BLADE;
    }
}