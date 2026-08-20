<?php
// extract-sections.php · Uso: php extract-sections.php > secciones.txt
$src = is_file(__DIR__.'/original.html') ? __DIR__.'/original.html' : __DIR__.'/storage/app/original.html';
if (!is_file($src)) { fwrite(STDERR, "No encuentro original.html\n"); exit(1); }
$html = file_get_contents($src);

echo "========== ARRAYS DE DATOS EN EL JS ==========\n";
preg_match_all('/(?:var|window\.)\s*([A-Z][A-Z_0-9]*)\s*=\s*(\[[\s\S]*?\]|\{[\s\S]*?\});/', $html, $m, PREG_SET_ORDER);
$seen = [];
foreach ($m as $x) {
    if (isset($seen[$x[1]])) continue;
    $seen[$x[1]] = 1;
    echo "\n----- var {$x[1]} = (".strlen($x[2])." chars)\n";
    echo substr($x[2], 0, 700), "\n";
}

echo "\n========== SECCIONES HTML ==========\n";
foreach (['historias', 'nosotros', 'faq', 'operaciones', 'contacto'] as $id) {
    if (preg_match('/<section[^>]*id="'.$id.'"[\s\S]*?<\/section>/', $html, $s)) {
        echo "\n===== SECTION #$id (".strlen($s[1])." chars) =====\n";
        echo (strlen($s[1]) > 6000 ? substr($s[1], 0, 6000)."\n…(truncado)" : $s[1]), "\n";
    }
}

// Bloque de stats (contadores)
if (preg_match('/<[^>]*class="[^"]*stats[^"]*"[\s\S]{0,1200}/', $html, $s)) {
    echo "\n===== STATS =====\n", $s[0], "\n";
}
echo "\nFIN.\n";