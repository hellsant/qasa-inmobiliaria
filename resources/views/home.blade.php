@php
use App\Models\Property;
use App\Models\Setting;
use App\Models\Zone;
use App\Models\Testimonial;
use App\Models\Milestone;
use App\Models\TeamMember;
use App\Models\Faq;

$U = array (
  0 => '1568605114967-8130f3a36994',
  1 => '1570129477492-45c003edd2be',
  2 => '1564013799919-ab600027ffc6',
  3 => '1600596542815-ffad4c1539a9',
  4 => '1600585154340-be6161a56a0c',
  5 => '1580587771525-78b9dba3b914',
  6 => '1512917774080-9991f1c4c750',
  7 => '1600607687939-ce8a6c25118c',
  8 => '1600566753190-17f0baa2a6c3',
  9 => '1600047509807-ba8f99d2cdde',
  10 => '1600210492486-724fe5c67fb0',
  11 => '1556912167-f556f1f39fdf',
  12 => '1556911220-bff31c812dba',
  13 => '1522708323590-d24dbb6b0267',
  14 => '1502672260266-1c1ef2d93688',
  15 => '1493809842364-78817add7ffb',
  16 => '1484154218962-a197022b5858',
  17 => '1501183638710-841dd1904471',
  18 => '1560448204-e02f11c3d0e2',
  19 => '1560185127-6ed189bf02f4',
  20 => '1560184897-ae75f418493e',
  21 => '1554995207-c18c203602cb',
  22 => '1584622650111-993a426fbf0a',
  23 => '1552321554-5fefe8c9ef14',
  24 => '1515263487990-61b07816b324',
  25 => '1545324418-cc1a3fa10c00',
  26 => '1460317442991-0ec209397118',
  27 => '1449844908441-8829872d2607',
  28 => '1430285561322-7808604715df',
  29 => '1416331108676-a22ccb276e35',
  30 => '1500382017468-9049fed747ef',
  31 => '1500530855697-b586d89ba3ee',
  32 => '1466692476868-aef1dfb1e735',
  33 => '1464822759023-fed622ff2c3b',
);

$zoneCoords = ['centro'=>[-17.395,-66.158],'norte'=>[-17.376,-66.146],'oeste'=>[-17.377,-66.185],'sur'=>[-17.425,-66.149],'valle'=>[-17.33,-66.22]];
$propsJs = Property::active()->with(['zone', 'images'])->get()->map(function ($p) use (&$U, $zoneCoords) {
    $imgs = [];
    foreach ($p->images as $im) {
        $u = $im->url;
        if (preg_match('/photo-([\w-]+)\?/', $u, $mm)) { $key = $mm[1]; }
        elseif (preg_match('#unsplash\.com/([\w-]+)$#', $u, $mm)) { $key = $mm[1]; }
        else { $key = $u; }
        $i = array_search($key, $U, true);
        if ($i === false) { $U[] = $key; $i = array_key_last($U); }
        $imgs[] = $i;
    }
    if (!$imgs) $imgs = range(0, 5);
    return [
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
        'imgs' => $imgs,
        'desc' => $p->description ?? '', 'feats' => $p->features ?? [],
        'anio' => (int) $p->created_at->format('Y'),
        'video' => $p->video_url ?? '',
        'social' => ['tiktok' => $p->social_tiktok ?? '', 'instagram' => $p->social_instagram ?? '', 'facebook' => $p->social_facebook ?? '', 'youtube' => $p->social_youtube ?? ''],
    ];
})->values();

$zonasJs = Zone::where('active', true)->get()->map(fn($z) => [
    'g' => $z->group, 'n' => $z->name, 'd' => strtoupper($z->name),
    'p' => 'US$ ' . number_format($z->price_m2 ?? 0, 0, ',', '.') . '/m²',
])->values();

$testimonials = Testimonial::orderBy('sort')->get();
$milestones   = Milestone::orderBy('year')->get();
$team         = TeamMember::orderBy('sort')->get();
$faqs         = Faq::orderBy('sort')->get();
$postPos      = [[2,6,-4],[5,4,3],[8,2,-2],[9,3,4],[6,9,-3],[3,7,2],[7,5,-3],[4,8,4]];



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
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>QASA — Inmobiliaria en Cochabamba · Venta, Alquiler y Anticrético</title>
<meta name="description" content="QASA Inmobiliaria en Cochabamba: casas y departamentos en venta, alquiler y anticrético. Fotos reales, mapa interactivo y tasación gratuita en 24 h.">
<meta name="theme-color" content="#fbfbfd" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0b0b0d" media="(prefers-color-scheme: dark)">
<meta property="og:title" content="QASA — Inmobiliaria en Cochabamba">
<meta property="og:description" content="Venta, alquiler y anticrético en Cochabamba y el valle. Mapa interactivo, galerías reales y tasación gratuita.">
<meta property="og:type" content="website">
<script type="application/ld+json">
{"@@context":"https://schema.org","@@type":"RealEstateAgent","name":"QASA Inmobiliaria","description":"Compra, venta, alquiler y anticrético en Cochabamba, Bolivia.","address":{"@@type":"PostalAddress","addressLocality":"Cochabamba","addressCountry":"BO"},"telephone":"+59170012345","areaServed":"Cochabamba, Bolivia"}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@@0,500;0,600;0,700;0,800;1,600&family=Instrument+Sans:wght@@400;500;600;700&family=Space+Mono:wght@@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@@1.9.4/dist/leaflet.css">
<style>
:root{
  --bg:#fbfbfd; --card:#fff; --soft:#f5f5f7; --ink:#1d1d1f; --mut:#6e6e73; --line:#d2d2d7;
  --ac:#b64a26; --ac2:#8e3a1e; --green:#3f5c46; --gold:#9a742b;
  --nav-bg:rgba(251,251,253,.75); --nav-link:rgba(29,29,31,.8); --menu-bg:rgba(251,251,253,.97);
  --ease:cubic-bezier(.22,1,.36,1); --r:20px;
  --disp:"Inter Tight",-apple-system,BlinkMacSystemFont,sans-serif;
  --body:"Instrument Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
  --mono:"Space Mono",monospace;
}
:root[data-theme="dark"]{
  --bg:#0b0b0d; --card:#17171a; --soft:#212124; --ink:#f5f5f7; --mut:#9b9ba0; --line:#2d2d31;
  --nav-bg:rgba(11,11,13,.72); --nav-link:rgba(245,245,247,.8); --menu-bg:rgba(11,11,13,.97);
}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{background:var(--bg);color:var(--ink);font-family:var(--body);font-size:17px;line-height:1.6;overflow-x:hidden;-webkit-font-smoothing:antialiased;transition:background .4s ease,color .4s ease}
img{display:block;max-width:100%;background:var(--soft)}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:none;background:none;color:inherit}
input,select,textarea{font-family:inherit;font-size:15px;color:var(--ink)}
::selection{background:var(--ac);color:#fff}
:focus-visible{outline:2px solid var(--ac);outline-offset:3px;border-radius:6px}
.wrap{width:min(1200px,92vw);margin:0 auto}
h1,h2,h3{font-family:var(--disp);letter-spacing:-.03em;line-height:1.04}
.it{font-style:italic;color:var(--ac)}
.eyebrow{font-family:var(--mono);font-size:11px;letter-spacing:.24em;text-transform:uppercase;color:var(--mut);display:flex;align-items:center;gap:14px;justify-content:center}
.eyebrow::before,.eyebrow::after{content:"";width:28px;height:1px;background:var(--ac)}
.sec-title{font-size:clamp(38px,5.6vw,72px);font-weight:700;margin:14px 0 12px}
.skip{position:absolute;left:-9999px;top:12px;z-index:800;background:var(--ink);color:var(--bg);padding:10px 18px;border-radius:8px}
.skip:focus{left:12px}
#toast{position:fixed;left:50%;bottom:28px;transform:translate(-50%,80px);background:rgba(29,29,31,.92);color:#fff;font-size:14px;padding:13px 24px;border-radius:100px;z-index:900;opacity:0;transition:.5s var(--ease);max-width:88vw;text-align:center}
#toast.on{transform:translate(-50%,0);opacity:1}

body.js [data-reveal]{opacity:0;transform:translateY(34px);transition:opacity .9s var(--ease),transform .9s var(--ease);transition-delay:calc(var(--d,0)*90ms)}
body.js [data-reveal].in{opacity:1;transform:none}
body.js .prop-card{opacity:0;transform:translateY(28px) scale(.98);transition:opacity .7s var(--ease),transform .7s var(--ease),box-shadow .4s;transition-delay:calc(var(--d)*55ms)}
.grid.go .prop-card{opacity:1;transform:none}
body.js .lines .line-in{transform:translateY(112%);transition:transform 1s var(--ease);transition-delay:calc(var(--i)*.1s)}
body.loaded .lines .line-in{transform:none}
.lines .line{display:block;overflow:hidden;padding-bottom:.09em;margin-bottom:-.09em}

/* NAV */
.nav{position:fixed;top:0;left:0;right:0;z-index:300;background:var(--nav-bg);backdrop-filter:saturate(180%) blur(20px);-webkit-backdrop-filter:saturate(180%) blur(20px);border-bottom:1px solid var(--line);transition:background .4s ease}
.nav-in{display:flex;align-items:center;gap:22px;height:52px}
.logo{font-family:var(--disp);font-weight:800;font-size:21px;display:flex;align-items:center;gap:7px}
.logo i{width:8px;height:8px;background:var(--ac);border-radius:50%}
.nav-links{display:flex;gap:22px;margin:0 auto;font-size:13px;font-weight:500;color:var(--nav-link)}
.nav-links a:hover{color:var(--ink)}
.nav-acts{display:flex;align-items:center;gap:12px}
.fav-pill{display:flex;align-items:center;gap:6px;font-family:var(--mono);font-size:12px;color:var(--mut)}
.fav-pill svg{width:14px;height:14px;fill:var(--ac)}
.theme-btn{width:34px;height:34px;border-radius:50%;background:var(--soft);font-size:15px;display:grid;place-items:center;transition:.3s var(--ease)}
.theme-btn:hover{transform:scale(1.12)}
.btn{display:inline-flex;align-items:center;gap:8px;background:var(--ac);color:#fff;padding:12px 22px;border-radius:100px;font-weight:600;font-size:14px;transition:.3s var(--ease)}
.btn:hover{background:var(--ac2);transform:scale(1.03)}
.btn.dark{background:var(--ink);color:var(--bg)}
.btn.dark:hover{background:#000;color:#fff}
:root[data-theme="dark"] .btn.dark{background:var(--soft);color:var(--ink)}
.btn.ghost{background:transparent;color:var(--ac);padding:12px 6px}
.btn.ghost:hover{transform:none;text-decoration:underline}
.btn.light{background:#fff;color:#1d1d1f}
.btn.wsp{background:#22a55b}
.btn.wsp:hover{background:#1d9150}
.menu-btn{display:none;width:40px;height:40px;position:relative;z-index:5}
.menu-btn span{position:absolute;left:10px;right:10px;height:1.6px;background:var(--ink);transition:.35s var(--ease)}
.menu-btn span:first-child{top:16px}.menu-btn span:last-child{bottom:16px}
body.menu-open .menu-btn span:first-child{top:19px;transform:rotate(45deg)}
body.menu-open .menu-btn span:last-child{bottom:19px;transform:rotate(-45deg)}
.mobile-menu{position:fixed;inset:0;background:var(--menu-bg);backdrop-filter:blur(24px);z-index:290;display:flex;flex-direction:column;justify-content:center;padding:10vw;clip-path:inset(0 0 100% 0);transition:clip-path .6s var(--ease)}
body.menu-open .mobile-menu{clip-path:inset(0 0 0 0)}
.mobile-menu a{font-family:var(--disp);font-weight:700;font-size:clamp(28px,8vw,46px);padding:12px 0;border-bottom:1px solid var(--line)}

/* HERO */
.hero{padding:120px 0 0;text-align:center}
.hero h1{font-size:clamp(50px,8.6vw,110px);font-weight:800;letter-spacing:-.045em;margin:18px 0 14px}
.hero .sub{font-size:clamp(17px,2vw,22px);color:var(--mut);max-width:660px;margin:0 auto}
.hero-cta{display:flex;gap:22px;justify-content:center;align-items:center;margin:26px 0 8px;flex-wrap:wrap}
.search{margin:30px auto 0;max-width:860px;background:var(--card);border:1px solid var(--line);border-radius:20px;padding:12px;display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:8px;box-shadow:0 20px 60px -30px rgba(0,0,0,.18);text-align:left}
.field{display:flex;flex-direction:column;gap:4px;padding:8px 14px}
.field+.field{border-left:1px solid var(--soft)}
.field label{font-family:var(--mono);font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--mut)}
.field select{border:none;background:transparent;font-weight:600;font-size:14.5px;padding:2px 0;cursor:pointer;color:var(--ink)}
.search .btn{border-radius:14px;justify-content:center}
.hero-media{position:relative;margin-top:44px;height:min(76vh,740px);overflow:hidden}
.hero-media img{width:100%;height:100%;object-fit:cover}
.kb{animation:kb 18s ease-in-out infinite alternate}
@@keyframes kb{from{transform:scale(1.05)}to{transform:scale(1.15) translateY(-2%)}}
.hero-media::after{content:"";position:absolute;inset:auto 0 0 0;height:40%;background:linear-gradient(transparent,rgba(0,0,0,.6));pointer-events:none}
.hero-cap{position:absolute;left:0;right:0;bottom:0;display:flex;justify-content:space-between;align-items:flex-end;gap:16px;padding:34px 4vw;color:#fff;text-align:left;flex-wrap:wrap}
.hero-cap b{font-family:var(--disp);font-size:clamp(20px,2.6vw,30px);font-weight:700;display:block}
.hero-cap span{font-size:14px;opacity:.85}
.hero-cap .mono{font-family:var(--mono);font-size:11px;letter-spacing:.14em;opacity:.8}
.hero-cap button{color:#fff;text-decoration:underline;text-underline-offset:5px;font-size:14px;font-weight:600}
.zone-chips{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;padding:34px 4vw 0}
.zone-chips button{font-size:13px;font-weight:500;background:var(--soft);color:var(--ink);border-radius:100px;padding:9px 18px;transition:.3s var(--ease)}
.zone-chips button:hover{background:var(--ink);color:var(--bg);transform:scale(1.05)}
.stats{display:grid;grid-template-columns:repeat(4,1fr);max-width:1000px;margin:56px auto 0;border-top:1px solid var(--line)}
.stat{padding:28px 18px;border-left:1px solid var(--line)}
.stat:first-child{border-left:none}
.stat b{font-family:var(--disp);font-weight:800;font-size:clamp(30px,3.6vw,48px);display:block}
.stat b em{font-style:normal;color:var(--ac)}
.stat span{font-size:12.5px;color:var(--mut)}

/* LISTADOS */
#propiedades{padding:110px 0 90px}
.sec-head{text-align:center;margin-bottom:36px}
.controls{display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:center;margin-bottom:36px}
.seg{display:flex;background:var(--soft);border-radius:100px;padding:4px;gap:2px;flex-wrap:wrap}
.seg .chip{font-size:13px;font-weight:600;padding:9px 18px;border-radius:100px;color:var(--mut);transition:.3s var(--ease)}
.seg .chip.active{background:var(--card);color:var(--ink);box-shadow:0 2px 10px rgba(0,0,0,.14)}
.controls select,.controls input[type="search"]{padding:11px 16px;border:1px solid var(--line);border-radius:100px;background:var(--card);font-size:13.5px}
.controls input[type="search"]{width:170px}
.count{width:100%;text-align:center;font-family:var(--mono);font-size:12px;color:var(--mut)}
.count b{color:var(--ac)}
.link-btn{font-size:13px;font-weight:600;color:var(--ac)}
.link-btn:hover{text-decoration:underline}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(310px,1fr));gap:24px}
.prop-card{background:var(--card);border-radius:var(--r);overflow:hidden;cursor:pointer;box-shadow:0 2px 14px rgba(0,0,0,.06)}
.prop-card:hover{transform:translateY(-8px)!important;box-shadow:0 24px 60px -18px rgba(0,0,0,.25)}
.prop-media{position:relative;aspect-ratio:4/3;overflow:hidden}
.prop-media img{width:100%;height:100%;object-fit:cover;transition:transform 1s var(--ease)}
.prop-card:hover .prop-media img{transform:scale(1.06)}
.badge{position:absolute;top:14px;left:14px;font-family:var(--mono);font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:#fff;padding:7px 13px;border-radius:100px}
.b-venta{background:rgba(182,74,38,.94)}.b-alquiler{background:rgba(63,92,70,.94)}.b-anticretico{background:rgba(154,116,43,.94)}
.photos-chip{position:absolute;bottom:12px;right:12px;background:rgba(0,0,0,.6);color:#fff;font-family:var(--mono);font-size:10.5px;padding:6px 11px;border-radius:100px}
.fav{position:absolute;top:12px;right:12px;width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.92);display:grid;place-items:center;transition:.3s var(--ease)}
.fav svg{width:16px;height:16px;fill:none;stroke:#1d1d1f;stroke-width:2}
.fav:hover{transform:scale(1.15)}
.fav.on{background:var(--ac)}
.fav.on svg{fill:#fff;stroke:#fff}
.prop-body{padding:20px 22px 22px}
.price-row{display:flex;align-items:baseline;gap:8px}
.price-row strong{font-family:var(--disp);font-size:23px;font-weight:800}
.price-row .per{font-family:var(--mono);font-size:11px;color:var(--mut)}
.prop-body h3{font-size:17.5px;font-weight:600;margin:8px 0 4px}
.loc{display:flex;align-items:center;gap:6px;font-size:13.5px;color:var(--mut)}
.loc svg{width:13px;height:13px;fill:var(--ac);flex:none}
.specs{display:flex;gap:14px;margin-top:14px;padding-top:13px;border-top:1px solid var(--soft);font-family:var(--mono);font-size:11.5px;color:var(--mut);align-items:center;flex-wrap:wrap}
.maplink{margin-left:auto;color:var(--ac);font-family:var(--body);font-weight:600;font-size:12.5px}
.maplink:hover{text-decoration:underline}
.load-wrap{text-align:center;margin-top:36px}
.empty{display:none;text-align:center;padding:70px 20px;border:1px dashed var(--line);border-radius:var(--r)}
.empty.show{display:block}

/* MAPA */
#mapa{padding:0 0 110px;background:var(--soft);transition:background .4s ease}
#mapa .sec-head{padding-top:100px}
.map-bar{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-bottom:26px}
#map,#gmapWrap{height:560px;border-radius:28px;border:1px solid var(--line);box-shadow:0 30px 80px -30px rgba(0,0,0,.25);overflow:hidden;position:relative;background:linear-gradient(160deg,#eef3f2,#e8edf2 55%,#eef0e9)}
#gmapWrap{display:none}
#gmapWrap iframe{width:100%;height:100%;border:0}
#mapa.gmode #map,#mapa.gmode .map-legend,#mapa.gmode #flyChips{display:none}
#mapa.gmode #gmapWrap{display:block}
.contours{position:absolute;inset:0;width:100%;height:100%}
.lpin{position:absolute;transform:translate(-50%,-50%);z-index:5;transition:transform .25s var(--ease)}
.lpin:hover{transform:translate(-50%,-50%) scale(1.12);z-index:9}
.lpin.hot .qpill{box-shadow:0 0 0 8px rgba(182,74,38,.25)}
.qpill{display:inline-block;font-family:var(--mono);font-size:11px;font-weight:700;color:#fff;padding:6px 11px;border-radius:100px;border:1.5px solid #fff;white-space:nowrap;cursor:pointer;filter:drop-shadow(0 6px 14px rgba(0,0,0,.35))}
.lpin .tip{position:absolute;bottom:calc(100% + 10px);left:50%;transform:translate(-50%,8px);background:var(--card);color:var(--ink);border-radius:12px;padding:10px 14px;width:200px;opacity:0;pointer-events:none;transition:.3s var(--ease);box-shadow:0 16px 40px rgba(0,0,0,.2);text-align:left}
.lpin:hover .tip,.lpin:focus-visible .tip,.lpin.hot .tip{opacity:1;transform:translate(-50%,0)}
.tip b{font-family:var(--disp);font-size:14px;font-weight:700;display:block}
.tip small{font-size:11px;color:var(--mut);display:block}
.map-legend{display:flex;gap:20px;justify-content:center;margin-top:18px;font-family:var(--mono);font-size:11px;color:var(--mut);flex-wrap:wrap}
.map-legend i{width:9px;height:9px;border-radius:50%;display:inline-block;margin-right:7px}
.leaflet-popup-content-wrapper{border-radius:16px;background:var(--card);color:var(--ink);font-family:var(--body);box-shadow:0 18px 50px rgba(0,0,0,.25)}
.leaflet-popup-content{margin:16px 18px;font-size:13.5px;line-height:1.55}
.leaflet-popup-tip{background:var(--card)}
.pop-t{font-family:var(--disp);font-weight:700;font-size:15.5px;display:block;margin-bottom:2px}
.pop-p{font-family:var(--disp);font-weight:800;font-size:17px;color:var(--ac)}

/* TILES */
#operaciones{padding:110px 0}
.tiles{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:50px}
.tile{position:relative;border-radius:28px;overflow:hidden;min-height:500px;display:flex;flex-direction:column;align-items:center;text-align:center;padding:52px 30px 0;color:#fff;transition:transform .5s var(--ease)}
.tile:hover{transform:scale(1.012)}
.tile img.bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:-1;transition:transform 1.4s var(--ease)}
.tile:hover img.bg{transform:scale(1.06)}
.tile::before{content:"";position:absolute;inset:0;background:linear-gradient(rgba(0,0,0,.55),rgba(0,0,0,.1) 55%,rgba(0,0,0,.4));z-index:-1}
.tile.wide{grid-column:1/-1;min-height:540px}
.tile h3{font-size:clamp(28px,3.6vw,46px);font-weight:800}
.tile p{max-width:480px;margin:12px auto 18px;font-size:15.5px;opacity:.92}
.tile ul{list-style:none;display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-bottom:20px}
.tile li{font-size:12.5px;font-weight:600;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.25);padding:8px 15px;border-radius:100px}
.tile .meta{font-family:var(--mono);font-size:11px;letter-spacing:.18em;opacity:.9}

/* HORIZONTAL */
#destacados{background:#000;color:#f5f5f7}
.hs-sticky{position:sticky;top:0;height:100vh;display:flex;flex-direction:column;justify-content:center;overflow:hidden}
.hs-head{padding:0 6vw 36px;display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap}
.hs-head .eyebrow{justify-content:flex-start;color:#86868b}
.hs-head h2{font-size:clamp(34px,4.8vw,64px);font-weight:800}
.hs-hint{font-family:var(--mono);font-size:11px;letter-spacing:.2em;color:#86868b}
.hs-track{display:flex;gap:28px;padding:0 6vw;will-change:transform}
.slide{flex:none;width:min(58vw,700px);cursor:pointer}
.slide .ph{border-radius:24px;overflow:hidden;aspect-ratio:16/10;position:relative;background:#111}
.slide img{width:100%;height:100%;object-fit:cover;transition:transform 1.2s var(--ease)}
.slide:hover img{transform:scale(1.05)}
.slide .idx{position:absolute;top:16px;right:18px;font-family:var(--mono);font-size:11px;background:rgba(0,0,0,.55);padding:6px 12px;border-radius:100px}
.slide-cap{display:flex;justify-content:space-between;align-items:center;padding-top:16px;gap:14px;flex-wrap:wrap}
.slide-cap b{font-family:var(--disp);font-size:20px;font-weight:700}
.slide-cap span{font-family:var(--mono);font-size:11.5px;color:#86868b}
.slide.cap-cta{display:grid;place-items:center;border:1px dashed rgba(255,255,255,.25);border-radius:24px;aspect-ratio:16/10;width:min(38vw,460px)}
.slide.cap-cta a{font-family:var(--disp);font-weight:700;font-size:clamp(22px,2.8vw,34px);text-align:center;padding:30px}
.slide.cap-cta a:hover{color:var(--ac)}

/* ZONAS */
#zonas{padding:110px 0;background:var(--soft);transition:background .4s ease}
.zonas-grid{display:grid;grid-template-columns:.9fr 1.1fr;gap:60px;align-items:center}
.zona-list{background:var(--card);border-radius:28px;overflow:hidden;box-shadow:0 20px 60px -25px rgba(0,0,0,.15)}
.zona-row{display:grid;grid-template-columns:1fr auto auto;gap:16px;align-items:baseline;padding:20px 28px;border-bottom:1px solid var(--soft);cursor:pointer;transition:.3s var(--ease)}
.zona-row:last-child{border:none}
.zona-row:hover{background:var(--soft);padding-left:36px}
.zona-row b{font-family:var(--disp);font-size:19px;font-weight:700}
.zona-row small{display:block;font-family:var(--mono);font-size:10px;letter-spacing:.14em;color:var(--mut)}
.zona-row .zp{font-family:var(--mono);font-size:12.5px;color:var(--ac)}
.zona-row .zn{font-family:var(--mono);font-size:11.5px;color:var(--mut)}

/* NOSOTROS */
#nosotros{padding:120px 0}
.nos-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:70px;align-items:center}
.manif{font-size:clamp(30px,3.8vw,52px);font-weight:800;letter-spacing:-.035em;margin:16px 0 22px}
.nos-grid p.txt{color:var(--mut);max-width:520px;margin-bottom:14px}
.val-chips{display:flex;flex-wrap:wrap;gap:10px;margin-top:26px}
.val-chips span{font-size:13px;font-weight:600;background:var(--soft);border-radius:100px;padding:10px 18px;transition:.3s var(--ease)}
.val-chips span:hover{background:var(--ink);color:var(--bg);transform:translateY(-3px)}
.collage{position:relative;height:560px}
.collage .c1,.collage .c2{position:absolute;border-radius:24px;overflow:hidden;box-shadow:0 30px 70px -25px rgba(0,0,0,.35)}
.collage .c1{width:74%;height:76%;top:0;right:0}
.collage .c2{width:52%;height:48%;bottom:0;left:0;border:6px solid var(--card)}
.collage img{width:100%;height:100%;object-fit:cover;transition:transform 1.4s var(--ease)}
.collage .c1:hover img,.collage .c2:hover img{transform:scale(1.07)}
.collage .yrs{position:absolute;top:7%;left:1%;background:var(--ac);color:#fff;border-radius:18px;padding:16px 22px;font-family:var(--disp);transform:rotate(-4deg)}
.collage .yrs b{font-size:34px;font-weight:800;display:block;line-height:1}
.collage .yrs span{font-family:var(--mono);font-size:10px;letter-spacing:.2em}
.timeline{display:grid;grid-template-columns:repeat(5,1fr);gap:26px;margin-top:90px;border-top:1px solid var(--line);padding-top:40px}
.tl-item{position:relative}
.tl-item::before{content:"";position:absolute;top:-46px;left:0;width:11px;height:11px;border-radius:50%;background:var(--ac);box-shadow:0 0 0 5px var(--bg),0 0 0 6px var(--line)}
.tl-item b{font-family:var(--mono);font-size:13px;color:var(--ac)}
.tl-item p{font-size:14px;color:var(--mut);margin-top:8px}
.team-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px;margin-top:80px}
.team-card{border-radius:var(--r);overflow:hidden;background:var(--card);box-shadow:0 2px 14px rgba(0,0,0,.06);transition:.4s var(--ease)}
.team-card:hover{transform:translateY(-8px);box-shadow:0 24px 60px -18px rgba(0,0,0,.2)}
.team-card img{aspect-ratio:4/4.3;object-fit:cover;width:100%;filter:grayscale(1);transition:filter .6s}
.team-card:hover img{filter:grayscale(0)}
.team-card div{padding:16px 18px}
.team-card b{font-family:var(--disp);font-size:17px;font-weight:700;display:block}
.team-card span{font-family:var(--mono);font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:var(--ac)}

/* PROCESO */
#proceso{padding:120px 0 26vh;background:var(--soft);transition:background .4s ease}
.stack{display:flex;flex-direction:column;gap:30px}
.step{position:sticky;top:calc(80px + var(--i)*20px);border-radius:28px;padding:52px 58px;min-height:280px;display:grid;grid-template-columns:auto 1fr;gap:46px;align-items:center;box-shadow:0 -14px 50px -25px rgba(0,0,0,.35)}
.step .n{font-family:var(--disp);font-size:clamp(56px,8vw,104px);font-weight:800;font-style:italic;opacity:.9}
.step h3{font-size:clamp(26px,3vw,40px);font-weight:800;margin-bottom:10px}
.step p{max-width:560px;opacity:.85}
.step.c1{background:var(--card)}.step.c2{background:var(--ink);color:var(--bg)}
.step.c3{background:var(--ac);color:#fff}.step.c4{background:var(--green);color:#fff}

/* TESTIMONIOS */
#historias{padding:120px 0;overflow:hidden}
.post-board{position:relative;height:600px;margin-top:50px}
.postcard{position:absolute;left:var(--x);top:var(--y);width:300px;background:var(--card);border-radius:18px;padding:26px 24px 22px;transform:rotate(var(--r));box-shadow:0 16px 50px -15px rgba(0,0,0,.18);transition:.5s var(--ease)}
.postcard:hover{transform:rotate(0) scale(1.05);z-index:10}
.postcard q{font-family:var(--disp);font-size:16.5px;line-height:1.45;font-weight:600;display:block}
.postcard .who{display:flex;align-items:center;gap:12px;margin-top:18px;padding-top:14px;border-top:1px solid var(--soft)}
.postcard .who img{width:38px;height:38px;border-radius:50%;object-fit:cover}
.postcard .who b{font-size:14px;display:block}
.postcard .who span{font-family:var(--mono);font-size:10px;color:var(--mut)}

/* BANDA PUBLICAR */
.cta-band{background:var(--ac);color:#fff;padding:90px 0;text-align:center}
.cta-band h2{font-size:clamp(34px,5vw,64px);font-weight:800}
.cta-band p{max-width:560px;margin:16px auto 30px;font-size:17px;opacity:.92}
.cta-band .row{display:flex;gap:14px;justify-content:center;flex-wrap:wrap}
.cta-band .mini{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:26px;font-family:var(--mono);font-size:11px;letter-spacing:.12em}
.cta-band .mini span{background:rgba(255,255,255,.14);padding:8px 16px;border-radius:100px}

/* PROPIETARIOS */
#propietarios{background:#000;color:#f5f5f7;padding:120px 0}
#propietarios .eyebrow{color:#86868b}
.owner-tabs{display:flex;gap:10px;margin:36px 0 34px;flex-wrap:wrap;justify-content:center}
.owner-tab{font-size:13.5px;font-weight:600;padding:13px 26px;border-radius:100px;border:1px solid rgba(255,255,255,.25);color:rgba(255,255,255,.7);transition:.35s var(--ease)}
.owner-tab:hover{border-color:#fff;color:#fff}
.owner-tab.active{background:#fff;border-color:#fff;color:#1d1d1f;transform:scale(1.05)}
.owner-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:60px;align-items:start;text-align:left}
.owner-panel{display:none}
.owner-panel.active{display:block;animation:fadeUp .55s var(--ease)}
@@keyframes fadeUp{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:none}}
.owner-panel h3{font-size:clamp(26px,3vw,40px);font-weight:800;margin-bottom:14px}
.owner-panel h3 em{font-style:italic;color:#f0a184}
.owner-panel p{color:#a1a1a6;max-width:520px}
.owner-panel ul{list-style:none;margin:24px 0;display:grid;gap:12px}
.owner-panel li{display:flex;gap:12px;font-size:15.5px;color:rgba(255,255,255,.88)}
.owner-panel li::before{content:"✦";color:var(--ac)}
.owner-stat{font-family:var(--mono);font-size:11.5px;letter-spacing:.1em;color:#f0a184;border:1px dashed rgba(255,255,255,.3);border-radius:14px;padding:14px 18px;display:inline-block}
.assure{display:flex;gap:12px;flex-wrap:wrap;margin-top:28px}
.assure span{font-size:12.5px;font-weight:600;background:rgba(255,255,255,.08);border-radius:100px;padding:9px 16px;color:rgba(255,255,255,.75)}
form.owner-form{background:var(--card);color:var(--ink);border-radius:28px;padding:36px;display:grid;gap:16px;box-shadow:0 40px 100px -30px rgba(0,0,0,.7)}
form.owner-form h3{font-size:24px;font-weight:800}
.f-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.f-field{display:flex;flex-direction:column;gap:7px}
.f-field label{font-family:var(--mono);font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--mut)}
.f-field input,.f-field select,.f-field textarea{border:1px solid var(--line);border-radius:12px;padding:12px 14px;background:var(--soft);transition:.3s}
.f-field textarea{resize:vertical;min-height:84px}
.f-field input:focus,.f-field select:focus,.f-field textarea:focus{outline:none;border-color:var(--ac);background:var(--card)}
form .btn{justify-content:center;padding:15px}
form .btn.sent{background:var(--green)}

/* FAQ */
#faq{padding:110px 0;width:min(840px,92vw);margin:0 auto}
.faq-item{border-bottom:1px solid var(--line)}
.faq-q{width:100%;display:flex;justify-content:space-between;align-items:center;gap:20px;padding:24px 4px;text-align:left;font-family:var(--disp);font-size:clamp(18px,2.4vw,24px);font-weight:700}
.faq-q:hover{color:var(--ac)}
.faq-q .pm{flex:none;width:32px;height:32px;border-radius:50%;background:var(--soft);display:grid;place-items:center;font-size:17px;transition:.4s var(--ease)}
.faq-item.open .pm{transform:rotate(45deg);background:var(--ac);color:#fff}
.faq-a{max-height:0;overflow:hidden;transition:max-height .55s var(--ease)}
.faq-a p{padding:0 44px 26px 4px;color:var(--mut);max-width:680px}

/* CONTACTO */
#contacto{background:var(--soft);padding:120px 0;transition:background .4s ease}
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:70px;align-items:start}
.info-list{margin-top:30px}
.info-item{display:grid;grid-template-columns:100px 1fr;gap:16px;padding:16px 0;border-bottom:1px solid var(--line);font-size:15px}
.info-item span{font-family:var(--mono);font-size:10.5px;letter-spacing:.16em;text-transform:uppercase;color:var(--mut);padding-top:3px}
.info-item a:hover{color:var(--ac)}
form.ficha{background:var(--card);border-radius:28px;padding:38px;display:grid;gap:18px;box-shadow:0 20px 60px -25px rgba(0,0,0,.15)}

/* FOOTER */
footer{background:var(--soft);padding:40px 0 60px;border-top:1px solid var(--line)}
.f-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:44px;padding-bottom:40px}
.f-grid h4{font-size:12px;font-weight:700;margin-bottom:14px}
.f-grid a{display:block;padding:4px 0;font-size:13px;color:var(--mut)}
.f-grid a:hover{color:var(--ink)}
.f-brand p{color:var(--mut);font-size:13px;max-width:300px;margin-top:12px}
.f-bottom{display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap;border-top:1px solid var(--line);padding-top:22px;font-size:12px;color:var(--mut)}
.f-bottom .mono{font-family:var(--mono);font-size:10.5px}
#toTop{color:var(--ac);font-weight:600;font-size:12.5px}
.wapp{position:fixed;right:22px;bottom:22px;z-index:310;width:56px;height:56px;border-radius:50%;background:#22a55b;display:grid;place-items:center;box-shadow:0 14px 35px -8px rgba(34,165,91,.6);transition:.35s var(--ease)}
.wapp:hover{transform:scale(1.1) rotate(6deg)}
.wapp svg{width:27px;height:27px;fill:#fff}

/* MODAL + GALERÍA */
.modal{position:fixed;inset:0;z-index:500;display:grid;place-items:center;padding:4vh 4vw}
.modal[hidden]{display:none}
.modal-back{position:absolute;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(10px);opacity:0;transition:.4s}
.modal-panel{position:relative;background:var(--card);border-radius:28px;width:min(1020px,100%);max-height:92vh;overflow:auto;display:grid;grid-template-columns:1.05fr .95fr;opacity:0;transform:translateY(36px) scale(.97);transition:.55s var(--ease)}
.modal.open .modal-back{opacity:1}
.modal.open .modal-panel{opacity:1;transform:none}
.gal{position:relative;background:#000;min-height:440px;display:flex;flex-direction:column}
.gal-main{position:relative;flex:1;overflow:hidden;min-height:320px}
.gal-main img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0}
.gal-nav{position:absolute;top:50%;transform:translateY(-50%);width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.92);display:grid;place-items:center;font-size:20px;z-index:3;transition:.25s;color:#1d1d1f}
.gal-nav:hover{transform:translateY(-50%) scale(1.12)}
.gal-nav.prev{left:14px}.gal-nav.next{right:14px}
.gal-count{position:absolute;top:14px;right:14px;background:rgba(0,0,0,.6);color:#fff;font-family:var(--mono);font-size:11px;padding:6px 12px;border-radius:100px;z-index:3}
.gal-thumbs{display:flex;gap:8px;padding:10px;background:#000;overflow-x:auto;scrollbar-width:none}
.gal-thumbs::-webkit-scrollbar{display:none}
.gal-thumbs img{width:64px;height:46px;object-fit:cover;border-radius:8px;opacity:.45;cursor:pointer;transition:.3s;flex:none;border:2px solid transparent}
.gal-thumbs img.on{opacity:1;border-color:var(--ac)}
.modal-info{padding:34px 32px;display:flex;flex-direction:column;gap:6px}
.modal-info .badge{position:static;align-self:flex-start}
.modal-info h3{font-size:25px;font-weight:800;margin:12px 0 4px}
.modal-info .desc{color:var(--mut);font-size:14.5px;margin:12px 0}
.modal-info .specs{border:none;padding:0;margin:4px 0 10px}
.feats{display:flex;flex-wrap:wrap;gap:8px;margin:8px 0 18px}
.feats span{font-size:12px;font-weight:600;background:var(--soft);border-radius:100px;padding:7px 14px;color:var(--mut)}
.modal-cta{margin-top:auto;display:flex;gap:14px;flex-wrap:wrap}
.modal-close{position:absolute;top:14px;right:14px;width:40px;height:40px;border-radius:50%;background:var(--card);display:grid;place-items:center;font-size:18px;z-index:6;transition:.3s;box-shadow:0 6px 20px rgba(0,0,0,.25)}
.modal-close:hover{background:var(--ac);color:#fff;transform:rotate(90deg)}
.modal-coords{font-family:var(--mono);font-size:10.5px;color:var(--mut);letter-spacing:.08em;margin-top:14px}

@@media(max-width:1080px){
  .zonas-grid,.nos-grid,.owner-grid,.contact-grid{grid-template-columns:1fr}
  .tiles{grid-template-columns:1fr}
  .timeline{grid-template-columns:repeat(2,1fr);gap:44px 26px}
  .team-grid{grid-template-columns:repeat(2,1fr)}
  .collage{height:480px}
}
@@media(max-width:900px){
  .nav-links{display:none}
  .menu-btn{display:block}
  .search{grid-template-columns:1fr 1fr}
  .search .btn{grid-column:1/-1}
  .stats{grid-template-columns:1fr 1fr}
  .stat:nth-child(odd){border-left:none}
  .slide{width:84vw}
  .post-board{height:auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:24px}
  .postcard{position:static;width:auto;transform:rotate(calc(var(--r)/2))}
  .modal-panel{grid-template-columns:1fr}
  .gal{min-height:300px}
  .step{grid-template-columns:1fr;gap:12px;padding:38px 30px;min-height:0}
  .f-grid{grid-template-columns:1fr 1fr}
  #map,#gmapWrap{height:440px}
}
@@media(max-width:640px){
  .search{grid-template-columns:1fr}
  .field+.field{border-left:none;border-top:1px solid var(--soft)}
  .f-row{grid-template-columns:1fr}
  .controls{flex-direction:column}
  .controls input[type="search"]{width:100%}
  .team-grid,.timeline{grid-template-columns:1fr}
}
body:not(.js) .hs-track{overflow-x:auto}
@@media(prefers-reduced-motion:reduce){
  html{scroll-behavior:auto}
  *,*::before,*::after{animation-duration:.001s!important;animation-iteration-count:1!important;transition-duration:.15s!important}
  .hs-track{overflow-x:auto;transform:none!important}
  .hs-sticky{position:static;height:auto;padding:90px 0}
}
</style>
<link rel="stylesheet" href="{{ asset('css/qasa.css') }}?v=11">
</head>
<body>
<script>window.__u=function(v){return v&&v.indexOf("http")===0?v:"https://images.unsplash.com/photo-"+v;};</script><div class="social-stack">
  @if(Setting::get('social_tiktok'))<a href="{{ Setting::get('social_tiktok') }}" target="_blank" rel="noopener" aria-label="TikTok" title="TikTok"><svg viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg></a>@endif
  @if(Setting::get('social_instagram'))<a href="{{ Setting::get('social_instagram') }}" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram"><svg viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 3.25.15 4.77 1.69 4.92 4.92.06 1.27.07 1.65.07 4.85 0 3.2-.01 3.58-.07 4.85-.15 3.23-1.66 4.77-4.92 4.92-1.27.06-1.64.07-4.85.07-3.2 0-3.58-.01-4.85-.07-3.26-.15-4.77-1.7-4.92-4.92-.06-1.27-.07-1.64-.07-4.85 0-3.2.01-3.58.07-4.85.15-3.23 1.66-4.77 4.92-4.92 1.27-.06 1.65-.07 4.85-.07zM12 0C8.74 0 8.33.01 7.05.07 2.7.27.27 2.69.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.2 4.36 2.62 6.78 6.98 6.98 1.28.06 1.69.07 4.95.07s3.67-.01 4.95-.07c4.36-.2 6.78-2.62 6.98-6.98.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95C21.73 2.69 19.31.27 14.95.07 13.67.01 13.26 0 12 0zm0 5.84A6.16 6.16 0 1 0 18.16 12 6.16 6.16 0 0 0 12 5.84zm0 10.16A4 4 0 1 1 16 12a4 4 0 0 1-4 4zm6.41-11.85a1.44 1.44 0 1 0 1.43 1.44 1.44 1.44 0 0 0-1.43-1.44z"/></svg></a>@endif
  @if(Setting::get('social_facebook'))<a href="{{ Setting::get('social_facebook') }}" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook"><svg viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg></a>@endif
</div>
<a class="skip" href="#propiedades">Ir a propiedades</a>
<div id="toast" role="status"></div>

<header class="nav">
  <div class="wrap nav-in">
    <a class="logo" href="#top"><i></i>QASA</a>
    <nav class="nav-links" aria-label="Principal">
      <a href="#propiedades">Propiedades</a><a href="#mapa">Mapa</a><a href="#operaciones">Operaciones</a><a href="#nosotros">Nosotros</a><a href="#propietarios">Propietarios</a><a href="#contacto">Contacto</a>
    </nav>
    <div class="nav-acts">
      <button class="theme-btn" id="themeBtn" aria-label="Cambiar tema claro/oscuro">☾</button>
      <span class="fav-pill" title="Guardadas"><svg viewBox="0 0 24 24"><path d="M12 21s-7.5-4.9-9.5-9.2C1 8.4 3 5 6.4 5c2 0 3.6 1.1 4.6 2.7l1 1.6 1-1.6c1-1.6 2.6-2.7 4.6-2.7C21 5 23 8.4 21.5 11.8 19.5 16.1 12 21 12 21z"/></svg><b id="favCount">0</b></span>
      <a class="btn" href="#contacto" style="padding:9px 18px">Agendar visita</a>
      <button class="menu-btn" id="menuBtn" aria-label="Menú" aria-expanded="false"><span></span><span></span></button>
    </div>
  </div>
</header>
<nav class="mobile-menu" aria-label="Menú móvil">
  <a href="#propiedades">Propiedades</a><a href="#mapa">Mapa</a><a href="#operaciones">Operaciones</a><a href="#nosotros">Nosotros</a><a href="#propietarios">Propietarios</a><a href="#contacto">Contacto</a>
</nav>

<!-- HERO -->
<section class="hero" id="top">
  <div class="wrap">
    <p class="eyebrow" data-reveal>{{ Setting::get('hero_kicker', 'Inmobiliaria · Cochabamba, Bolivia') }}</p>
    <h1 class="lines">
      <span class="line"><span class="line-in" style="--i:0">{{ Setting::get('hero_title_1', 'Tu próximo hogar') }}</span></span>
      <span class="line"><span class="line-in" style="--i:1">{{ Setting::get('hero_title_2', 'ya tiene') }} <span class="it">{{ Setting::get('hero_title_3', 'dirección.') }}</span></span></span>
    </h1>
    <p class="sub" data-reveal style="--d:1">{{ Setting::get('hero_text', 'Venta, alquiler y anticrético en Cochabamba y el valle. Galerías reales de 6+ fotos, precio claro y ubicación en el mapa.') }}</p>
    <div class="hero-cta" data-reveal style="--d:2">
      <a class="btn dark" href="#propiedades">Explorar propiedades</a>
      <a class="btn ghost" href="#propietarios">Soy propietario ›</a>
    </div>
    <form class="search" id="searchForm" data-reveal style="--d:3" aria-label="Buscador">
      <div class="field"><label for="sOp">Operación</label>
        <select id="sOp"><option value="todos">Todas</option><option value="venta">Venta</option><option value="alquiler">Alquiler</option><option value="anticretico">Anticrético</option></select></div>
      <div class="field"><label for="sCat">Tipo de inmueble</label>
        <select id="sCat"><option value="todos">Todos</option><option>Casa</option><option>Departamento</option><option>Penthouse</option><option>Garzonier</option><option>Condominio</option><option>Terreno</option></select></div>
      <div class="field"><label for="sZona">Zona</label>
        <select id="sZona"><option value="todas">Todas</option><option value="centro">Centro & Prado</option><option value="norte">Norte</option><option value="oeste">Oeste & América</option><option value="sur">Sur</option><option value="valle">Valle</option></select></div>
      <button class="btn" type="submit">Buscar</button>
    </form>
  </div>
  <div class="hero-media">
    <img id="heroImg" class="kb" src="{{ Setting::get('hero_image', 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1800&q=70') }}" alt="Casa moderna en Cochabamba">
    <div class="hero-cap">
      <div><b>{{ Setting::get('hero_caption_title', 'Residencia moderna · Zona Norte') }}</b><span>{{ Setting::get('hero_caption_text', 'Galería completa en la ficha · tocá para ver destacadas') }}</span></div>
      <span class="mono">17.3705° S — 66.1502° W</span>
      <button id="heroCard" type="button">Ver destacadas de la semana ↓</button>
    </div>
  </div>
  <div class="zone-chips" data-reveal>
    <button data-q="Prado">El Prado</button><button data-q="Cala Cala">Cala Cala</button><button data-q="Queru">Queru Queru</button><button data-q="Bosque">El Bosque</button><button data-q="Sarco">Sarco</button><button data-q="América">Av. América</button><button data-q="Quillacollo">Quillacollo</button>
  </div>
  <div class="wrap"><div class="stats">
    <div class="stat"><b><span data-count="{{ Setting::get('stat_years', '14') }}">14</span><em>+</em></b><span>Años en el valle</span></div>
    <div class="stat"><b><span data-count="{{ Setting::get('stat_operations', '320') }}">320</span><em>+</em></b><span>Operaciones cerradas</span></div>
    <div class="stat"><b><span data-count="{{ Setting::get('stat_properties', '54') }}">54</span><em>+</em></b><span>Propiedades activas</span></div>
    <div class="stat"><b><span data-count="{{ Setting::get('stat_recommend', '98') }}">98</span><em>%</em></b><span>Clientes que recomiendan</span></div>
  </div></div>
</section>

<!-- LISTADOS -->
<section id="propiedades">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow" data-reveal>Catálogo</p>
      <h2 class="sec-title" data-reveal style="--d:1">Casas <span class="it">disponibles</span> hoy.</h2>
    </div>
    <div class="controls" data-reveal>
      <div class="seg" id="tipoChips" role="tablist" aria-label="Operación">
        <button class="chip active" data-tipo="todos">Todas</button>
        <button class="chip" data-tipo="venta">Venta</button>
        <button class="chip" data-tipo="alquiler">Alquiler</button>
        <button class="chip" data-tipo="anticretico">Anticrético</button>
      </div>
      <select id="fCat" aria-label="Tipo de inmueble"><option value="todos">Tipo: todos</option><option>Casa</option><option>Departamento</option><option>Penthouse</option><option>Garzonier</option><option>Condominio</option><option>Terreno</option></select>
      <select id="fZona" aria-label="Zona"><option value="todas">Todas las zonas</option><option value="centro">Centro & Prado</option><option value="norte">Norte</option><option value="oeste">Oeste & América</option><option value="sur">Sur</option><option value="valle">Valle</option></select>
      <select id="fPrecio" aria-label="Presupuesto"></select>
      <select id="fOrden" aria-label="Ordenar"><option value="dest">Destacadas</option><option value="asc">Precio ↑</option><option value="desc">Precio ↓</option></select>
      <input type="search" id="fSearch" placeholder="Buscar zona…" aria-label="Buscar">
      <button class="link-btn" id="clearF" type="button">Limpiar</button>
      <p class="count">Mostrando <b id="count">—</b> propiedades · cada una con galería de 6+ fotos</p>
    </div>
    <div class="grid" id="grid"></div>
    <div class="load-wrap"><button class="btn dark" id="loadMore" hidden>Cargar más</button></div>
    <div class="empty" id="empty"><h3 style="font-size:26px;font-weight:800">Nada por aquí… todavía.</h3><p style="color:var(--mut);margin:8px 0 18px">Probá con otra combinación o limpiá los filtros.</p><button class="btn dark" id="emptyClear">Limpiar filtros</button></div>
  </div>
</section>

<!-- MAPA -->
<section id="mapa">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow" data-reveal>Mapa</p>
      <h2 class="sec-title" data-reveal style="--d:1">Cada pin, <span class="it">una historia.</span></h2>
    </div>
    <div class="map-bar" data-reveal>
      <div class="seg" id="modeChips">
        <button class="chip active" data-mode="pins">Pines QASA</button>
        <button class="chip" data-mode="google">Google Maps</button>
      </div>
      <div class="seg" id="flyChips">
        <button class="chip active" data-view="all">Vista general</button>
        <button class="chip" data-view="centro">Centro</button>
        <button class="chip" data-view="norte">Norte</button>
        <button class="chip" data-view="oeste">Oeste</button>
        <button class="chip" data-view="sur">Sur</button>
        <button class="chip" data-view="valle">Valle</button>
      </div>
    </div>
    <div id="map" data-reveal aria-label="Mapa de propiedades en Cochabamba"></div>
    <div id="gmapWrap" data-reveal><iframe title="Mapa de Cochabamba en Google Maps" loading="lazy" src="about:blank" referrerpolicy="no-referrer-when-downgrade"></iframe></div>
    <div class="map-legend"><span><i style="background:var(--ac)"></i>VENTA</span><span><i style="background:var(--green)"></i>ALQUILER</span><span><i style="background:var(--gold)"></i>ANTICRÉTICO</span><span>· TOCÁ UN PIN PARA VER LA FICHA</span></div>
  </div>
</section>

<!-- OPERACIONES -->
<section id="operaciones">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow" data-reveal>Operaciones</p>
      <h2 class="sec-title" data-reveal style="--d:1">Tres formas de llegar <span class="it">a tu casa.</span></h2>
    </div>
    <div class="tiles">
      <article class="tile wide" data-reveal>
        <img class="bg" src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1600&q=70" alt="Piscina y terraza al atardecer">
        <h3>Anticrético. El clásico boliviano, bien hecho.</h3>
        <p>{{ Setting::get('op_anticretico_desc', 'Contrato registrado en Derechos Reales, devolución del capital garantizada por escrito y verificación legal del inmueble antes de firmar.') }}</p>
        <ul><li>Registro en DD.RR.</li><li>Capital protegido</li><li>Verificación legal</li></ul>
        <span class="meta">DESDE {{ Setting::get('op_anticretico_price', 'BS 280.000') }} · <a class="link-btn" style="color:#fff" href="#propiedades" data-filter="anticretico">Ver propiedades →</a></span>
      </article>
      <article class="tile" data-reveal>
        <img class="bg" src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1200&q=70" alt="Casa con jardín">
        <h3>Venta</h3>
        <p>{{ Setting::get('op_venta_desc', 'Tasación sin costo, acompañamiento notarial y opciones con crédito bancario pre-aprobado.') }}</p>
        <span class="meta">DESDE {{ Setting::get('op_venta_price', '$US 85.000') }} · <a class="link-btn" style="color:#fff" href="#propiedades" data-filter="venta">Ver →</a></span>
      </article>
      <article class="tile" data-reveal style="--d:1">
        <img class="bg" src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=70" alt="Living de departamento">
        <h3>Alquiler</h3>
        <p>{{ Setting::get('op_alquiler_desc', 'Contratos claros en bolivianos o dólares, garantías flexibles e inventario fotográfico firmado.') }}</p>
        <span class="meta">DESDE {{ Setting::get('op_alquiler_price', 'BS 2.300/MES') }} · <a class="link-btn" style="color:#fff" href="#propiedades" data-filter="alquiler">Ver →</a></span>
      </article>
    </div>
  </div>
</section>

<!-- DESTACADOS -->
<section id="destacados">
  <div class="hs-sticky">
    <div class="hs-head">
      <div><p class="eyebrow">Destacados</p><h2>La vitrina de la semana.</h2></div>
      <span class="hs-hint">SEGUÍ HACIENDO SCROLL ⟶</span>
    </div>
    <div class="hs-track" id="hsTrack"></div>
  </div>
</section>



<!-- ZONAS -->
<section id="zonas">
  <div class="wrap zonas-grid">
    <div>
      <p class="eyebrow" data-reveal style="justify-content:flex-start">Zonas</p>
      <h2 class="sec-title" data-reveal style="--d:1">El valle, cuadra por cuadra.</h2>
      <p style="color:var(--mut);max-width:440px;margin:10px 0 26px" data-reveal style="--d:2">Valores referenciales del m² por zona. Tocá una zona para ver sus propiedades.</p>
      <a class="btn dark" href="#mapa" data-reveal style="--d:3">Abrir el mapa</a>
    </div>
    <div class="zona-list" id="zonaList" data-reveal style="--d:2"></div>
  </div>
</section>

<!-- NOSOTROS -->
<section id="nosotros">
  <div class="wrap">
    <div class="nos-grid">
      <div>
        <p class="eyebrow" data-reveal style="justify-content:flex-start">Quiénes somos</p>
        <h2 class="manif lines" data-reveal>
          <span class="line"><span class="line-in" style="--i:0">Somos de acá.</span></span>
          <span class="line"><span class="line-in" style="--i:1">Conocemos <span class="it">cada cuadra</span></span></span>
          <span class="line"><span class="line-in" style="--i:2">del valle.</span></span>
        </h2>
        <p class="txt" data-reveal style="--d:2">{{ Setting::get('about_text', 'QASA nació en 2012 en una oficina chiquita cerca del Prado, con una idea grande: que comprar, alquilar o dar en anticrético en Cochabamba sea tan claro como un apretón de manos.') }}</p>
        <p class="txt" data-reveal style="--d:3"></p>
        <div class="val-chips" data-reveal style="--d:4"><span>✦ Ética primero</span><span>✦ Datos claros</span><span>✦ Cero letra chica</span><span>✦ Cochalos de pura cepa</span></div>
      </div>
      <div class="collage" data-reveal style="--d:2">
        <div class="c1"><img src="{{ Setting::get('about_image_1', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1000&q=70') }}" alt="Casa blanca moderna"></div>
        <div class="c2"><img src="{{ Setting::get('about_image_2', 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=700&q=70') }}" alt="Interior cálido"></div>
        <div class="yrs"><b>{{ Setting::get('stat_years', '14') }}</b><span>AÑOS EN EL VALLE</span></div>
      </div>
    </div>
    <div class="timeline">
@foreach($milestones as $i => $m)
      <div class="tl-item" data-reveal style="--d:{{ $i }}">
        <b>{{ $m->year }}</b>
        <p>{{ $m->description }}</p>
      </div>
@endforeach
  </div>
  <div class="team-grid">
@foreach($team as $i => $member)
      <article class="team-card" data-reveal style="--d:{{ $i }}">
        <img src="{{ $member->photo_url }}" alt="">
        <div><b>{{ $member->name }}</b><span>{{ $member->role }}</span></div>
      </article>
@endforeach
  </div>
  </div>
</section>

<!-- PROCESO -->
<section id="proceso">
  <div class="wrap">
    <div class="sec-head"><p class="eyebrow" data-reveal>Proceso</p><h2 class="sec-title" data-reveal style="--d:1">Cuatro pasos, <span class="it">cero letra chica.</span></h2></div>
    <div class="stack">
      <article class="step c1" style="--i:0"><span class="n">01</span><div><h3>Descubrí</h3><p>Filtrá por operación, tipo de inmueble, zona y presupuesto, o explorá el mapa. Cada ficha con galería completa y pin verificado.</p></div></article>
      <article class="step c2" style="--i:1"><span class="n">02</span><div><h3>Visitá</h3><p>Coordinamos la visita en menos de 48 horas con un asesor que conoce la zona cuadra por cuadra.</p></div></article>
      <article class="step c3" style="--i:2"><span class="n">03</span><div><h3>Negociá</h3><p>Te acompañamos con la oferta, la tasación comparativa y la revisión de papeles. Con datos y sin apuros.</p></div></article>
      <article class="step c4" style="--i:3"><span class="n">04</span><div><h3>Firmá</h3><p>Contrato claro, notaría de confianza y entrega de llaves con inventario firmado.</p></div></article>
    </div>
  </div>
</section>

<!-- TESTIMONIOS -->
@if($testimonials->count())
<section id="historias">
  <div class="wrap">
    <div class="sec-head"><p class="eyebrow" data-reveal>Historias</p><h2 class="sec-title" data-reveal style="--d:1">Gente que ya tiene <span class="it">sus llaves.</span></h2></div>
    <div class="post-board">
@foreach($testimonials as $i => $t)
      <article class="postcard" style="--x:{{ $postPos[$i % count($postPos)][0] }}%;--y:{{ $postPos[$i % count($postPos)][1] }}%;--r:{{ $postPos[$i % count($postPos)][2] }}deg">
        <q>“{{ $t->quote }}”</q>
        <div class="who">
          <img src="{{ $t->photo_url }}" alt="{{ $t->author }}">
          <div><b>{{ $t->author }}</b><span>{{ $t->operation }} · {{ $t->location }}</span></div>
        </div>
      </article>
@endforeach
    </div>
  </div>
</section>
@endif


<!-- BANDA PUBLICAR -->
<section class="cta-band">
  <div class="wrap">
    <h2 data-reveal>{{ Setting::get('owner_cta_title', '¿Tenés una propiedad?') }}<br><em style="font-style:italic">{{ Setting::get('owner_cta_text', 'Publicala con nosotros.') }}</em></h2>
    <p data-reveal style="--d:1">{{ Setting::get('owner_cta_desc', 'Tasación gratuita en 24 h, sesión de fotos profesional y publicación en los principales portales. Vos mostrás las llaves, nosotros hacemos el resto.') }}</p>
    <div class="row" data-reveal style="--d:2">
      <a class="btn light" href="#propietarios">Quiero publicar mi propiedad</a>
      <a class="btn" style="background:rgba(0,0,0,.25)" href="https://wa.me/{{ Setting::get('contact_whatsapp', '59170012345') }}" target="_blank" rel="noopener">WhatsApp directo</a>
    </div>
    <div class="mini" data-reveal style="--d:3"><span>✦ TASACIÓN GRATIS 24 H</span><span>✦ FOTOS + DRON</span><span>✦ 6 PORTALES</span><span>✦ VISITAS FILTRADAS</span></div>
  </div>
</section>

<!-- PROPIETARIOS -->
<section id="propietarios">
  <div class="wrap">
    <div class="sec-head"><p class="eyebrow" data-reveal>Propietarios</p><h2 class="sec-title" data-reveal style="--d:1">Tu propiedad, <em style="font-style:italic;color:#f0a184">en buenas manos.</em></h2></div>
    <div class="owner-tabs" role="tablist" data-reveal>
      <button class="owner-tab active" data-tab="vender" aria-selected="true">Quiero vender</button>
      <button class="owner-tab" data-tab="alquilar" aria-selected="false">Quiero alquilar</button>
      <button class="owner-tab" data-tab="anticretico" aria-selected="false">Dar en anticrético</button>
    </div>
    <div class="owner-grid">
      <div>
        <div class="owner-panel active" data-panel="vender">
          <h3>Vendé al <em>mejor precio</em>, sin dolores de cabeza.</h3>
          <p>{{ Setting::get('owner_sell_desc') }}</p>
          <ul><li>Tasación gratuita y por escrito en 24 h</li><li>Publicación en 6 portales + cartera propia</li><li>Negociación profesional y papeleo notarial</li><li>Acompañamiento bancario hasta el desembolso</li></ul>
          <span class="owner-stat">{{ Setting::get('owner_sell_stat', 'PROMEDIO DE VENTA QASA: 45 DÍAS · 97% DEL PRECIO PEDIDO') }}</span>
        </div>
        <div class="owner-panel" data-panel="alquilar">
          <h3>Alquilá <em>tranquilo</em>: nosotros nos preocupamos.</h3>
          <p>{{ Setting::get('owner_rent_desc') }}</p>
          <ul><li>Selección y verificación de inquilinos</li><li>Contrato digital + inventario firmado</li><li>Gestión de cobro mensuales</li><li>Respuesta legal ante incumplimientos</li></ul>
          <span class="owner-stat">{{ Setting::get('owner_rent_stat', '0 DESALOJOS PERDIDOS EN LOS ÚLTIMOS 5 AÑOS') }}</span>
        </div>
        <div class="owner-panel" data-panel="anticretico">
          <h3>Anticrético <em>seguro</em>, capital protegido.</h3>
          <p>{{ Setting::get('owner_anti_desc') }}</p>
          <ul><li>Valuación según mercado real</li><li>Contrapartes con solvencia verificada</li><li>Contrato registrado en Derechos Reales</li><li>Acompañamiento hasta la devolución</li></ul>
          <span class="owner-stat">{{ Setting::get('owner_anti_stat', '100% DE CONTRATOS REGISTRADOS Y SIN CONFLICTOS') }}</span>
        </div>
        <div class="assure" data-reveal><span>✦ Tasación gratis 24 h</span><span>✦ Fotos + dron incluidos</span><span>✦ Publicación en 6 portales</span><span>✦ Visitas filtradas</span></div>
      </div>
      <form class="owner-form" id="ownerForm" data-reveal style="--d:1">
        <h3>Solicitá tu tasación gratuita</h3>
        <div class="f-row">
          <div class="f-field"><label for="pNombre">Nombre</label><input id="pNombre" required placeholder="Tu nombre"></div>
          <div class="f-field"><label for="pTel">WhatsApp</label><input id="pTel" required placeholder="+591 …"></div>
        </div>
        <div class="f-row">
          <div class="f-field"><label for="pZona">Zona de la propiedad</label><input id="pZona" required placeholder="Ej.: Cala Cala…"></div>
          <div class="f-field"><label for="pTipo">Tipo</label><select id="pTipo"><option>Casa</option><option>Departamento</option><option>Penthouse / Dúplex</option><option>Terreno / Lote</option><option>Local / Oficina</option></select></div>
        </div>
        <div class="f-row">
          <div class="f-field"><label for="pOp">Quiero…</label><select id="pOp"><option value="vender">Venderla</option><option value="alquilar">Alquilarla</option><option value="anticretico">Darla en anticrético</option></select></div>
          <div class="f-field"><label for="pM2">m² aprox.</label><input id="pM2" type="number" min="1" placeholder="Ej.: 180"></div>
        </div>
        <div class="f-field"><label for="pMsg">Contanos algo más (opcional)</label><textarea id="pMsg" placeholder="Antigüedad, documentos, tiempos…"></textarea></div>
        <button class="btn dark" type="submit" id="pBtn">Solicitar tasación gratuita</button>
      </form>
    </div>
  </div>
</section>

<!-- FAQ -->
<section id="faq">
  <p class="eyebrow" data-reveal style="justify-content:flex-start">Preguntas frecuentes</p>
  <h2 class="sec-title" data-reveal style="--d:1">Antes de preguntar, <span class="it">mirá acá.</span></h2>
  @foreach($faqs as $i => $faq)
  <div class="faq-item" data-reveal @if($i > 0) style="--d:{{ $i }}" @endif>
    <button class="faq-q" aria-expanded="false">{{ $faq->question }}<span class="pm">+</span></button>
    <div class="faq-a"><p>{{ $faq->answer }}</p></div>
  </div>
@endforeach

</section>

<!-- CONTACTO -->
<section id="contacto">
  <div class="wrap contact-grid">
    <div>
      <p class="eyebrow" data-reveal style="justify-content:flex-start">Contacto</p>
      <h2 class="sec-title lines" data-reveal><span class="line"><span class="line-in" style="--i:0">Hablemos de tu</span></span><span class="line"><span class="line-in" style="--i:1"><span class="it">próxima casa.</span></span></span></h2>
      <div class="info-list" data-reveal style="--d:2">
        <div class="info-item"><span>Oficina</span><p>{{ Setting::get('contact_address', 'Av. América 1234, Edif. Torre QASA') }}<br>Cochabamba, Bolivia</p></div>
        <div class="info-item"><span>WhatsApp</span><p><a href="https://wa.me/{{ Setting::get('contact_whatsapp', '59170012345') }}">{{ Setting::get('contact_phone', '+591 700 12 345') }}</a> · <a href="tel:+59144521234">(4) 452 1234</a></p></div>
        <div class="info-item"><span>Email</span><p><a href="mailto:{{ Setting::get('contact_email', 'hola@qasa.bo') }}">{{ Setting::get('contact_email', 'hola@qasa.bo') }}</a></p></div>
        <div class="info-item"><span>Horario</span><p>{{ Setting::get('contact_hours', 'Lun – Sáb · 9:00 a 19:00') }}</p></div>
      </div>
    </div>
    <form class="ficha" id="contactForm" data-reveal style="--d:2">
      <div class="f-row">
        <div class="f-field"><label for="cNombre">Nombre completo</label><input id="cNombre" required placeholder="Ej.: Camila Rojas"></div>
        <div class="f-field"><label for="cTel">Teléfono / WhatsApp</label><input id="cTel" required placeholder="+591 …"></div>
      </div>
      <div class="f-field"><label for="cMotivo">Me interesa</label>
        <select id="cMotivo"><option value="comprar">Comprar una propiedad</option><option value="alquilar">Alquilar</option><option value="anticretico">Anticrético</option><option value="vender">Vender mi propiedad</option><option value="tasacion">Una tasación gratuita</option></select>
      </div>
      <div class="f-field"><label for="cMsg">Mensaje</label><textarea id="cMsg" placeholder="Contanos qué buscás: zona, presupuesto, tiempos…"></textarea></div>
      <button class="btn dark" type="submit" id="cBtn">Enviar consulta</button>
    </form>
  </div>
</section>

<footer>
  <div class="wrap">
    <div class="f-grid">
      <div class="f-brand"><a class="logo" href="#top"><i></i>QASA</a><p>{{ Setting::get('footer_text', 'Inmobiliaria cochabambina. Compra, venta, alquiler y anticrético con papeles claros desde 2012.') }}</p></div>
      <div><h4>Explorar</h4><a href="#propiedades">Propiedades</a><a href="#mapa">Mapa</a><a href="#destacados">Destacados</a><a href="#nosotros">Nosotros</a></div>
      <div><h4>Operaciones</h4><a href="#operaciones">Venta</a><a href="#operaciones">Alquiler</a><a href="#operaciones">Anticrético</a><a href="#propietarios">Publicar propiedad</a></div>
      <div><h4>Legal</h4><a href="{{ Setting::get('legal_terms', '#') }}">Términos de uso</a><a href="{{ Setting::get('legal_privacy', '#') }}">Privacidad</a><a href="{{ Setting::get('legal_registry', '#') }}">Registro FUNDEMPRESA</a></div>
    </div>
    <div class="f-bottom">
      <span>{{ Setting::get('footer_copy', '© ' . date('Y') . ' QASA · Cochabamba, Bolivia') }}</span>
      <span class="mono">{{ Setting::get('footer_note', '17.3895° S, 66.1566° W · Cochabamba, Bolivia') }}</span>
      <button id="toTop">Volver arriba ↑</button>
    </div>
  </div>
</footer>

<a class="wapp" id="wappBtn" href="https://wa.me/{{ Setting::get('contact_whatsapp', '59170012345') }}" target="_blank" rel="noopener" aria-label="WhatsApp"><svg viewBox="0 0 32 32"><path d="M16 3C9.4 3 4 8.3 4 14.9c0 2.6.8 5 2.3 7L4 29l7.3-2.2c1.9 1 3.9 1.5 4.7 1.5 6.6 0 12-5.3 12-11.9S22.6 3 16 3zm6.2 16.4c-.3.8-1.6 1.5-2.2 1.6-.6.1-1.3.2-3.7-.8-3.1-1.3-5.1-4.4-5.3-4.6-.2-.2-1.3-1.7-1.3-3.2s.8-2.3 1.1-2.6c.3-.3.6-.4.8-.4h.6c.2 0 .5-.1.7.5l1 2.4c.1.2.1.4 0 .6l-.4.7-.6.7c-.2.2-.4.4-.2.7.2.4 1 1.7 2.2 2.7 1.5 1.3 2.8 1.8 3.2 1.9.4.2.6.2.8-.1l1.2-1.4c.2-.3.5-.2.8-.1l2.2 1.1c.3.2.6.3.7.4.1.3.1.9-.2 1.7z"/></svg></a>

<!-- MODAL -->
<div class="modal" id="modal" hidden role="dialog" aria-modal="true" aria-labelledby="mTitle">
  <div class="modal-back" data-close></div>
  <div class="modal-panel">
    <button class="modal-close" data-close aria-label="Cerrar">✕</button>
    <div class="gal">
      <div class="gal-main"><img id="mImg" src="" alt="">
        <button class="gal-nav prev" id="gPrev" aria-label="Anterior">‹</button>
        <button class="gal-nav next" id="gNext" aria-label="Siguiente">›</button>
        <span class="gal-count" id="gCount">1/1</span>
      </div>
      <div class="gal-thumbs" id="gThumbs"></div>
    </div>
    <div class="modal-info">
      <span class="badge" id="mBadge"></span>
      <h3 id="mTitle"></h3>
      <p class="loc" id="mLoc"></p>
      <p class="price-row" style="margin-top:8px"><strong id="mPrecio"></strong><span class="per" id="mPer"></span></p>
      <p class="desc" id="mDesc"></p>
      <div class="specs" id="mSpecs"></div>
      <div class="feats" id="mFeats"></div>
      <div class="modal-cta">
        <button class="btn dark" id="mAgendar">Agendar visita</button>
        <a class="btn wsp" id="mWapp" target="_blank" rel="noopener">WhatsApp</a>
        <a class="btn ghost" id="mGmaps" target="_blank" rel="noopener">Google Maps ↗</a>
      </div>
      <p class="modal-coords" id="mCoords"></p>
    </div>
  </div>
</div>

<!-- ============ SCRIPT 1: NÚCLEO ============ -->
<script>
(function(){
"use strict";
var $=function(s){return document.querySelector(s)}, $$=function(s){return Array.prototype.slice.call(document.querySelectorAll(s))};
window.reduced=matchMedia("(prefers-reduced-motion: reduce)").matches;
window.$q=$; window.$$q=$$;

/* ====== CONFIGURACIÓN DE TU MARCA (editá acá) ====== */
var CONFIG=@json($configJs);
window.CONFIG=CONFIG;
function waLink(msg){return "https://wa.me/"+CONFIG.wa+"?text="+encodeURIComponent(msg);}
window.waLink=waLink;
try{var wb=$("#wappBtn");if(wb)wb.href=waLink("¡Hola "+CONFIG.nombre+"! Quiero información sobre sus propiedades.");}catch(e){}

/* modo oscuro: guardado + sistema */
(function(){
  var root=document.documentElement,t=null;
  try{t=localStorage.getItem("qasa-theme")}catch(e){}
  if(!t)t=matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light";
  root.setAttribute("data-theme",t);
  function paint(){var b=$("#themeBtn");if(b)b.textContent=root.getAttribute("data-theme")==="dark"?"☀":"☾";}
  window.__setTheme=function(v){root.setAttribute("data-theme",v);try{localStorage.setItem("qasa-theme",v)}catch(e){}paint();};
  paint();
})();

/* fallback de imágenes */
var PLACEHOLDER="data:image/svg+xml,"+encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600"><rect width="100%" height="100%" fill="#f5f5f7"/><path d="M400 230l130 110v130H270V340z" fill="#d2d2d7"/><text x="400" y="510" font-family="sans-serif" font-size="22" fill="#86868b" text-anchor="middle">QASA · Cochabamba</text></svg>');
document.addEventListener("error",function(e){
  var t=e.target; if(!t||t.tagName!=="IMG")return;
  if(!t.dataset.fb){t.dataset.fb="1"; if(t.dataset.alt){t.src=t.dataset.alt;}}
  else if(t.dataset.fb==="1"){t.dataset.fb="2"; t.src=PLACEHOLDER;}
},true);

/* pool Unsplash */
var U=@json($U);
function pic(i,w){return __u(U[i])+"?auto=format&fit=crop&w="+(w||1200)+"&q=70";}
window.pic=pic;

var PROPS=@json($propsJs);
PROPS.forEach(function(p){ if(p.lat==null){p.lat=-17.389+(Math.random()-.5)*.12; p.lng=-66.16+(Math.random()-.5)*.14;} });
window.PROPS=PROPS;

var TIPO_LABEL={venta:"Venta",alquiler:"Alquiler",anticretico:"Anticrético"};
var TIPO_COLOR={venta:"#b64a26",alquiler:"#3f5c46",anticretico:"#9a742b"};
window.TIPO_LABEL=TIPO_LABEL; window.TIPO_COLOR=TIPO_COLOR;
var PRECIOS={
 todos:[["all","Cualquier precio"]],
 venta:[["all","Cualquier precio"],["0-100000","Hasta $us 100 mil"],["100000-250000","$us 100 – 250 mil"],["250000-1000000","$us 250 mil – 1 M"],["1000000-9e9","$us 1 M +"]],
 alquiler:[["all","Cualquier precio"],["0-2500","Hasta Bs 2.500/mes"],["2500-4000","Bs 2.500 – 4.000/mes"],["4000-9e9","Bs 4.000+ /mes"]],
 anticretico:[["all","Cualquier precio"],["0-300000","Hasta Bs 300 mil"],["300000-9e9","Bs 300 mil +"]]
};
var fmt=function(n){return n.toLocaleString("es-BO")}; window.fmt=fmt;
var pinSVG='<svg viewBox="0 0 24 24"><path d="M12 22s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12zm0-9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>';

var state={tipo:"todos",cat:"todos",zona:"todas",precio:"all",orden:"dest",q:"",shown:9,favoritos:{}};
window.state=state;
function fillPrecio(sel,tipo){ if(!sel)return; sel.innerHTML=PRECIOS[tipo].map(function(x){return '<option value="'+x[0]+'">'+x[1]+'</option>'}).join(""); sel.value="all"; }
window.fillPrecio=fillPrecio;
fillPrecio($("#fPrecio"),"todos");

function filtrar(){
  return PROPS.filter(function(p){
    if(state.tipo!=="todos"&&p.tipo!==state.tipo)return false;
    if(state.cat!=="todos"&&p.cat!==state.cat)return false;
    if(state.zona!=="todas"&&p.grupo!==state.zona)return false;
    if(state.precio!=="all"){var r=state.precio.split("-"),a=+r[0],b=+r[1];if(p.precio<a||p.precio>b)return false;}
    if(state.q){var q=state.q.toLowerCase();if((p.titulo+" "+p.zona).toLowerCase().indexOf(q)===-1)return false;}
    return true;
  }).sort(function(a,b){
    if(state.orden==="asc")return a.precio-b.precio;
    if(state.orden==="desc")return b.precio-a.precio;
    return (b.dest?1:0)-(a.dest?1:0);
  });
}
window.filtrar=filtrar;

function specsHTML(p){return p.cat==="Terreno"?'<span>Lote</span><span>'+fmt(p.m2)+' m²</span>':'<span>'+p.hab+' hab</span><span>'+p.banos+' baños</span><span>'+p.m2+' m²</span>';}
function cardHTML(p,i){
  return '<article class="prop-card" data-id="'+p.id+'" style="--d:'+i+'" tabindex="0" role="button" aria-label="Ver '+p.titulo+'">'+
    '<div class="prop-media"><img loading="lazy" data-alt="'+pic(p.imgs[1],800)+'" src="'+pic(p.imgs[0],800)+'" alt="'+p.titulo+'">'+
      '<span class="badge b-'+p.tipo+'">'+TIPO_LABEL[p.tipo]+'</span>'+
      '<button class="fav '+(state.favoritos[p.id]?"on":"")+'" data-fav="'+p.id+'" aria-label="Guardar"><svg viewBox="0 0 24 24"><path d="M12 21s-7.5-4.9-9.5-9.2C1 8.4 3 5 6.4 5c2 0 3.6 1.1 4.6 2.7l1 1.6 1-1.6c1-1.6 2.6-2.7 4.6-2.7C21 5 23 8.4 21.5 11.8 19.5 16.1 12 21 12 21z"/></svg></button>'+
      '<span class="photos-chip">▣ '+p.imgs.length+' fotos</span></div>'+
    '<div class="prop-body"><div class="price-row"><strong>'+p.cur+' '+fmt(p.precio)+'</strong>'+(p.per?'<span class="per">'+p.per+'</span>':"")+'</div>'+
      '<h3>'+p.titulo+'</h3><p class="loc">'+pinSVG+p.zona+' · Cochabamba</p>'+
      '<div class="specs">'+specsHTML(p)+'<button class="maplink" data-map="'+p.id+'">ver en mapa →</button></div></div></article>';
}
window.renderMarkers=window.renderMarkers||function(){};
function render(){
  var list=filtrar(),grid=$("#grid"),vis=list.slice(0,state.shown);
  grid.classList.remove("go");
  grid.innerHTML=vis.map(cardHTML).join("");
  $("#empty").classList.toggle("show",!list.length);
  $("#count").textContent=vis.length+" de "+list.length;
  var lm=$("#loadMore");
  if(list.length>state.shown){lm.hidden=false;lm.textContent="Cargar más · quedan "+(list.length-state.shown);}
  else lm.hidden=true;
  requestAnimationFrame(function(){requestAnimationFrame(function(){grid.classList.add("go")})});
  window.renderMarkers();
}
window.render=render;
function filterChanged(){state.shown=9;render();}
render();
$("#loadMore").onclick=function(){state.shown+=6;render();};

/* hover rota fotos */
$("#grid").addEventListener("mouseover",function(e){
  var card=e.target.closest(".prop-card"); if(!card||window.reduced||card._t)return;
  var p=PROPS.filter(function(x){return x.id===+card.dataset.id})[0]; if(!p)return;
  var im=card.querySelector("img"),i=0;
  card._t=setInterval(function(){i=(i+1)%p.imgs.length;im.src=pic(p.imgs[i],800);},900);
});
$("#grid").addEventListener("mouseout",function(e){
  var card=e.target.closest(".prop-card"); if(!card||!card._t)return;
  clearInterval(card._t); card._t=null;
  var p=PROPS.filter(function(x){return x.id===+card.dataset.id})[0];
  if(p)card.querySelector("img").src=pic(p.imgs[0],800);
});

/* filtros */
function syncUI(){
  $$("#tipoChips .chip").forEach(function(c){c.classList.toggle("active",c.dataset.tipo===state.tipo)});
  if($("#fZona"))$("#fZona").value=state.zona;
  if($("#fCat"))$("#fCat").value=state.cat;
  if($("#fSearch"))$("#fSearch").value=state.q;
  if($("#sOp"))$("#sOp").value=state.tipo;
  if($("#sZona"))$("#sZona").value=state.zona;
  if($("#sCat"))$("#sCat").value=state.cat;
}
function aplicarEstado(reset){ if(reset)fillPrecio($("#fPrecio"),state.tipo); syncUI(); render(); }
window.aplicarEstado=aplicarEstado;
$("#tipoChips").addEventListener("click",function(e){var c=e.target.closest(".chip");if(c){state.tipo=c.dataset.tipo;filterChanged();}});
$("#fCat").onchange=function(e){state.cat=e.target.value;filterChanged();};
$("#fZona").onchange=function(e){state.zona=e.target.value;filterChanged();};
$("#fPrecio").onchange=function(e){state.precio=e.target.value;filterChanged();};
$("#fOrden").onchange=function(e){state.orden=e.target.value;filterChanged();};
$("#fSearch").oninput=function(e){state.q=e.target.value;filterChanged();};
function limpiar(){state.tipo="todos";state.cat="todos";state.zona="todas";state.precio="all";state.orden="dest";state.q="";state.shown=9;aplicarEstado(true);toast("Filtros limpiados ✦");}
$("#clearF").onclick=limpiar; $("#emptyClear").onclick=limpiar;
$$("[data-filter]").forEach(function(a){a.addEventListener("click",function(){state.tipo=a.dataset.filter;filterChanged();});});
$$(".zone-chips button").forEach(function(b){b.addEventListener("click",function(){
  state.q=b.dataset.q; state.shown=9; aplicarEstado(false);
  document.getElementById("propiedades").scrollIntoView({behavior:window.reduced?"auto":"smooth"});
  toast(filtrar().length+" propiedades en "+b.textContent+" ✦");
});});
$("#searchForm").addEventListener("submit",function(e){
  e.preventDefault();
  state.tipo=$("#sOp").value; state.cat=$("#sCat").value; state.zona=$("#sZona").value; state.shown=9;
  aplicarEstado(true);
  document.getElementById("propiedades").scrollIntoView({behavior:window.reduced?"auto":"smooth"});
  toast(filtrar().length+" propiedades encontradas ✦");
});

/* favoritos + click tarjetas */
function toggleFav(id,btn){
  state.favoritos[id]=!state.favoritos[id];
  btn.classList.toggle("on",!!state.favoritos[id]);
  var n=Object.keys(state.favoritos).filter(function(k){return state.favoritos[k]}).length;
  $("#favCount").textContent=n;
  toast(state.favoritos[id]?"Guardada en favoritas ♥":"Quitada de favoritas");
}
$("#grid").addEventListener("click",function(e){
  var f=e.target.closest("[data-fav]");
  if(f){e.stopPropagation();toggleFav(+f.dataset.fav,f);return;}
  var m=e.target.closest("[data-map]");
  if(m){e.stopPropagation();window.showOnMap(+m.dataset.map);return;}
  var card=e.target.closest(".prop-card"); if(card)openModal(+card.dataset.id);
});
$("#grid").addEventListener("keydown",function(e){if(e.key==="Enter"){var c=e.target.closest(".prop-card");if(c)openModal(+c.dataset.id);}});

/* MODAL + GALERÍA + WhatsApp */
var currentProp=null,mIdx=0;
function setImg(i){
  if(!currentProp)return;
  var arr=currentProp.imgs; mIdx=((i%arr.length)+arr.length)%arr.length;
  $("#mImg").src=pic(arr[mIdx],1400);
  $("#gCount").textContent=(mIdx+1)+" / "+arr.length;
  $$("#gThumbs img").forEach(function(t,j){t.classList.toggle("on",j===mIdx)});
}
function openModal(id){
  var p=PROPS.filter(function(x){return x.id===id})[0]; if(!p)return;
  currentProp=p; mIdx=0;
  $("#gThumbs").innerHTML=p.imgs.map(function(k,i){return '<img src="'+pic(k,200)+'" data-i="'+i+'" alt="Foto '+(i+1)+'" loading="lazy">'}).join("");
  setImg(0);
  var b=$("#mBadge"); b.textContent=TIPO_LABEL[p.tipo]; b.className="badge b-"+p.tipo;
  $("#mTitle").textContent=p.titulo;
  $("#mLoc").innerHTML=pinSVG+p.zona+" · Cochabamba";
  $("#mPrecio").textContent=p.cur+" "+fmt(p.precio); $("#mPer").textContent=p.per||"";
  $("#mDesc").textContent=p.desc;
  $("#mSpecs").innerHTML="<span>"+p.cat+"</span>"+specsHTML(p)+"<span>Año "+p.anio+"</span>";
  $("#mFeats").innerHTML=p.feats.map(function(f){return "<span>"+f+"</span>"}).join("");
  $("#mCoords").textContent=Math.abs(p.lat).toFixed(4)+"° S · "+Math.abs(p.lng).toFixed(4)+"° W — "+p.zona.toUpperCase()+", COCHABAMBA · COD. Q-000"+p.id;
  $("#mGmaps").href="https://www.google.com/maps/search/?api=1&query="+p.lat+","+p.lng;
  $("#mWapp").href=waLink("¡Hola "+CONFIG.nombre+"! Me interesa \""+p.titulo+"\" en "+p.zona+" ("+p.cur+" "+fmt(p.precio)+p.per+"). ¿Cuándo podemos agendar una visita?");
  var m=$("#modal"); m.hidden=false; document.body.style.overflow="hidden";
  requestAnimationFrame(function(){m.classList.add("open")});
}
window.openModal=openModal;
function closeModal(){var m=$("#modal");m.classList.remove("open");document.body.style.overflow="";setTimeout(function(){m.hidden=true},350);}
$$("[data-close]").forEach(function(el){el.onclick=closeModal});
$("#gPrev").onclick=function(){setImg(mIdx-1)};
$("#gNext").onclick=function(){setImg(mIdx+1)};
$("#gThumbs").addEventListener("click",function(e){var t=e.target.closest("img");if(t)setImg(+t.dataset.i)});
document.addEventListener("keydown",function(e){
  if($("#modal").hidden)return;
  if(e.key==="Escape")closeModal();
  if(e.key==="ArrowLeft")setImg(mIdx-1);
  if(e.key==="ArrowRight")setImg(mIdx+1);
});
$("#mAgendar").onclick=function(){
  if(!currentProp)return;
  var mapOp={venta:"comprar",alquiler:"alquilar",anticretico:"anticretico"};
  $("#cMotivo").value=mapOp[currentProp.tipo];
  $("#cMsg").value='Hola, quiero agendar una visita a "'+currentProp.titulo+'" en '+currentProp.zona+".";
  closeModal();
  document.getElementById("contacto").scrollIntoView({behavior:window.reduced?"auto":"smooth"});
  toast("Contanos tus horarios y coordinamos ✦");
};
$("#heroCard").onclick=function(){document.getElementById("destacados").scrollIntoView({behavior:window.reduced?"auto":"smooth"})};

/* destacados horizontal */
var dests=PROPS.filter(function(p){return p.dest});
$("#hsTrack").innerHTML=dests.map(function(p,i){
  return '<article class="slide" data-id="'+p.id+'" role="button" tabindex="0">'+
  '<div class="ph"><img loading="lazy" data-alt="'+pic(p.imgs[1],1200)+'" src="'+pic(p.imgs[0],1200)+'" alt="'+p.titulo+'"><span class="idx">0'+(i+1)+' / 0'+dests.length+' · '+p.imgs.length+' FOTOS</span></div>'+
  '<div class="slide-cap"><b>'+p.titulo+'</b><span>'+TIPO_LABEL[p.tipo].toUpperCase()+' · '+p.zona.toUpperCase()+' — '+p.cur+' '+fmt(p.precio)+p.per+'</span></div></article>';
}).join("")+'<div class="slide cap-cta"><a href="#propiedades">Ver todo el catálogo →</a></div>';
$("#hsTrack").addEventListener("click",function(e){var s=e.target.closest(".slide[data-id]");if(s)openModal(+s.dataset.id)});
$("#hsTrack").addEventListener("keydown",function(e){if(e.key==="Enter"){var s=e.target.closest(".slide[data-id]");if(s)openModal(+s.dataset.id)}});

/* zonas lista */
var ZONAS=@json($zonasJs);
$("#zonaList").innerHTML=ZONAS.map(function(z){
  var n=PROPS.filter(function(p){return p.grupo===z.g}).length;
  return '<div class="zona-row" data-g="'+z.g+'" role="button" tabindex="0"><div><b>'+z.n+'</b><small>'+z.d+'</small></div><span class="zp">'+z.p+'</span><span class="zn">'+n+' prop.</span></div>';
}).join("");
function zonaGo(g){state.zona=g;state.tipo="todos";state.shown=9;aplicarEstado(true);document.getElementById("propiedades").scrollIntoView({behavior:window.reduced?"auto":"smooth"});toast(filtrar().length+" propiedades en la zona ✦");}
$("#zonaList").addEventListener("click",function(e){var r=e.target.closest(".zona-row");if(r)zonaGo(r.dataset.g)});
$("#zonaList").addEventListener("keydown",function(e){if(e.key==="Enter"){var r=e.target.closest(".zona-row");if(r)zonaGo(r.dataset.g)}});

/* propietarios tabs */
$$(".owner-tab").forEach(function(t){t.addEventListener("click",function(){
  $$(".owner-tab").forEach(function(x){var on=x===t;x.classList.toggle("active",on);x.setAttribute("aria-selected",on)});
  $$(".owner-panel").forEach(function(p){p.classList.toggle("active",p.dataset.panel===t.dataset.tab)});
  $("#pOp").value=t.dataset.tab;
});});

/* formularios → WhatsApp (+ Formspree opcional) */
function enviarAFormspree(datos){
  if(!CONFIG.formspree)return;
  try{fetch(CONFIG.formspree,{method:"POST",headers:{"Content-Type":"application/json",Accept:"application/json"},body:JSON.stringify(datos)}).catch(function(){});}catch(e){}
}
$("#ownerForm").addEventListener("submit",function(e){
  e.preventDefault();
  var btn=$("#pBtn"),f=e.target;
  var msg="Hola "+CONFIG.nombre+"! Soy "+$("#pNombre").value+" y quiero "+
    ($("#pOp").value==="vender"?"vender":$("#pOp").value==="alquilar"?"alquilar":"dar en anticrético")+
    " mi "+$("#pTipo").value.toLowerCase()+" en "+$("#pZona").value+" ("+($("#pM2").value||"—")+" m²). Tel: "+$("#pTel").value+
    ($("#pMsg").value?". "+$("#pMsg").value:"");
  btn.disabled=true; btn.innerHTML="Enviando…";
  enviarAFormspree({formulario:"Propietarios",nombre:$("#pNombre").value,tel:$("#pTel").value,zona:$("#pZona").value,tipo:$("#pTipo").value,operacion:$("#pOp").value,mensaje:$("#pMsg").value});
  setTimeout(function(){
    btn.classList.add("sent"); btn.innerHTML="✓ ¡Recibido!";
    toast("¡Gracias! Te abrimos WhatsApp para coordinar ✦");
    window.open(waLink(msg),"_blank");
    f.reset();
    setTimeout(function(){btn.classList.remove("sent");btn.disabled=false;btn.innerHTML="Solicitar tasación gratuita";},4000);
  },900);
});
$("#contactForm").addEventListener("submit",function(e){
  e.preventDefault();
  var btn=$("#cBtn"),f=e.target;
  var motivo=$("#cMotivo").options[$("#cMotivo").selectedIndex].text;
  var msg="Hola "+CONFIG.nombre+"! Soy "+$("#cNombre").value+" (tel "+$("#cTel").value+"). Me interesa: "+motivo+
    ($("#cMsg").value?". "+$("#cMsg").value:"");
  btn.disabled=true; btn.innerHTML="Enviando…";
  enviarAFormspree({formulario:"Contacto",nombre:$("#cNombre").value,tel:$("#cTel").value,motivo:motivo,mensaje:$("#cMsg").value});
  setTimeout(function(){
    btn.classList.add("sent"); btn.innerHTML="✓ ¡Consulta enviada!";
    toast("¡Gracias! Te abrimos WhatsApp para responderte al toque ✦");
    window.open(waLink(msg),"_blank");
    f.reset();
    setTimeout(function(){btn.classList.remove("sent");btn.disabled=false;btn.innerHTML="Enviar consulta";},4000);
  },900);
});

/* FAQ */
$$(".faq-item").forEach(function(it){
  var q=it.querySelector(".faq-q"),a=it.querySelector(".faq-a");
  q.addEventListener("click",function(){
    var open=it.classList.contains("open");
    $$(".faq-item.open").forEach(function(o){o.classList.remove("open");o.querySelector(".faq-a").style.maxHeight=null;o.querySelector(".faq-q").setAttribute("aria-expanded","false")});
    if(!open){it.classList.add("open");a.style.maxHeight=a.scrollHeight+"px";q.setAttribute("aria-expanded","true");}
  });
});

/* misc */
var toastT;
function toast(msg){var t=$("#toast");t.textContent=msg;t.classList.add("on");clearTimeout(toastT);toastT=setTimeout(function(){t.classList.remove("on")},2800);}
window.toast=toast;
$("#toTop").onclick=function(){scrollTo({top:0,behavior:window.reduced?"auto":"smooth"})};
var menuBtn=$("#menuBtn");
menuBtn.onclick=function(){var o=document.body.classList.toggle("menu-open");menuBtn.setAttribute("aria-expanded",o)};
$$(".mobile-menu a").forEach(function(a){a.onclick=function(){document.body.classList.remove("menu-open")}});
$("#themeBtn").onclick=function(){window.__setTheme(document.documentElement.getAttribute("data-theme")==="dark"?"light":"dark")};

/* SEO: JSON-LD dinámico de propiedades */
try{
  var s=document.createElement("script"); s.type="application/ld+json";
  s.text=JSON.stringify({"@@context":"https://schema.org","@@type":"ItemList","itemListElement":PROPS.map(function(p,i){return {"@@type":"ListItem","position":i+1,"item":{"@@type":"Residence","name":p.titulo,"description":p.desc,"address":{"@@type":"PostalAddress","addressLocality":"Cochabamba","addressCountry":"BO"}}}})});
  document.head.appendChild(s);
}catch(e){}

/* reveals + contadores */
try{
  var io=new IntersectionObserver(function(es){es.forEach(function(en){if(en.isIntersecting){en.target.classList.add("in");io.unobserve(en.target);}})},{threshold:.12});
  $$("[data-reveal]").forEach(function(el){io.observe(el)});
  var ioC=new IntersectionObserver(function(es){es.forEach(function(en){
    if(!en.isIntersecting)return; ioC.unobserve(en.target);
    var el=en.target,end=+el.dataset.count,t0=performance.now();
    if(window.reduced){el.textContent=end;return;}
    el.textContent="0";
    (function tick(t){var p=Math.min(1,(t-t0)/1800),e2=1-Math.pow(1-p,3);el.textContent=Math.round(end*e2);if(p<1)requestAnimationFrame(tick)})(t0);
  })},{threshold:.6});
  $$("[data-count]").forEach(function(el){ioC.observe(el)});
  document.body.classList.add("js");
  requestAnimationFrame(function(){document.body.classList.add("loaded")});
}catch(err){}
})();
</script>

<!-- ============ SCRIPT 2: MAPA ============ -->
<script src="https://unpkg.com/leaflet@@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
"use strict";
var $=window.$q, $$=window.$$q, PROPS=window.PROPS, TIPO_COLOR=window.TIPO_COLOR, TIPO_LABEL=window.TIPO_LABEL, fmt=window.fmt;
var VIEWS={all:[-17.389,-66.160,11.4],centro:[-17.395,-66.158,13.5],norte:[-17.376,-66.146,12.6],oeste:[-17.377,-66.185,12.6],sur:[-17.425,-66.149,13],valle:[-17.33,-66.22,10.6]};
var mapEl=$("#map"), useLeaflet=!!window.L, map=null, markers=null, markerById={}, pinById={}, currentView="all";
function shortPrice(p){
  if(p.tipo==="alquiler")return "Bs "+fmt(p.precio)+"/m";
  if(p.tipo==="anticretico")return "Bs "+Math.round(p.precio/1000)+"k";
  if(p.precio>=1e6)return "$us "+(p.precio/1e6).toFixed(1).replace(".0","")+"M";
  return "$us "+Math.round(p.precio/1000)+"k";
}
function initLeaflet(){
  map=L.map("map",{scrollWheelZoom:false});
  L.tileLayer("https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",{attribution:"© OpenStreetMap · © CARTO",maxZoom:20}).addTo(map);
  map.setView([VIEWS.all[0],VIEWS.all[1]],VIEWS.all[2]);
  markers=L.layerGroup().addTo(map);
  mapEl.addEventListener("click",function(e){var b=e.target.closest("[data-open]");if(b){map.closePopup();window.openModal(+b.dataset.open);}});
}
function project(p){
  var minLng=-66.30,maxLng=-66.11,minLat=-17.45,maxLat=-17.26;
  var x=((p.lng-minLng)/(maxLng-minLng))*100, y=((maxLat-p.lat)/(maxLat-minLat))*100;
  return [Math.min(96,Math.max(4,x)),Math.min(94,Math.max(5,y))];
}
function initLocal(){
  mapEl.innerHTML='<svg class="contours" viewBox="0 0 800 560" preserveAspectRatio="none" aria-hidden="true"><g fill="none" stroke="rgba(29,29,31,.08)" stroke-width="1.2"><path d="M-20,120 C160,60 300,180 470,120 S760,40 830,110"/><path d="M-20,230 C140,170 320,310 500,240 S740,160 830,240"/><path d="M-20,350 C180,280 340,430 520,350 S760,270 830,360"/><path d="M-20,470 C150,400 330,540 510,460 S750,380 830,470"/></g><g stroke="rgba(29,29,31,.05)"><line x1="200" y1="0" x2="200" y2="560"/><line x1="400" y1="0" x2="400" y2="560"/><line x1="600" y1="0" x2="600" y2="560"/><line x1="0" y1="180" x2="800" y2="180"/><line x1="0" y1="370" x2="800" y2="370"/></g><text x="24" y="536" font-family="monospace" font-size="11" fill="rgba(29,29,31,.4)">MAPA ILUSTRATIVO DEL VALLE · COCHABAMBA, BOLIVIA</text></svg><div id="pinLayer" style="position:absolute;inset:0"></div>';
}
try{ if(useLeaflet){initLeaflet();} else {initLocal();} }catch(err){ useLeaflet=false; initLocal(); }

window.renderMarkers=function(){
  var list=window.filtrar();
  if(useLeaflet&&map){
    markers.clearLayers(); markerById={};
    list.forEach(function(p){
      var icon=L.divIcon({className:"qmarker",html:'<span class="qpill" style="background:'+TIPO_COLOR[p.tipo]+'">'+shortPrice(p)+"</span>",iconSize:[0,0]});
      var mk=L.marker([p.lat,p.lng],{icon}).addTo(markers);
      mk.bindPopup('<span class="pop-t">'+p.titulo+"</span>"+p.zona+" · "+TIPO_LABEL[p.tipo]+" · "+p.imgs.length+' fotos<br><span class="pop-p">'+p.cur+" "+fmt(p.precio)+p.per+'</span><br><button class="link-btn" data-open="'+p.id+'">Ver ficha y galería →</button>',{closeButton:false});
      markerById[p.id]=mk;
    });
  }else{
    var layer=mapEl.querySelector("#pinLayer"); if(!layer)return;
    layer.innerHTML=""; pinById={};
    list.forEach(function(p){
      var xy=project(p), b=document.createElement("button");
      b.className="lpin"; b.dataset.id=p.id; b.dataset.g=p.grupo;
      b.style.left=xy[0]+"%"; b.style.top=xy[1]+"%";
      b.setAttribute("aria-label",p.titulo);
      b.innerHTML='<span class="qpill" style="background:'+TIPO_COLOR[p.tipo]+'">'+shortPrice(p)+'</span><span class="tip"><b>'+p.titulo+"</b><small>"+p.zona+" · "+TIPO_LABEL[p.tipo]+"</small><small>"+p.cur+" "+fmt(p.precio)+p.per+" · "+p.imgs.length+" fotos</small></span>";
      b.addEventListener("click",function(){window.openModal(p.id)});
      layer.appendChild(b); pinById[p.id]=b;
    });
    applyView(currentView);
  }
};
function applyView(v){
  currentView=v;
  if(useLeaflet&&map){var c=VIEWS[v];map.flyTo([c[0],c[1]],c[2],{duration:window.reduced?0:1});}
  else{Object.keys(pinById).forEach(function(id){var b=pinById[id];b.style.display=(v==="all"||b.dataset.g===v)?"":"none";});}
}
$("#flyChips").addEventListener("click",function(e){
  var c=e.target.closest(".chip"); if(!c)return;
  $$("#flyChips .chip").forEach(function(x){x.classList.toggle("active",x===c)});
  applyView(c.dataset.view);
});

/* toggle Pines / Google Maps embebido */
$("#modeChips").addEventListener("click",function(e){
  var c=e.target.closest(".chip"); if(!c)return;
  $$("#modeChips .chip").forEach(function(x){x.classList.toggle("active",x===c)});
  var sec=document.getElementById("mapa");
  if(c.dataset.mode==="google"){
    sec.classList.add("gmode");
    var fr=$("#gmapWrap iframe");
    if(fr.src==="about:blank"||!fr.src)fr.src="https://maps.google.com/maps?q=Cochabamba%2C%20Bolivia&z=13&output=embed";
  }else{
    sec.classList.remove("gmode");
    if(useLeaflet&&map)setTimeout(function(){map.invalidateSize()},200);
  }
});

window.showOnMap=function(id){
  var p=PROPS.filter(function(x){return x.id===id})[0]; if(!p)return;
  document.getElementById("mapa").scrollIntoView({behavior:window.reduced?"auto":"smooth"});
  setTimeout(function(){
    if(useLeaflet&&map&&markerById[id]){map.flyTo([p.lat,p.lng],15,{duration:window.reduced?0:1.1});setTimeout(function(){markerById[id].openPopup()},window.reduced?100:1200);}
    else if(pinById[id]){var b=pinById[id];b.classList.add("hot");setTimeout(function(){b.classList.remove("hot")},2600);}
  },300);
};
window.renderMarkers();
})();
</script>

<!-- ============ SCRIPT 3: EFECTOS DE SCROLL ============ -->
<script>
(function(){
"use strict";
var hsSec=document.getElementById("destacados"),hsTrack=document.getElementById("hsTrack"),heroImg=document.getElementById("heroImg");
var hsMax=0;
function hsLayout(){
  if(window.reduced||innerWidth<900){hsSec.style.height="";hsTrack.style.transform="";return;}
  hsMax=Math.max(0,hsTrack.scrollWidth-innerWidth+innerWidth*.06);
  hsSec.style.height=(hsMax+innerHeight)+"px";
}
var ticking=false;
function onScroll(){
  var y=scrollY;
  if(!window.reduced){
    if(heroImg&&y<document.querySelector(".hero").offsetHeight+300)heroImg.style.transform="translateY("+y*.12+"px) scale(1.08)";
    if(hsMax>0){var r=hsSec.getBoundingClientRect();
      if(r.top<innerHeight&&r.bottom>0){var prog=Math.min(1,Math.max(0,-r.top/(hsSec.offsetHeight-innerHeight)));hsTrack.style.transform="translate3d("+(-prog*hsMax)+"px,0,0)";}}
  }
  ticking=false;
}
addEventListener("scroll",function(){if(!ticking){requestAnimationFrame(onScroll);ticking=true;}},{passive:true});
addEventListener("resize",hsLayout);
addEventListener("load",function(){hsLayout();onScroll();});
hsLayout();onScroll();
})();
</script>
<script>
/* ====== PARCHE FICHA v6 ====== */
;(function(){
  console.log('[QASA] parche ficha v6 activo');
  var SVG = {
    youtube:  '<svg viewBox="0 0 24 24"><path d="M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 6.19C0 8.07 0 12 0 12s0 3.93.5 5.81a3.02 3.02 0 0 0 2.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14C24 15.93 24 12 24 12s0-3.93-.5-5.81zM9.55 15.57V8.43L15.82 12l-6.27 3.57z"/></svg>',
    tiktok:   '<svg viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>',
    instagram:'<svg viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 3.25.15 4.77 1.69 4.92 4.92.06 1.27.07 1.65.07 4.85 0 3.2-.01 3.58-.07 4.85-.15 3.23-1.66 4.77-4.92 4.92-1.27.06-1.64.07-4.85.07-3.2 0-3.58-.01-4.85-.07-3.26-.15-4.77-1.7-4.92-4.92-.06-1.27-.07-1.64-.07-4.85 0-3.2.01-3.58.07-4.85.15-3.23 1.66-4.77 4.92-4.92 1.27-.06 1.65-.07 4.85-.07zM12 0C8.74 0 8.33.01 7.05.07 2.7.27.27 2.69.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.2 4.36 2.62 6.78 6.98 6.98 1.28.06 1.69.07 4.95.07s3.67-.01 4.95-.07c4.36-.2 6.78-2.62 6.98-6.98.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95C21.73 2.69 19.31.27 14.95.07 13.67.01 13.26 0 12 0zm0 5.84A6.16 6.16 0 1 0 18.16 12 6.16 6.16 0 0 0 12 5.84zm0 10.16A4 4 0 1 1 16 12a4 4 0 0 1-4 4zm6.41-11.85a1.44 1.44 0 1 0 1.43 1.44 1.44 1.44 0 0 0-1.43-1.44z"/></svg>',
    facebook: '<svg viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>',
    play:     '<svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>'
  };
  function findProp(modal){
    var t = modal.querySelector('h1,h2,h3');
    var title = t ? t.textContent.trim() : '';
    var P = window.PROPS || [];
    for (var i=0;i<P.length;i++){ if (P[i].titulo === title) return P[i]; }
    return null;
  }
  function counterEl(modal){
    var els = modal.querySelectorAll('div,span,b');
    for (var i=0;i<els.length;i++){
      var t = (els[i].textContent||'').trim();
      if (/^\d+\s*\/\s*\d+$/.test(t) && els[i].children.length === 0) return els[i];
    }
    return null;
  }
  function navBtns(modal){
    var btns = modal.querySelectorAll('button'), prev = null, next = null;
    for (var i=0;i<btns.length;i++){
      var t = (btns[i].textContent||'').trim();
      if (t === '‹' || t === '<' || t === '←' || t === '❮') prev = btns[i];
      if (t === '›' || t === '>' || t === '→' || t === '❯') next = btns[i];
    }
    return {prev:prev, next:next};
  }
  function socialIcons(p){
    var S = (p && p.social) ? p.social : {}, out = '';
    if (S.youtube)   out += '<a class="qx-ico" title="YouTube" href="'+S.youtube+'" target="_blank" rel="noopener">'+SVG.youtube+'</a>';
    if (S.tiktok)    out += '<a class="qx-ico" title="TikTok" href="'+S.tiktok+'" target="_blank" rel="noopener">'+SVG.tiktok+'</a>';
    if (S.instagram) out += '<a class="qx-ico" title="Instagram" href="'+S.instagram+'" target="_blank" rel="noopener">'+SVG.instagram+'</a>';
    if (S.facebook)  out += '<a class="qx-ico" title="Facebook" href="'+S.facebook+'" target="_blank" rel="noopener">'+SVG.facebook+'</a>';
    return out;
  }
  function showVideo(st, ctr){
    st.videoActive = true; st.slide.style.display = 'block';
    if (st.main) st.main.style.opacity = '0';
    if (st.thumb) st.thumb.classList.add('on');
    if (ctr) ctr.textContent = (st.N+1) + ' / ' + (st.N+1);
    var v = st.slide.querySelector('video'); if (v && v.play) v.play();
  }
  function hideVideo(st, ctr){
    st.videoActive = false; st.slide.style.display = 'none';
    if (st.main) st.main.style.opacity = '1';
    if (st.thumb) st.thumb.classList.remove('on');
    if (ctr) ctr.textContent = st.idx + ' / ' + (st.N+1);
  }
  function enhance(){
    var nodes = document.querySelectorAll('body > div, body > section, body > dialog, body > aside');
    for (var m=0;m<nodes.length;m++){
      var modal = nodes[m];
      var cs = getComputedStyle(modal);
      if (cs.position !== 'fixed' || cs.display === 'none' || cs.visibility === 'hidden' || modal.offsetHeight < 300) continue;
      var btns = modal.querySelectorAll('button, a'), btn = null;
      for (var b=0;b<btns.length;b++){ if (btns[b].textContent.indexOf('Agendar visita') !== -1){ btn = btns[b]; break; } }
      if (!btn) continue;
      var p = findProp(modal);
      var cur = p ? p.titulo : '__sin__';
      if (modal.getAttribute('data-qx-title') === cur) continue;
      modal.setAttribute('data-qx-title', cur);
      console.log('[QASA] ficha enriquecida:', cur);

      var olds = modal.querySelectorAll('.qx-slide, .qx-thumb, .qx-extra, .qx-video-box, .qx-social-box');
      for (var o=0;o<olds.length;o++) olds[o].remove();
      modal.classList.add('qx-modal-fix');

      var ctr = counterEl(modal);
      var N = 0;
      if (ctr){ var m0 = (ctr.textContent||'').match(/(\d+)\s*\/\s*(\d+)/); N = m0 ? parseInt(m0[2],10) : 0; }
      var st = {N:N, idx:1, videoActive:false, slide:null, thumb:null, main:null, hasVideo:false};
      modal.__qxState = st;

      var vid = p ? (p.video || ((p.social && p.social.youtube) ? p.social.youtube : '')) : '';
      if (vid && N){
        var imgs = modal.querySelectorAll('img'), main = null, bestA = 0;
        for (var i2=0;i2<imgs.length;i2++){
          var a = imgs[i2].offsetWidth * imgs[i2].offsetHeight;
          if (a > bestA){ bestA = a; main = imgs[i2]; }
        }
        if (main){
          st.main = main;
          var wrap = main.parentElement; wrap.style.position = 'relative';
          var slide = document.createElement('div'); slide.className = 'qx-slide';
          if (vid.indexOf('youtu') !== -1){
            var mm = vid.match(/(?:youtu\.be\/|v=|embed\/)([\w-]{11})/);
            slide.innerHTML = mm ? '<iframe src="https://www.youtube.com/embed/'+mm[1]+'?autoplay=1&mute=1" allow="autoplay; encrypted-media" allowfullscreen></iframe>' : '';
          } else if (vid.indexOf('tiktok.com') !== -1 || vid.indexOf('instagram.com') !== -1){
            slide.innerHTML = '<div class="qx-slide-link"><a href="'+vid+'" target="_blank" rel="noopener">▶ Ver video en la red ↗</a></div>';
          } else {
            slide.innerHTML = '<video src="'+vid+'" muted loop playsinline controls autoplay></video>';
          }
          /* Fusible video */
          var vv = slide.querySelector('video');
          if (vv){ vv.addEventListener('error', function(){
            slide.remove(); if (th) th.remove();
            st.hasVideo = false; st.slide = null; st.thumb = null;
            var c2 = counterEl(modal); if (c2) c2.textContent = st.idx + ' / ' + st.N;
          }); }
          wrap.appendChild(slide); st.slide = slide; st.hasVideo = true;

          var others = [];
          for (var i3=0;i3<imgs.length;i3++){ if (imgs[i3] !== main) others.push(imgs[i3]); }
          if (others.length){
            var t0 = others[0];
            var strip = (t0.closest('button') ? t0.closest('button') : t0).parentElement;
            var th = document.createElement('button');
            th.type = 'button'; th.className = 'qx-thumb'; th.title = 'Video tour'; th.innerHTML = SVG.play;
            strip.appendChild(th); st.thumb = th;
          }
          var nb0 = navBtns(modal);
          [nb0.prev, nb0.next, ctr].forEach(function(el){ if (el) el.style.zIndex = '12'; });
          ctr.textContent = '1 / ' + (N+1);
        }
      }
      if (ctr && !modal.__qxObs){
        modal.__qxObs = true;
        new MutationObserver(function(){
          var s = modal.__qxState; if (!s || !s.N) return;
          var mm2 = (ctr.textContent||'').match(/(\d+)\s*\/\s*(\d+)/); if (!mm2) return;
          var k = parseInt(mm2[1],10), tot = parseInt(mm2[2],10);
          if (s.videoActive) return;
          if (s.slide && s.slide.style.display === 'block') hideVideo(s, ctr);
          s.idx = k;
          if (s.hasVideo && tot === s.N) ctr.textContent = k + ' / ' + (s.N+1);
        }).observe(ctr, {childList:true, characterData:true, subtree:true});
      }
      if (!modal.__qxNav){
        modal.__qxNav = true;
        modal.addEventListener('click', function(e){
          var s = modal.__qxState; if (!s || !s.hasVideo) return;
          var t = e.target.closest ? e.target.closest('button') : null; if (!t) return;
          if (s.thumb && t === s.thumb){ e.stopPropagation(); e.preventDefault(); showVideo(s, counterEl(modal)); return; }
          var nb = navBtns(modal);
          if (t === nb.next){
            if (s.videoActive){ hideVideo(s, counterEl(modal)); return; }
            if (s.idx >= s.N){ e.stopPropagation(); e.preventDefault(); showVideo(s, counterEl(modal)); return; }
          }
          if (t === nb.prev && s.videoActive){ e.stopPropagation(); e.preventDefault(); hideVideo(s, counterEl(modal)); return; }
        }, true);
      }
      var soc = socialIcons(p);
      if (soc){
        var panel = btn.parentElement;
        for (var up=0; up<5 && panel; up++){ if (panel.querySelector('h1,h2,h3')) break; panel = panel.parentElement; }
        var sb = document.createElement('div');
        sb.className = 'qx-extra qx-social-box';
        sb.innerHTML = '<span class="qx-label">Seguinos</span><div class="qx-social">'+soc+'</div>';
        (panel || modal).appendChild(sb);
      }
    }
  }
  if (window.MutationObserver){
    new MutationObserver(function(){ setTimeout(enhance, 60); })
      .observe(document.body, {childList:true, subtree:true, attributes:true, attributeFilter:['class','style','hidden']});
  }
  document.addEventListener('click', function(){ setTimeout(enhance, 120); setTimeout(enhance, 450); }, true);
})();
</script>
<script>
/* ====== PARCHE LIGHTBOX v7: click en la imagen de la ficha → visor fullscreen ====== */
;(function(){
  var lb=null, els={};
  function build(){
    lb=document.createElement('div');
    lb.className='qx-lb';
    lb.innerHTML=
      '<div class="qx-lb-top">'+
        '<div class="qx-lb-info"><b id="qxLbPrice"></b><span id="qxLbMeta"></span><span id="qxLbLoc"></span></div>'+
        '<div class="qx-lb-acts">'+
          '<button class="qx-lb-btn" id="qxLbFav" type="button">♥ Favorito</button>'+
          '<button class="qx-lb-btn" id="qxLbShare" type="button">Compartir</button>'+
          '<button class="qx-lb-x" id="qxLbClose" type="button" aria-label="Cerrar">✕</button>'+
        '</div>'+
      '</div>'+
      '<button class="qx-lb-nav prev" id="qxLbPrev" type="button" aria-label="Anterior">‹</button>'+
      '<div class="qx-lb-stage"><img id="qxLbImg" alt=""></div>'+
      '<button class="qx-lb-nav next" id="qxLbNext" type="button" aria-label="Siguiente">›</button>'+
      '<div class="qx-lb-count" id="qxLbCount"></div>';
    document.body.appendChild(lb);
    ['Price','Meta','Loc','Fav','Share','Close','Prev','Next','Img','Count'].forEach(function(k){ els[k]=lb.querySelector('#qxLb'+k); });

    els.Close.addEventListener('click', closeLb);
    els.Prev.addEventListener('click', function(){ nav('prev'); });
    els.Next.addEventListener('click', function(){ nav('next'); });
    lb.addEventListener('click', function(e){ if (e.target === lb || e.target.classList.contains('qx-lb-stage')) closeLb(); });
    els.Share.addEventListener('click', function(){
      var modal=openModal(); var p=modal?findProp(modal):null;
      var txt=(p?p.titulo:'Propiedad QASA')+' · '+els.Price.textContent;
      if (navigator.share){ navigator.share({title:'QASA', text:txt}).catch(function(){}); }
      else if (navigator.clipboard){ navigator.clipboard.writeText(txt).then(function(){ flash(els.Share,'¡Copiado!'); }); }
      else flash(els.Share,'—');
    });
    els.Fav.addEventListener('click', function(){
      var modal=openModal(); var p=modal?findProp(modal):null; if(!p) return;
      var favs=JSON.parse(localStorage.getItem('qasa_favs')||'[]');
      var i=favs.indexOf(p.id);
      if (i>-1) favs.splice(i,1); else favs.push(p.id);
      localStorage.setItem('qasa_favs', JSON.stringify(favs));
      paintFav(p.id);
      var pill=document.getElementById('favCount'); if (pill) pill.textContent=favs.length;
    });
    document.addEventListener('keydown', function(e){
      if (!lb.classList.contains('open')) return;
      if (e.key==='Escape') closeLb();
      if (e.key==='ArrowLeft') nav('prev');
      if (e.key==='ArrowRight') nav('next');
    });
  }
  function flash(btn,msg){ var old=btn.textContent; btn.textContent=msg; setTimeout(function(){ btn.textContent=old; },1400); }
  function openModal(){
    var nodes=document.querySelectorAll('body > div');
    for (var i=0;i<nodes.length;i++){
      var m=nodes[i], cs=getComputedStyle(m);
      if (cs.position==='fixed' && cs.display!=='none' && m.offsetHeight>300 && m.querySelector('#mImg')) return m;
    }
    return null;
  }
  function findProp(modal){
    var t=modal.querySelector('h1,h2,h3'); var title=t?t.textContent.trim():'';
    var P=window.PROPS||[];
    for (var i=0;i<P.length;i++){ if (P[i].titulo===title) return P[i]; }
    return null;
  }
  function paintFav(id){
    var favs=JSON.parse(localStorage.getItem('qasa_favs')||'[]');
    els.Fav.classList.toggle('on', favs.indexOf(id)>-1);
  }
  function sync(){
    var modal=openModal(); if(!modal) return;
    var main=modal.querySelector('#mImg');
    els.Img.src=main.src;
    var c=modal.querySelector('#gCount');
    els.Count.textContent=c?c.textContent.replace(/\s*/g,' '):'';
  }
  function nav(dir){
    var modal=openModal(); if(!modal) return;
    var b=modal.querySelector(dir==='prev' ? '#gPrev' : '#gNext');
    if (b) b.click();
    setTimeout(sync, 60);
  }
  function openLb(){
    if(!lb) build();
    var modal=openModal(); if(!modal) return;
    var p=findProp(modal);
    var precio=modal.querySelector('#mPrecio'), per=modal.querySelector('#mPer');
    var specs=modal.querySelector('#mSpecs'), loc=modal.querySelector('#mLoc');
    els.Price.textContent=(precio?precio.textContent:'')+(per&&per.textContent?per.textContent:'');
    els.Meta.textContent=specs?specs.textContent.replace(/\s+/g,' ').trim():'';
    els.Loc.textContent=loc?loc.textContent:'';
    if (p) paintFav(p.id);
    sync();
    lb.classList.add('open');
    document.body.style.overflow='hidden';
  }
  function closeLb(){
    if(!lb) return;
    lb.classList.remove('open');
    document.body.style.overflow='';
  }
  document.addEventListener('click', function(e){
    var t=e.target;
    if (t && t.closest && t.closest('.gal-main img')){ e.preventDefault(); openLb(); }
  }, true);
})();
</script>
</body>
</html>