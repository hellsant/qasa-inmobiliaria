<article class="prop-card"
  data-operation="{{ $property->operation }}"
  data-type="{{ $property->type }}"
  data-zone="{{ $property->zone->group ?? '' }}"
  data-price="{{ $property->price }}">
  <button class="fav-btn" type="button" data-fav="{{ $property->id }}" aria-label="Guardar en favoritos" title="Guardar en favoritos">♡</button>
  <a class="prop-media" href="{{ route('property.show', $property) }}">
    <img src="{{ $property->cover_url }}" alt="{{ $property->title }}" loading="lazy">
    <span class="op-badge op-{{ $property->operation }}">{{ $property->operation_label }}</span>
    <span class="photo-count">✦ {{ $property->images->count() }} fotos</span>
  </a>
  <div class="prop-body">
    <div class="prop-price">{{ $property->price_label }}</div>
    <h3 class="prop-title"><a href="{{ route('property.show', $property) }}">{{ $property->title }}</a></h3>
    <div class="prop-zone">📍 {{ $property->zone->name ?? 'Cochabamba' }} · {{ $property->type_label }}</div>
    <div class="prop-specs">
      @if($property->bedrooms)<span>🛏 {{ $property->bedrooms }} dorm.</span>@endif
      @if($property->bathrooms)<span>🛁 {{ $property->bathrooms }} baños</span>@endif
      @if($property->area_m2)<span>📐 {{ number_format($property->area_m2, 0, ',', '.') }} m²</span>@endif
      @if($property->parking)<span>🚗 {{ $property->parking }} garaje</span>@endif
    </div>
  </div>
</article>