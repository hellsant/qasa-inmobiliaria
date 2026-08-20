<?php
// restore-home.php · Uso: php restore-home.php
$root = __DIR__;
if (!is_file($root.'/original.html')) { fwrite(STDERR, "Falta original.html en la raíz.\n"); exit(1); }
if (strpos(file_get_contents($root.'/public/js/qasa.js'), 'PARCHE FICHA v6') === false) {
    echo "⚠ qasa.js sin parche v6: ejecutá ANTES php rebuild-final.php\n"; exit(1);
}
$html = file_get_contents($root.'/original.html');

/* 0) Quitar style e inline scripts; escapar @ */
$html = preg_replace('/<style[^>]*>.*?<\/style>/s', '', $html);
$html = preg_replace('/<script(?![^>]*src)[^>]*>.*?<\/script>/s', '', $html);
$html = str_replace('@', '@@', $html);
$html = str_replace('<link rel="stylesheet" href="https://unpkg.com/leaflet@@1.9.4/dist/leaflet.css">', '', $html);
$html = str_replace('<script src="https://unpkg.com/leaflet@@1.9.4/dist/leaflet.js"></script>', '', $html);

function R(&$h, $search, $replace) { $c = substr_count($h, $search); $h = str_replace($search, $replace, $h); return $c; }
$rep = [];

/* 1) Bloque PHP de datos al inicio */
$phpHead = <<<'BLADE'
@php
use App\Models\Property;
use App\Models\Setting;
use App\Models\Zone;
use App\Models\Testimonial;
use App\Models\Milestone;
use App\Models\TeamMember;
use App\Models\Faq;

$unsplashPool = ["1568605114967-8130f3a36994","1570129477492-45c003edd2be","1564013799919-ab600027ffc6","1600596542815-ffad4c1539a9","1600585154340-be6161a56a0c","1580587771525-78b9dba3b914","1512917774080-9991f1c4c750","1600607687939-ce8a6c25118c","1600566753190-17f0baa2a6c3","1600047509807-ba8f99d2cdde","1600210492486-724fe5c67fb0","1556912167-f556f1f39fdf","1556911220-bff31c812dba","1522708323590-d24dbb6b0267","1502672260266-1c1ef2d93688","1493809842364-78817add7ffb","1484154218962-a197022b5858","1501183638710-841dd1904471","1560448204-e02f11c3d0e2","1560185127-6ed189bf02f4","1560184897-ae75f418493e","1554995207-c18c203602cb","1584622650111-993a426fbf0a","1552321554-5fefe8c9ef14","1515263487990-61b07816b324","1544005313-94ddf0286df2","1507003211169-0a1dd7228f2d","1573496359142-b8d87734a5a2","1560250097-0b93563167ca","1573497019940-1c2873416488","1519085360753-af0119f7cbe7"];

$zonasJs = Zone::where('active', true)->get()->map(fn($z) => [
    'g' => $z->group, 'n' => $z->name, 'd' => strtoupper($z->name),
    'p' => 'US$ ' . number_format($z->price_m2 ?? 0, 0, ',', '.') . '/m²',
])->values();

$zoneCoords = ['centro'=>[-17.395,-66.158],'norte'=>[-17.376,-66.146],'oeste'=>[-17.377,-66.185],'sur'=>[-17.425,-66.149],'valle'=>[-17.33,-66.22]];
$propsJs = Property::active()->with(['zone', 'images'])->get()->map(fn($p) => [
    'id' => $p->id, 'titulo' => $p->title,
    'zona' => $p->zone->name ?? 'Cochabamba', 'grupo' => $p->zone->group ?? 'centro',
    'cat' => $p->type_label, 'tipo' => $p->operation,
    'precio' => (float) $p->price,
    'cur' => strtoupper($p->currency) === 'USD' ? '$us' : 'Bs',
    'per' => $p->price_suffix ?? '',
    'hab' => (int) $p->bedrooms, 'banos' => (int) $p->bathrooms,
    'm2' => (float) ($p->area_m2 ?? 0),
    'lat' => (float) ($p->lat ?? ($zoneCoords[$p->zone->group ?? 'centro'][0] + (($p->id * 37) % 90 - 45) / 9000)),
    'lng' => (float) ($p->lng ?? ($zoneCoords[$p->zone->group ?? 'centro'][1] + (($p->id * 53) % 90 - 45) / 9000)),
    'dest' => (bool) $p->is_featured,
    'imgs' => $p->images->count() ? $p->images->map(fn($i) => $i->url)->values() : array_slice($unsplashPool, 0, 6),
    'desc' => $p->description ?? '', 'feats' => $p->features ?? [],
    'anio' => (int) $p->created_at->format('Y'),
    'video' => $p->video_url ?? '',
    'social' => ['tiktok' => $p->social_tiktok ?? '', 'instagram' => $p->social_instagram ?? '', 'facebook' => $p->social_facebook ?? '', 'youtube' => $p->social_youtube ?? ''],
])->values();

$testimonials = Testimonial::orderBy('sort')->get();
$milestones   = Milestone::orderBy('year')->get();
$team         = TeamMember::orderBy('sort')->get();
$faqs         = Faq::orderBy('sort')->get();
$postPos      = [[2,6,-4],[5,4,3],[8,2,-2],[9,3,4],[6,9,-3],[3,7,2],[7,5,-3],[4,8,4]];

$videoProps = Property::active()->whereNotNull('video_url')->where('video_url', '!=', '')->with(['zone', 'images'])->get()->map(function ($p) {
    $u = $p->video_url; $kind = 'file'; $src = $u;
    if (str_contains($u, 'youtu.be/') || str_contains($u, 'youtube.com/')) {
        $kind = 'youtube';
        preg_match('/(?:youtu\.be\/|v=|embed\/)([\w-]{11})/', $u, $m);
        $src = 'https://www.youtube.com/embed/' . ($m[1] ?? '') . '?autoplay=1&mute=1&loop=1';
    } elseif (str_contains($u, 'tiktok.com') || str_contains($u, 'instagram.com')) {
        $kind = 'social';
    }
    return ['p' => $p, 'kind' => $kind, 'src' => $src];
});

$configJs = [
    'nombre' => Setting::get('site_name', 'QASA'),
    'wa' => Setting::get('contact_whatsapp', '59170012345'),
    'tel' => Setting::get('contact_phone', '+591 700 12 345'),
    'email' => Setting::get('contact_email', 'hola@qasa.bo'),
    'dir' => Setting::get('contact_address', 'Av. América 1234, Cochabamba'),
    'social_tiktok' => Setting::get('social_tiktok', ''),
    'social_instagram' => Setting::get('social_instagram', ''),
    'social_facebook' => Setting::get('social_facebook', ''),
];
@endphp
BLADE;
$html = $phpHead . "\n" . $html;

/* 2) Script de datos + redes flotantes después de <body> */
$dataScript = <<<'BLADE'
<script>
window.__u=function(v){return v&&v.indexOf("http")===0?v:"https://images.unsplash.com/photo-"+v;};
window.U = @json($unsplashPool);
window.PROPS = @json($propsJs);
window.ZONAS = @json($zonasJs);
window.TIPO_COLOR = {venta:"#b64a26",alquiler:"#3f5c46",anticretico:"#9a742b"};
window.TIPO_LABEL = {venta:"Venta",alquiler:"Alquiler",anticretico:"Anticrético"};
window.CONFIG = @json($configJs);
</script>
BLADE;
$socialStack = <<<'BLADE'
<div class="social-stack">
  @if(Setting::get('social_tiktok'))<a href="{{ Setting::get('social_tiktok') }}" target="_blank" rel="noopener" aria-label="TikTok" title="TikTok"><svg viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg></a>@endif
  @if(Setting::get('social_instagram'))<a href="{{ Setting::get('social_instagram') }}" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram"><svg viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 3.25.15 4.77 1.69 4.92 4.92.06 1.27.07 1.65.07 4.85 0 3.2-.01 3.58-.07 4.85-.15 3.23-1.66 4.77-4.92 4.92-1.27.06-1.64.07-4.85.07-3.2 0-3.58-.01-4.85-.07-3.26-.15-4.77-1.7-4.92-4.92-.06-1.27-.07-1.64-.07-4.85 0-3.2.01-3.58.07-4.85.15-3.23 1.66-4.77 4.92-4.92 1.27-.06 1.65-.07 4.85-.07zM12 0C8.74 0 8.33.01 7.05.07 2.7.27.27 2.69.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.2 4.36 2.62 6.78 6.98 6.98 1.28.06 1.69.07 4.95.07s3.67-.01 4.95-.07c4.36-.2 6.78-2.62 6.98-6.98.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95C21.73 2.69 19.31.27 14.95.07 13.67.01 13.26 0 12 0zm0 5.84A6.16 6.16 0 1 0 18.16 12 6.16 6.16 0 0 0 12 5.84zm0 10.16A4 4 0 1 1 16 12a4 4 0 0 1-4 4zm6.41-11.85a1.44 1.44 0 1 0 1.43 1.44 1.44 1.44 0 0 0-1.43-1.44z"/></svg></a>@endif
  @if(Setting::get('social_facebook'))<a href="{{ Setting::get('social_facebook') }}" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook"><svg viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg></a>@endif
</div>
BLADE;
$pos = strpos($html, '<body'); $end = strpos($html, '>', $pos) + 1;
$html = substr($html, 0, $end) . "\n" . $dataScript . $socialStack . substr($html, $end);

/* 3) Leaflet + assets */
$html = str_replace('</head>', "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css\">\n<link rel=\"stylesheet\" href=\"{{ asset('css/qasa.css') }}?v=10\">\n</head>", $html);
$html = str_replace('</body>', "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js\"></script>\n<script src=\"{{ asset('js/qasa.js') }}?v=10\"></script>\n</body>", $html);

/* 4) Textos -> Settings */
$rep['hero_img']  = R($html, 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1800&q=70', '{{ Setting::get(\'hero_image\', \'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1800&q=70\') }}');
$rep['kicker']    = R($html, '<p class="eyebrow" data-reveal>Inmobiliaria · Cochabamba, Bolivia</p>', '<p class="eyebrow" data-reveal>{{ Setting::get(\'hero_kicker\', \'Inmobiliaria · Cochabamba, Bolivia\') }}</p>');
$rep['hero_t1']   = R($html, '<span class="line-in" style="--i:0">Tu próximo hogar</span>', '<span class="line-in" style="--i:0">{{ Setting::get(\'hero_title_1\', \'Tu próximo hogar\') }}</span>');
$rep['hero_t2']   = R($html, 'ya tiene <span class="it">dirección.</span>', '{{ Setting::get(\'hero_title_2\', \'ya tiene\') }} <span class="it">{{ Setting::get(\'hero_title_3\', \'dirección.\') }}</span>');
$rep['hero_sub']  = R($html, '<p class="sub" data-reveal style="--d:1">Venta, alquiler y anticrético en Cochabamba y el valle. Galerías reales de 6+ fotos, precio claro y ubicación en el mapa.</p>', '<p class="sub" data-reveal style="--d:1">{{ Setting::get(\'hero_text\', \'Venta, alquiler y anticrético en Cochabamba y el valle. Galerías reales de 6+ fotos, precio claro y ubicación en el mapa.\') }}</p>');
$rep['cap1']      = R($html, '<b>Residencia moderna · Zona Norte</b>', '<b>{{ Setting::get(\'hero_caption_title\', \'Residencia moderna · Zona Norte\') }}</b>');
$rep['cap2']      = R($html, '<span>Galería completa en la ficha · tocá para ver destacadas</span>', '<span>{{ Setting::get(\'hero_caption_text\', \'Galería completa en la ficha · tocá para ver destacadas\') }}</span>');
$rep['stat1'] = R($html, 'data-count="14"', 'data-count="{{ Setting::get(\'stat_years\', \'14\') }}"');
$rep['stat2'] = R($html, 'data-count="320"', 'data-count="{{ Setting::get(\'stat_operations\', \'320\') }}"');
$rep['stat3'] = R($html, 'data-count="54"', 'data-count="{{ Setting::get(\'stat_properties\', \'54\') }}"');
$rep['stat4'] = R($html, 'data-count="98"', 'data-count="{{ Setting::get(\'stat_recommend\', \'98\') }}"');
$rep['yrs']   = R($html, '<div class="yrs"><b>14</b>', '<div class="yrs"><b>{{ Setting::get(\'stat_years\', \'14\') }}</b>');
$rep['opA_p'] = R($html, 'DESDE BS 280.000', 'DESDE {{ Setting::get(\'op_anticretico_price\', \'BS 280.000\') }}');
$rep['opA_d'] = R($html, 'Contrato registrado en Derechos Reales, devolución del capital garantizada por escrito y verificación legal del inmueble antes de firmar.', '{{ Setting::get(\'op_anticretico_desc\', \'Contrato registrado en Derechos Reales, devolución del capital garantizada por escrito y verificación legal del inmueble antes de firmar.\') }}');
$rep['opV_p'] = R($html, 'DESDE $US 85.000', 'DESDE {{ Setting::get(\'op_venta_price\', \'$US 85.000\') }}');
$rep['opV_d'] = R($html, 'Tasación sin costo, acompañamiento notarial y opciones con crédito bancario pre-aprobado.', '{{ Setting::get(\'op_venta_desc\', \'Tasación sin costo, acompañamiento notarial y opciones con crédito bancario pre-aprobado.\') }}');
$rep['opL_p'] = R($html, 'DESDE BS 2.300/MES', 'DESDE {{ Setting::get(\'op_alquiler_price\', \'BS 2.300/MES\') }}');
$rep['opL_d'] = R($html, 'Contratos claros en bolivianos o dólares, garantías flexibles e inventario fotográfico firmado.', '{{ Setting::get(\'op_alquiler_desc\', \'Contratos claros en bolivianos o dólares, garantías flexibles e inventario fotográfico firmado.\') }}');
$rep['about'] = R($html, 'QASA nació en 2012 en una oficina chiquita cerca del Prado, con una idea grande: que comprar, alquilar o dar en anticrético en Cochabamba sea tan claro como un apretón de manos.', '{{ Setting::get(\'about_text\', \'QASA nació en 2012 en una oficina chiquita cerca del Prado, con una idea grande: que comprar, alquilar o dar en anticrético en Cochabamba sea tan claro como un apretón de manos.\') }}');
$rep['about2']= R($html, 'Cada ficha con galería completa de fotos, precio verificable, pin en el mapa y papeles revisados antes de publicarse. El 98% de nuestros clientes llega recomendado.', '');
$rep['col1']  = R($html, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1000&q=70', '{{ Setting::get(\'about_image_1\', \'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1000&q=70\') }}');
$rep['col2']  = R($html, 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=700&q=70', '{{ Setting::get(\'about_image_2\', \'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=700&q=70\') }}');
$rep['cta1']  = R($html, '¿Tenés una propiedad?', '{{ Setting::get(\'owner_cta_title\', \'¿Tenés una propiedad?\') }}');
$rep['cta2']  = R($html, 'Publicala con nosotros.', '{{ Setting::get(\'owner_cta_text\', \'Publicala con nosotros.\') }}');
$rep['os_d']  = R($html, 'Tasamos con datos comparables reales de Cochabamba, producimos fotos y video con dron, y filtramos visitantes para que solo entren compradores calificados.', '{{ Setting::get(\'owner_sell_desc\') }}');
$rep['os_s']  = R($html, 'PROMEDIO DE VENTA QASA: 45 DÍAS · 97% DEL PRECIO PEDIDO', '{{ Setting::get(\'owner_sell_stat\', \'PROMEDIO DE VENTA QASA: 45 DÍAS · 97% DEL PRECIO PEDIDO\') }}');
$rep['or_d']  = R($html, 'Verificamos antecedentes y solvencia del inquilino, firmamos contrato con inventario fotográfico y hacemos seguimiento de pagos mes a mes.', '{{ Setting::get(\'owner_rent_desc\') }}');
$rep['or_s']  = R($html, '0 DESALOJOS PERDIDOS EN LOS ÚLTIMOS 5 AÑOS', '{{ Setting::get(\'owner_rent_stat\', \'0 DESALOJOS PERDIDOS EN LOS ÚLTIMOS 5 AÑOS\') }}');
$rep['oa_d']  = R($html, 'Valuamos el capital justo para tu inmueble, buscamos contrapartes solventes y registramos todo en Derechos Reales.', '{{ Setting::get(\'owner_anti_desc\') }}');
$rep['oa_s']  = R($html, '100% DE CONTRATOS REGISTRADOS Y SIN CONFLICTOS', '{{ Setting::get(\'owner_anti_stat\', \'100% DE CONTRATOS REGISTRADOS Y SIN CONFLICTOS\') }}');
$rep['dir']   = R($html, 'Av. América 1234, Edif. Torre QASA', '{{ Setting::get(\'contact_address\', \'Av. América 1234, Edif. Torre QASA\') }}');
$rep['tel']   = R($html, '+591 700 12 345', '{{ Setting::get(\'contact_phone\', \'+591 700 12 345\') }}');
$rep['mail']  = R($html, 'hola@@qasa.bo', '{{ Setting::get(\'contact_email\', \'hola@qasa.bo\') }}');
$rep['hor']   = R($html, 'Lun – Sáb · 9:00 a 19:00', '{{ Setting::get(\'contact_hours\', \'Lun – Sáb · 9:00 a 19:00\') }}');
$rep['wa']    = R($html, 'https://wa.me/59170012345', 'https://wa.me/{{ Setting::get(\'contact_whatsapp\', \'59170012345\') }}');

/* 5) Loops con cirugía de posiciones */
$testiLoop = <<<'BLADE'
@foreach($testimonials as $i => $t)
      <article class="postcard" style="--x:{{ $postPos[$i % count($postPos)][0] }}%;--y:{{ $postPos[$i % count($postPos)][1] }}%;--r:{{ $postPos[$i % count($postPos)][2] }}deg">
        <q>“{{ $t->quote }}”</q>
        <div class="who">
          <img src="{{ $t->photo_url }}" alt="{{ $t->author }}">
          <div><b>{{ $t->author }}</b><span>{{ $t->operation }} · {{ $t->location }}</span></div>
        </div>
      </article>
@endforeach
BLADE;
$s = strpos($html, '<div class="post-board">');
if ($s !== false) {
    $secEnd = strpos($html, '</section>', $s);
    $region = substr($html, $s, $secEnd - $s);
    $wrapClose = strrpos($region, '</div>');
    $boardClose = strrpos(substr($region, 0, $wrapClose), '</div>');
    $html = substr($html, 0, $s + strlen('<div class="post-board">')) . "\n" . $testiLoop . "\n    " . substr($html, $s + $boardClose);
    echo "✔ Historias → loop\n";
}

$hitosLoop = <<<'BLADE'
@foreach($milestones as $i => $m)
      <div class="tl-item" data-reveal style="--d:{{ $i }}">
        <b>{{ $m->year }}</b>
        <p>{{ $m->description }}</p>
      </div>
@endforeach
  </div>
BLADE;
$s = strpos($html, '<div class="timeline">');
$e = strpos($html, '<div class="team-grid">', $s);
if ($s !== false && $e !== false) {
    $html = substr($html, 0, $s + strlen('<div class="timeline">')) . "\n" . $hitosLoop . "\n  " . substr($html, $e);
    echo "✔ Hitos → loop\n";
}

$teamLoop = <<<'BLADE'
@foreach($team as $i => $member)
      <article class="team-card" data-reveal style="--d:{{ $i }}">
        <img src="{{ $member->photo_url }}" alt="">
        <div><b>{{ $member->name }}</b><span>{{ $member->role }}</span></div>
      </article>
@endforeach
BLADE;
$s = strpos($html, '<div class="team-grid">');
if ($s !== false) {
    $secEnd = strpos($html, '</section>', $s);
    $region = substr($html, $s, $secEnd - $s);
    $wrapClose = strrpos($region, '</div>');
    $teamClose = strrpos(substr($region, 0, $wrapClose), '</div>');
    $html = substr($html, 0, $s + strlen('<div class="team-grid">')) . "\n" . $teamLoop . "\n  " . substr($html, $s + $teamClose);
    echo "✔ Equipo → loop\n";
}

$faqLoop = <<<'BLADE'
@foreach($faqs as $i => $faq)
  <div class="faq-item" data-reveal @if($i > 0) style="--d:{{ $i }}" @endif>
    <button class="faq-q" aria-expanded="false">{{ $faq->question }}<span class="pm">+</span></button>
    <div class="faq-a"><p>{{ $faq->answer }}</p></div>
  </div>
@endforeach
BLADE;
$s = strpos($html, '<div class="faq-item"');
if ($s !== false) {
    $secEnd = strpos($html, '</section>', $s);
    $region = substr($html, $s, $secEnd - $s);
    $lastClose = strrpos($region, '</div>');
    $html = substr($html, 0, $s) . $faqLoop . "\n" . substr($html, $s + $lastClose + strlen('</div>'));
    echo "✔ FAQ → loop\n";
}

/* 6) Sección Video tours */
$videosSec = <<<'BLADE'

<section id="videos">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow" data-reveal>✦ Video tours</p>
      <h2 class="sec-title" data-reveal style="--d:1">Recorré las casas <span class="it">sin moverte</span> del sofá.</h2>
    </div>
    <div class="videos-track">
      @foreach($videoProps as $item)
        <div class="video-card" data-reveal>
          @if($item['kind'] === 'file')
            <video src="{{ $item['src'] }}" autoplay muted loop playsinline></video>
          @elseif($item['kind'] === 'youtube')
            <iframe src="{{ $item['src'] }}" title="{{ $item['p']->title }}" allow="autoplay; encrypted-media" allowfullscreen></iframe>
          @else
            <a href="{{ $item['src'] }}" target="_blank" rel="noopener">
              <img src="{{ $item['p']->cover_url }}" alt="{{ $item['p']->title }}">
              <span class="video-social">Ver video ↗</span>
            </a>
          @endif
          <div class="video-cap"><b>{{ $item['p']->title }}</b><span>{{ $item['p']->price_label }}</span></div>
        </div>
      @endforeach
    </div>
  </div>
</section>
BLADE;
$s = strpos($html, 'id="destacados"');
if ($s !== false && strpos($html, 'id="videos"') === false) {
    $e = strpos($html, '</section>', $s) + strlen('</section>');
    $html = substr($html, 0, $e) . $videosSec . substr($html, $e);
    echo "✔ Video tours insertada\n";
}

file_put_contents($root.'/resources/views/home.blade.php', $html);
$ok = count(array_filter($rep, fn($n) => $n > 0));
echo "✔ home.blade.php regenerado ({$ok} textos conectados)\n";
echo "\n✅ php artisan view:clear → Ctrl+Shift+R\n";