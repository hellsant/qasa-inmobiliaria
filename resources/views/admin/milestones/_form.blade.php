@php $ms = $milestone ?? null; @endphp
<label class="field"><span>Año *</span><input type="number" name="year" min="1990" max="2100" required value="{{ old('year', $ms->year ?? '') }}"></label>
<label class="field"><span>Descripción *</span><textarea name="description" required>{{ old('description', $ms->description ?? '') }}</textarea></label>
<button class="btn btn-gold" type="submit">{{ isset($ms) ? 'Guardar' : 'Crear' }}</button>