<?php
// connect-admin-sections.php (v3) · Uso: php connect-admin-sections.php
$viewPath = __DIR__.'/resources/views/home.blade.php';
if (!is_file($viewPath)) { fwrite(STDERR, "Ejecutá antes: php install-original.php\n"); exit(1); }
$html = file_get_contents($viewPath);

/* ===== 1) Agregar consultas al bloque @php de inyección ===== */
$extra = <<<'PHP'
$testimonials = \App\Models\Testimonial::orderBy('sort')->get();
$milestones   = \App\Models\Milestone::orderBy('year')->get();
$team         = \App\Models\TeamMember::orderBy('sort')->get();
$faqs         = \App\Models\Faq::orderBy('sort')->get();
$postPos      = [[2,6,-4],[5,4,3],[8,2,-2],[9,3,4],[6,9,-3],[3,7,2],[7,5,-3],[4,8,4]];

PHP;
if (strpos($html, '$testimonials =') === false) {
    $html = str_replace('$configJs = [', $extra.'$configJs = [', $html, $c);
    echo ($c ? "✔ Consultas (testimonios/hitos/equipo/faq) agregadas al bloque de datos\n" : "✗ No se encontró el bloque de datos\n");
} else {
    echo "· Consultas ya presentes, se salta\n";
}

/* ===== 2) HISTORIAS: .posts-board -> @foreach ===== */
$testiLoop = <<<'BLADE'
@foreach($testimonials as $i => $t)
      <article class="postcard" style="--x:{{ $postPos[$i % count($postPos)][0] }};--y:{{ $postPos[$i % count($postPos)][1] }};--r:{{ $postPos[$i % count($postPos)][2] }}deg">
        <q>“{{ $t->quote }}”</q>
        <div class="wh">
          <img src="{{ $t->photo_url }}" alt="">
          <div><b>{{ $t->author }}</b><span>{{ $t->operation }} · {{ $t->location }}</span></div>
        </div>
      </article>
@endforeach
BLADE;
$html = preg_replace(
    '/(<div class="posts-board">)\s*(?:<article[\s\S]*?<\/article>\s*)+(<\/div>)/',
    '$1'."\n".$testiLoop."\n  ".'$2',
    $html, 1, $c
);
echo ($c ? "✔ Historias conectadas al admin\n" : "✗ No se encontró .posts-board\n");

/* ===== 3) HITOS: .timeline -> @foreach ===== */
$hitosLoop = <<<'BLADE'
@foreach($milestones as $i => $m)
      <div class="tl-item" data-reveal style="--d:{{ $i }}">
        <b>{{ $m->year }}</b>
        <p>{{ $m->description }}</p>
      </div>
@endforeach
BLADE;
$html = preg_replace(
    '/(<div class="timeline">)\s*(?:<div class="tl-item"[\s\S]*?<\/div>\s*)+(<\/div>)/',
    '$1'."\n".$hitosLoop."\n  ".'$2',
    $html, 1, $c
);
echo ($c ? "✔ Hitos conectados al admin\n" : "✗ No se encontró .timeline\n");

/* ===== 4) EQUIPO: .team-grid -> @foreach ===== */
$teamLoop = <<<'BLADE'
@foreach($team as $i => $member)
      <article class="team-card" data-reveal style="--d:{{ $i }}">
        <img src="{{ $member->photo_url }}" alt="">
        <div><b>{{ $member->name }}</b><span>{{ $member->role }}</span></div>
      </article>
@endforeach
BLADE;
$html = preg_replace(
    '/(<div class="team-grid">)\s*(?:<article class="team-card"[\s\S]*?<\/article>\s*)+(<\/div>)/',
    '$1'."\n".$teamLoop."\n  ".'$2',
    $html, 1, $c
);
echo ($c ? "✔ Equipo conectado al admin\n" : "✗ No se encontró .team-grid\n");

/* ===== 5) FAQ: .faq-item -> @foreach ===== */
$faqLoop = <<<'BLADE'
@foreach($faqs as $i => $faq)
  <div class="faq-item" data-reveal @if($i > 0) style="--d:{{ $i }}" @endif>
    <button class="faq-q" aria-expanded="false">{{ $faq->question }}<span class="pm">+</span></button>
    <div class="faq-a"><p>{{ $faq->answer }}</p></div>
  </div>
@endforeach
BLADE;
$html = preg_replace(
    '/(?:<div class="faq-item"[\s\S]*?<\/div>\s*<\/div>\s*)+/',
    $faqLoop."\n",
    $html, 1, $c
);
echo ($c ? "✔ FAQ conectadas al admin\n" : "✗ No se encontraron .faq-item\n");

/* ===== 6) Collage: años en el valle dinámicos ===== */
$html = preg_replace(
    '/(<div class="c3"><b>)14(<\/b>)/',
    '$1{{ Setting::get(\'stat_years\', \'14\') }}$2',
    $html, 1, $c
);
echo ($c ? "✔ Años del collage dinámicos\n" : "· Collage ya dinámico o no encontrado\n");

/* ===== Guardar ===== */
file_put_contents($viewPath, $html);
echo "\n✅ home.blade.php actualizado.\n";
echo "Ahora: php artisan view:clear  →  Ctrl+F5\n";