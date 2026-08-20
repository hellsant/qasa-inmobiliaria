@extends('layouts.admin')
@section('title', 'Editar propiedad')

@section('content')
<div class="page-head">
  <div><h1>Editar: {{ $property->title }}</h1>
    <div class="sub"><a href="{{ route('property.show', $property) }}" target="_blank" style="color:var(--gold)">Ver ficha pública ↗</a></div>
  </div>
  <a href="{{ route('admin.properties.index') }}" class="btn btn-line">← Volver</a>
</div>

<div class="card">
  <form method="POST" action="{{ route('admin.properties.update', $property) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.properties._form')
  </form>
</div>

<div class="card">
  <h2>Galería de fotos ({{ $property->images->count() }})</h2>
  <form method="POST" action="{{ route('admin.properties.images.store', $property) }}" enctype="multipart/form-data"
        style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
    @csrf
    <input type="file" name="images[]" multiple accept="image/*" required>
    <button class="btn btn-primary" type="submit">Subir fotos</button>
  </form>

  <div class="img-grid">
    @foreach($property->images as $image)
      <figure>
        <img src="{{ $image->url }}" alt="">
        <div class="img-actions">
          @if($image->is_cover)
            <span class="cover-tag">★ Portada</span>
          @else
            <form method="POST" action="{{ route('admin.images.cover', $image) }}">
              @csrf @method('PATCH')
              <button type="submit">★ Portada</button>
            </form>
          @endif
          <form method="POST" action="{{ route('admin.images.destroy', $image) }}" data-confirm="¿Eliminar esta foto?">
            @csrf @method('DELETE')
            <button type="submit" class="danger">✕ Borrar</button>
          </form>
        </div>
      </figure>
    @endforeach
  </div>
</div>
@endsection