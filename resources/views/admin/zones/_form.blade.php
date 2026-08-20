@php $z = $zone ?? null; @endphp
<label class="field"><span>Nombre *</span><input type="text" name="name" required value="{{ old('name', $z->name ?? '') }}" placeholder="Ej: Cala Cala"></label>
<label class="field"><span>Grupo *</span>
  <select name="group" required>
    @foreach(\App\Models\Zone::GROUPS as $val => $label)
      <option value="{{ $val }}" @selected(old('group', $z->group ?? '') === $val)>{{ $label }}</option>
    @endforeach
  </select>
</label>
<label class="field"><span>Precio referencial m² ($us)</span><input type="number" step="0.01" min="0" name="price_m2" value="{{ old('price_m2', $z->price_m2 ?? '') }}"></label>
<label class="check" style="margin-bottom:20px"><input type="checkbox" name="active" value="1" @checked(old('active', $z->active ?? true))> Activa (visible en el sitio)</label>
<button class="btn btn-gold" type="submit">{{ isset($z) ? 'Guardar' : 'Crear zona' }}</button>