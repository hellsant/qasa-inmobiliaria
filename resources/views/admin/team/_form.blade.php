{{-- _form.blade.php --}}
@php $m = $member ?? null; @endphp
<label class="field"><span>Nombre *</span><input type="text" name="name" required value="{{ old('name', $m->name ?? '') }}"></label>
<label class="field"><span>Cargo *</span><input type="text" name="role" required value="{{ old('role', $m->role ?? '') }}" placeholder="Ej: Jefe de Ventas"></label>
<label class="field"><span>Foto</span><input type="file" name="photo" accept="image/*">
  @if($m?->photo)<img src="{{ $m->photo_url }}" class="avatar" style="margin-top:8px">@endif
</label>
<label class="field"><span>Orden</span><input type="number" min="0" name="sort" value="{{ old('sort', $m->sort ?? 0) }}"></label>
<button class="btn btn-gold" type="submit">{{ isset($m) ? 'Guardar' : 'Agregar' }}</button>