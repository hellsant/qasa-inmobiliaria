@extends('layouts.admin')
@section('title', 'Configuración')

@section('content')
@php
    use App\Models\Setting;
    $s = fn(string $key, string $default = '') => old("settings.$key", Setting::get($key, $default));
@endphp

<div class="page-head">
    <div><h1>Configuración del sitio</h1><div class="sub">Todos los textos e imágenes de la landing.</div></div>
    <button class="btn btn-gold" form="settingsForm" type="submit">Guardar todo</button>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm" enctype="multipart/form-data">
    @csrf @method('PUT')

 {{-- ================= HERO ================= --}}
<fieldset class="card">
    <legend>Hero principal</legend>
    <div class="form-grid">
        <label class="field"><span>Etiqueta superior (kicker)</span><input type="text" name="settings[hero_kicker]" value="{{ $s('hero_kicker', 'Inmobiliaria · Cochabamba, Bolivia') }}"></label>
        <label class="field"><span>Título línea 1</span><input type="text" name="settings[hero_title_1]" value="{{ $s('hero_title_1', 'Tu próximo hogar') }}"></label>
        <label class="field"><span>Título línea 2</span><input type="text" name="settings[hero_title_2]" value="{{ $s('hero_title_2', 'ya tiene') }}"></label>
        <label class="field"><span>Título línea 3 (itálica)</span><input type="text" name="settings[hero_title_3]" value="{{ $s('hero_title_3', 'dirección.') }}"></label>
        <label class="field span3"><span>Subtítulo</span><textarea name="settings[hero_text]" style="min-height:80px">{{ $s('hero_text') }}</textarea></label>
        <label class="field"><span>Caption: título</span><input type="text" name="settings[hero_caption_title]" value="{{ $s('hero_caption_title', 'Residencia moderna · Zona Norte') }}"></label>
        <label class="field span2"><span>Caption: texto</span><input type="text" name="settings[hero_caption_text]" value="{{ $s('hero_caption_text', 'Galería completa en la ficha · tocá para ver destacadas') }}"></label>
        <label class="field span2"><span>Imagen de fondo (URL)</span><input type="url" name="settings[hero_image]" value="{{ $s('hero_image') }}"></label>
        <label class="field"><span>O subí una imagen nueva</span><input type="file" name="hero_image_file" accept="image/*"></label>
        @if(Setting::get('hero_image'))
            <div class="span3"><img src="{{ Setting::get('hero_image') }}" alt="Hero actual" style="max-height:140px;border-radius:12px;margin-top:6px"></div>
        @endif
    </div>
</fieldset>

    {{-- ================= STATS ================= --}}
    <fieldset class="card">
        <legend>Números (stats)</legend>
        <div class="form-grid">
            <label class="field"><span>Años en el valle</span><input type="text" name="settings[stat_years]" value="{{ $s('stat_years') }}"></label>
            <label class="field"><span>Operaciones cerradas</span><input type="text" name="settings[stat_operations]" value="{{ $s('stat_operations') }}"></label>
            <label class="field"><span>Propiedades activas</span><input type="text" name="settings[stat_properties]" value="{{ $s('stat_properties') }}"></label>
            <label class="field"><span>% que recomienda</span><input type="text" name="settings[stat_recommend]" value="{{ $s('stat_recommend') }}"></label>
        </div>
    </fieldset>

    {{-- ================= OPERACIONES ================= --}}
    <fieldset class="card">
        <legend>Operaciones (Venta / Alquiler / Anticrético)</legend>
        @foreach([
            'venta' => 'Venta', 'alquiler' => 'Alquiler', 'anticretico' => 'Anticrético',
        ] as $key => $label)
            <div style="border-bottom:1px dashed var(--line);padding-bottom:16px;margin-bottom:16px">
                <b style="display:block;margin-bottom:10px;color:var(--pine-2)">{{ $label }}</b>
                <div class="form-grid">
                    <label class="field"><span>Precio “desde”</span><input type="text" name="settings[op_{{ $key }}_price]" value="{{ $s("op_{$key}_price") }}"></label>
                    <label class="field"><span>Imagen (URL)</span><input type="url" name="settings[op_{{ $key }}_image]" value="{{ $s("op_{$key}_image") }}"></label>
                    <label class="field"><span>O subir imagen</span><input type="file" name="op_{{ $key }}_image_file" accept="image/*"></label>
                    <label class="field span3"><span>Descripción</span><textarea name="settings[op_{{ $key }}_desc]" style="min-height:70px">{{ $s("op_{$key}_desc") }}</textarea></label>
                    @if($key === 'anticretico')
                        <label class="field span3"><span>Puntos fuertes (uno por línea)</span><textarea name="settings[op_anticretico_points]" style="min-height:80px">{{ $s('op_anticretico_points') }}</textarea></label>
                    @endif
                </div>
            </div>
        @endforeach
    </fieldset>

    {{-- ================= NOSOTROS ================= --}}
    <fieldset class="card">
        <legend>Nosotros</legend>
        <div class="form-grid">
            <label class="field span3"><span>Título</span><input type="text" name="settings[about_title]" value="{{ $s('about_title') }}"></label>
            <label class="field span3"><span>Texto (separá párrafos con línea en blanco)</span><textarea name="settings[about_text]" style="min-height:120px">{{ $s('about_text') }}</textarea></label>
            <label class="field"><span>Foto grande (URL)</span><input type="url" name="settings[about_image_1]" value="{{ $s('about_image_1') }}"></label>
            <label class="field"><span>O subir foto grande</span><input type="file" name="about_image_1_file" accept="image/*"></label>
            <label class="field"><span>Foto chica (URL)</span><input type="url" name="settings[about_image_2]" value="{{ $s('about_image_2') }}"></label>
            <label class="field"><span>O subir foto chica</span><input type="file" name="about_image_2_file" accept="image/*"></label>
        </div>
    </fieldset>

    {{-- ================= PROPIETARIOS ================= --}}
    <fieldset class="card">
        <legend>Propietarios</legend>
        <div class="form-grid">
            <label class="field span2"><span>Título banner</span><input type="text" name="settings[owner_cta_title]" value="{{ $s('owner_cta_title') }}"></label>
            <label class="field span3"><span>Texto banner</span><textarea name="settings[owner_cta_text]" style="min-height:70px">{{ $s('owner_cta_text') }}</textarea></label>
        <label class="field span3"><span>Párrafo debajo del título</span><input type="text" name="settings[owner_cta_desc]" value="{{ $s('owner_cta_desc') }}"></label>
        </div>
        @foreach([
            'sell' => 'Pestaña Vender', 'rent' => 'Pestaña Alquilar', 'anti' => 'Pestaña Anticrético',
        ] as $key => $label)
            <div style="border-top:1px dashed var(--line);padding-top:14px;margin-top:14px">
                <b style="display:block;margin-bottom:10px;color:var(--pine-2)">{{ $label }}</b>
                <div class="form-grid">
                    <label class="field span3"><span>Título</span><input type="text" name="settings[owner_{{ $key }}_title]" value="{{ $s("owner_{$key}_title") }}"></label>
                    <label class="field span3"><span>Descripción</span><textarea name="settings[owner_{{ $key }}_desc]" style="min-height:70px">{{ $s("owner_{$key}_desc") }}</textarea></label>
                    <label class="field span2"><span>Beneficios (uno por línea)</span><textarea name="settings[owner_{{ $key }}_points]" style="min-height:90px">{{ $s("owner_{$key}_points") }}</textarea></label>
                    <label class="field"><span>Dato destacado</span><input type="text" name="settings[owner_{{ $key }}_stat]" value="{{ $s("owner_{$key}_stat") }}"></label>
                </div>
            </div>
        @endforeach
    </fieldset>

    {{-- ================= CONTACTO Y FOOTER ================= --}}
    <fieldset class="card">
        <legend>Contacto y footer</legend>
        <div class="form-grid">
            <label class="field span3"><span>Dirección</span><input type="text" name="settings[contact_address]" value="{{ $s('contact_address') }}"></label>
            <label class="field"><span>WhatsApp (solo números con código de país)</span><input type="text" name="settings[contact_whatsapp]" value="{{ $s('contact_whatsapp') }}"></label>
            <label class="field"><span>Teléfono fijo</span><input type="text" name="settings[contact_phone]" value="{{ $s('contact_phone') }}"></label>
            <label class="field"><span>Email</span><input type="email" name="settings[contact_email]" value="{{ $s('contact_email') }}"></label>
            <label class="field"><span>Horario</span><input type="text" name="settings[contact_hours]" value="{{ $s('contact_hours') }}"></label>
            <label class="field span3"><span>Texto del footer</span><input type="text" name="settings[footer_text]" value="{{ $s('footer_text') }}"></label>
        </div>
    </fieldset>

    <fieldset class="card">
    <legend>Redes sociales</legend>
    <div class="form-grid">
        <label class="field"><span>TikTok (URL)</span><input type="url" name="settings[social_tiktok]" value="{{ $s('social_tiktok') }}"></label>
        <label class="field"><span>Instagram (URL)</span><input type="url" name="settings[social_instagram]" value="{{ $s('social_instagram') }}"></label>
        <label class="field"><span>Facebook (URL)</span><input type="url" name="settings[social_facebook]" value="{{ $s('social_facebook') }}"></label>
    </div>
</fieldset>
{{-- ================= PROCESO (4 pasos) ================= --}}
<fieldset class="card">
    <legend>Proceso (4 pasos)</legend>
    @foreach([1 => 'Descubrí', 2 => 'Visitá', 3 => 'Negociá', 4 => 'Firmá'] as $num => $default)
        <div style="border-bottom:1px dashed var(--line);padding-bottom:12px;margin-bottom:12px">
            <b style="display:block;margin-bottom:8px">Paso {{ $num }}</b>
            <div class="form-grid">
                <label class="field"><span>Título</span><input type="text" name="settings[step_{{ $num }}_title]" value="{{ $s("step_{$num}_title", $default) }}"></label>
                <label class="field span2"><span>Descripción</span><textarea name="settings[step_{{ $num }}_desc]" style="min-height:60px">{{ $s("step_{$num}_desc") }}</textarea></label>
            </div>
        </div>
    @endforeach
</fieldset>

{{-- ================= CTA BAND ================= --}}
<fieldset class="card">
    <legend>Banner "¿Tenés una propiedad?"</legend>
    <div class="form-grid">
        <label class="field span3"><span>Título</span><input type="text" name="settings[owner_cta_title]" value="{{ $s('owner_cta_title', '¿Tenés una propiedad?') }}"></label>
        <label class="field span3"><span>Texto (itálica)</span><input type="text" name="settings[owner_cta_text]" value="{{ $s('owner_cta_text', 'Publicala con nosotros.') }}"></label>
    </div>
</fieldset>
<fieldset class="card">
    <legend>Footer & legal</legend>
    <div class="form-grid">
        <label class="field span3"><span>Texto de marca (footer)</span><textarea name="settings[footer_text]" style="min-height:60px">{{ $s('footer_text', 'Inmobiliaria cochabambina. Compra, venta, alquiler y anticrético con papeles claros desde 2012.') }}</textarea></label>
        <label class="field"><span>Copyright (año automático si lo dejás vacío)</span><input type="text" name="settings[footer_copy]" value="{{ $s('footer_copy') }}" placeholder="© 2026 QASA · Cochabamba, Bolivia"></label>
        <label class="field span2"><span>Línea de coordenadas / nota</span><input type="text" name="settings[footer_note]" value="{{ $s('footer_note') }}" placeholder="17.3895° S, 66.1566° W · Cochabamba, Bolivia"></label>
        <label class="field"><span>URL Términos de uso</span><input type="text" name="settings[legal_terms]" value="{{ $s('legal_terms') }}" placeholder="# o https://…"></label>
        <label class="field"><span>URL Privacidad</span><input type="text" name="settings[legal_privacy]" value="{{ $s('legal_privacy') }}" placeholder="# o https://…"></label>
        <label class="field"><span>URL Registro FUNDEMPRESA</span><input type="text" name="settings[legal_registry]" value="{{ $s('legal_registry') }}" placeholder="# o https://…"></label>
    </div>
</fieldset>
<button class="btn btn-gold" type="submit" style="padding:14px 40px">Guardar configuración</button>
</form>
@endsection