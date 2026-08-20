{{-- _form.blade.php --}}
@php $t = $testimonial ?? null; @endphp
<label class="field"><span>Testimonio *</span><textarea name="quote" required>{{ old('quote', $t->quote ?? '') }}</textarea></label>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px">
  <label class="field"><span>Autor *</span><input type="text" name="author" required value="{{ old('author', $t->author ?? '') }}"></label>
  <label class="field"><span>Operación · Zona</span>
    <div style="display:flex;gap:8px">
      <input type="text" name="operation" placeholder="VENTA" value="{{ old('operation', $t->operation ?? '') }}">
      <input type="text" name="location" placeholder="CALA CALA" value="{{ old('location', $t->location ?? '') }}">
    </div>
  </label>
</div>
<label class="field"><span>Foto</span><input type="file" name="photo" accept="image/*" data-preview="tPhoto">
  @if($t?->photo)<img id="tPhoto" src="{{ $t->photo_url }}" class="avatar" style="margin-top:8px">@else<img id="tPhoto" src="" class="avatar" style="display:none;margin-top:8px">@endif
</label>
<label class="field"><span>Orden</span><input type="number" min="0" name="sort" value="{{ old('sort', $t->sort ?? 0) }}"></label>
<button class="btn btn-gold" type="submit">{{ isset($t) ? 'Guardar' : 'Crear' }}</button>