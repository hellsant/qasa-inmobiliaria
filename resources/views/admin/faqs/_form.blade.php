@php $f = $faq ?? null; @endphp
<label class="field"><span>Pregunta *</span><input type="text" name="question" required value="{{ old('question', $f->question ?? '') }}"></label>
<label class="field"><span>Respuesta *</span><textarea name="answer" required style="min-height:140px">{{ old('answer', $f->answer ?? '') }}</textarea></label>
<label class="field"><span>Orden</span><input type="number" min="0" name="sort" value="{{ old('sort', $f->sort ?? 0) }}"></label>
<button class="btn btn-gold" type="submit">{{ isset($f) ? 'Guardar' : 'Crear' }}</button>