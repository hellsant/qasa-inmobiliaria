@php $p = $property ?? null; @endphp
<fieldset>
  <legend>Datos básicos</legend>
  <div class="form-grid">
    <label class="field span2"><span>Título *</span>
      <input type="text" name="title" required value="{{ old('title', $p->title ?? '') }}" placeholder="Ej: Residencia moderna · Zona Norte">
    </label>
    <label class="field"><span>Zona</span>
      <select name="zone_id">
        <option value="">Sin zona</option>
        @foreach($zones as $zone)
          <option value="{{ $zone->id }}" @selected(old('zone_id', $p->zone_id ?? '') == $zone->id)>
            {{ $zone->name }} ({{ $zone->group_label }})
          </option>
        @endforeach
      </select>
    </label>
    <label class="field"><span>Operación *</span>
      <select name="operation" required>
        @foreach(\App\Models\Property::OPERATIONS as $val => $label)
          <option value="{{ $val }}" @selected(old('operation', $p->operation ?? '') === $val)>{{ $label }}</option>
        @endforeach
      </select>
    </label>
    <label class="field"><span>Tipo *</span>
      <select name="type" required>
        @foreach(\App\Models\Property::TYPES as $val => $label)
          <option value="{{ $val }}" @selected(old('type', $p->type ?? '') === $val)>{{ $label }}</option>
        @endforeach
      </select>
    </label>
    <label class="field"><span>Estado</span>
      <select name="status">
        @foreach(['disponible' => 'Disponible', 'reservada' => 'Reservada', 'vendida' => 'Vendida / Alquilada'] as $val => $label)
          <option value="{{ $val }}" @selected(old('status', $p->status ?? 'disponible') === $val)>{{ $label }}</option>
        @endforeach
      </select>
    </label>
  </div>
</fieldset>

<fieldset>
  <legend>Precio</legend>
  <div class="form-grid">
    <label class="field"><span>Precio *</span>
      <input type="number" step="0.01" min="0" name="price" required value="{{ old('price', $p->price ?? '') }}">
    </label>
    <label class="field"><span>Moneda</span>
      <select name="currency">
        <option value="USD" @selected(old('currency', $p->currency ?? 'USD') === 'USD')>Dólares ($us)</option>
        <option value="BS" @selected(old('currency', $p->currency ?? '') === 'BS')>Bolivianos (Bs)</option>
      </select>
    </label>
    <label class="field"><span>Sufijo (opcional)</span>
      <input type="text" name="price_suffix" value="{{ old('price_suffix', $p->price_suffix ?? '') }}" placeholder="Ej: /mes">
    </label>
  </div>
</fieldset>

<fieldset>
  <legend>Características</legend>
  <div class="form-grid">
    <label class="field"><span>Dormitorios</span><input type="number" min="0" name="bedrooms" value="{{ old('bedrooms', $p->bedrooms ?? 0) }}"></label>
    <label class="field"><span>Baños</span><input type="number" min="0" name="bathrooms" value="{{ old('bathrooms', $p->bathrooms ?? 0) }}"></label>
    <label class="field"><span>Superficie (m²)</span><input type="number" step="0.01" min="0" name="area_m2" value="{{ old('area_m2', $p->area_m2 ?? '') }}"></label>
    <label class="field"><span>Garajes</span><input type="number" min="0" name="parking" value="{{ old('parking', $p->parking ?? 0) }}"></label>
    <label class="field span2"><span>Dirección</span><input type="text" name="address" value="{{ old('address', $p->address ?? '') }}"></label>
    <label class="field span3"><span>Detalles (uno por línea)</span>
      <textarea name="features" placeholder="Cocina equipada&#10;Patio / jardín&#10;Documentación al día">{{ old('features', isset($p) ? implode("\n", $p->features ?? []) : '') }}</textarea>
    </label>
    <label class="field span3"><span>Descripción</span>
      <textarea name="description" style="min-height:140px">{{ old('description', $p->description ?? '') }}</textarea>
    </label>
  </div>
</fieldset>

<fieldset>
  <legend>Mapa y multimedia</legend>
  <div class="form-grid">
    <label class="field"><span>Latitud</span><input type="number" step="0.0000001" name="lat" value="{{ old('lat', $p->lat ?? '') }}" placeholder="-17.3705"></label>
    <label class="field"><span>Longitud</span><input type="number" step="0.0000001" name="lng" value="{{ old('lng', $p->lng ?? '') }}" placeholder="-66.1502"></label>
    <label class="field"><span>URL de video (opcional)</span><input type="url" name="video_url" value="{{ old('video_url', $p->video_url ?? '') }}"></label>
<label class="field"><span>O subí un video (mp4/webm)</span><input type="file" name="video_file" accept="video/mp4,video/webm"><small>Se reproduce solo (muteado) en “Video tours”.</small></label>
    <label class="field span3"><span>Fotos @isset($p)(para agregar más en una propiedad existente usá la sección de abajo)@endisset</span>
      <input type="file" name="images[]" multiple accept="image/*">
      <small>Podés seleccionar varias a la vez. La primera será la portada si no hay ninguna.</small>
    </label>
  </div>

<fieldset style="grid-column:1/-1;margin-top:8px">
    <legend>Redes sociales de la propiedad (botones en la ficha)</legend>
    <div class="form-grid">
        <label class="field"><span>TikTok (URL)</span><input type="text" name="social_tiktok" placeholder="https://tiktok.com/..." value="{{ old('social_tiktok', $p->social_tiktok ?? '') }}"></label>
        <label class="field"><span>Instagram (URL)</span><input type="text" name="social_instagram" placeholder="https://instagram.com/..." value="{{ old('social_instagram', $p->social_instagram ?? '') }}"></label>
        <label class="field"><span>Facebook (URL)</span><input type="text" name="social_facebook" placeholder="https://facebook.com/..." value="{{ old('social_facebook', $p->social_facebook ?? '') }}"></label>
        <label class="field"><span>YouTube (URL) — se usa como video tour si no subiste video</span><input type="text" name="social_youtube" placeholder="https://youtube.com/watch?v=..." value="{{ old('social_youtube', $p->social_youtube ?? '') }}"></label>
    </div>
</fieldset>
</fieldset>

<fieldset>
  <legend>Visibilidad</legend>
  <div style="display:flex;gap:34px;flex-wrap:wrap">
    <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $p->is_active ?? true))> Publicada (visible en el sitio)</label>
    <label class="check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $p->is_featured ?? false))> Destacada de la semana</label>
  </div>
</fieldset>

<button class="btn btn-gold" type="submit" style="padding:14px 34px">{{ isset($p) ? 'Guardar cambios' : 'Crear propiedad' }}</button>