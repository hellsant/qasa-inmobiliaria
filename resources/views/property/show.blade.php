@extends('layouts.public')

@section('title', $property->title . ' · QASA')

@section('content')
@php use App\Models\Setting; $wa = Setting::get('contact_whatsapp', '59170012345'); @endphp

<section class="pd">
  <div class="container">
    <div class="pd-head">
      <div>
        <span class="op-badge op-{{ $property->operation }}" style="position:static;display:inline-block;margin-bottom:12px">{{ $property->operation_label }}</span>
        <h1>{{ $property->title }}</h1>
        <div class="pd-zone">📍 {{ $property->zone->name ?? 'Cochabamba' }} · {{ $property->type_label }} @if($property->address) · {{ $property->address }} @endif</div>
      </div>
      <div class="pd-price">{{ $property->price_label }}</div>
    </div>

    <figure class="gallery-main">
      <img id="galleryMain" src="{{ $property->images->first()?->url ?? $property::FALLBACK_IMG }}" alt="{{ $property->title }}">
    </figure>
    <div class="gallery-thumbs">
      @foreach($property->images as $i => $image)
        <button class="{{ $i === 0 ? 'active' : '' }}" data-full="{{ $image->url }}" type="button">
          <img src="{{ $image->url }}" alt="Foto {{ $i + 1 }}" loading="lazy">
        </button>
      @endforeach
    </div>

    <div class="pd-layout">
      <div>
        <div class="pd-block">
          <h2>Características principales</h2>
          <div class="spec-grid">
            @if($property->bedrooms)<div class="spec"><b>{{ $property->bedrooms }}</b><span>Dormitorios</span></div>@endif
            @if($property->bathrooms)<div class="spec"><b>{{ $property->bathrooms }}</b><span>Baños</span></div>@endif
            @if($property->area_m2)<div class="spec"><b>{{ number_format($property->area_m2, 0, ',', '.') }}</b><span>m²</span></div>@endif
            @if($property->parking)<div class="spec"><b>{{ $property->parking }}</b><span>Garaje</span></div>@endif
            <div class="spec"><b>{{ ucfirst($property->status) }}</b><span>Estado</span></div>
          </div>
        </div>

        @if($property->features)
          <div class="pd-block">
            <h2>Detalles</h2>
            <div class="feat-list">
              @foreach($property->features as $feature)<span>{{ $feature }}</span>@endforeach
            </div>
          </div>
        @endif

        @if($property->description)
          <div class="pd-block">
            <h2>Descripción</h2>
            <p style="font-size:14.5px">{!! nl2br(e($property->description)) !!}</p>
          </div>
        @endif

        @if($property->lat && $property->lng)
          <div class="pd-block pd-map">
            <h2>Ubicación</h2>
            <iframe loading="lazy" src="https://www.google.com/maps?q={{ $property->lat }},{{ $property->lng }}&z=16&output=embed" title="Mapa"></iframe>
          </div>
        @endif
      </div>

      <aside class="pd-side">
        <div class="pd-block">
          <h2>¿Querés visitarla?</h2>
          <p style="font-size:14px;margin-bottom:18px">Coordinamos la visita en menos de 48 h. Las visitas son gratuitas.</p>
          <div class="pd-cta">
            <a class="btn btn-gold" target="_blank"
               href="https://wa.me/{{ $wa }}?text={{ urlencode('Hola QASA, me interesa: ' . $property->title . ' (' . $property->price_label . ')') }}">💬 WhatsApp directo</a>
            <a class="btn btn-pine" href="{{ route('home') }}#contacto">Agendar visita</a>
          </div>
        </div>

        @if($similar->count())
          <div class="pd-block">
            <h2>Propiedades similares</h2>
            @foreach($similar as $sim)
              <a href="{{ route('property.show', $sim) }}" style="display:flex;gap:12px;margin-bottom:14px;align-items:center">
                <img src="{{ $sim->cover_url }}" style="width:80px;height:60px;object-fit:cover;border-radius:10px" alt="{{ $sim->title }}">
                <div>
                  <b style="display:block;font-size:13.5px;color:var(--ink)">{{ $sim->title }}</b>
                  <span style="font-size:13px;color:var(--pine-2);font-weight:700">{{ $sim->price_label }}</span>
                </div>
              </a>
            @endforeach
          </div>
        @endif
      </aside>
    </div>
  </div>
</section>

<div class="lightbox" id="lightbox" hidden>
  <button class="lb-btn lb-close" id="lbClose" type="button">✕</button>
  <button class="lb-btn lb-prev" id="lbPrev" type="button">‹</button>
  <img id="lbImg" src="" alt="Galería">
  <button class="lb-btn lb-next" id="lbNext" type="button">›</button>
  <span class="lb-count" id="lbCount"></span>
</div>
@endsection