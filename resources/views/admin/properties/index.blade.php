@extends('layouts.admin')
@section('title', 'Propiedades')

@section('content')
<div class="page-head">
  <div><h1>Propiedades</h1><div class="sub">{{ $properties->total() }} propiedades en total</div></div>
  <a href="{{ route('admin.properties.create') }}" class="btn btn-gold">+ Nueva propiedad</a>
</div>

<div class="card">
  <form method="GET" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por título…"
           style="flex:1;min-width:200px;border:1.5px solid var(--line);border-radius:10px;padding:10px 14px;font-family:inherit">
    <select name="op" style="border:1.5px solid var(--line);border-radius:10px;padding:10px 14px;font-family:inherit">
      <option value="">Todas las operaciones</option>
      @foreach(\App\Models\Property::OPERATIONS as $val => $label)
        <option value="{{ $val }}" @selected(request('op') === $val)>{{ $label }}</option>
      @endforeach
    </select>
    <button class="btn btn-primary">Filtrar</button>
  </form>

  <table class="table">
    <thead><tr><th>Foto</th><th>Propiedad</th><th>Operación</th><th>Precio</th><th>Fotos</th><th>Estado</th><th>Destacada</th><th></th></tr></thead>
    <tbody>
      @forelse($properties as $property)
        <tr>
          <td><img src="{{ $property->cover_url }}" class="thumb" alt=""></td>
          <td>
            <b style="color:var(--ink)">{{ $property->title }}</b><br>
            <small style="color:var(--muted)">{{ $property->zone->name ?? 'Sin zona' }} · {{ $property->type_label }}</small>
          </td>
          <td><span class="badge badge-{{ $property->operation }}">{{ $property->operation_label }}</span></td>
          <td><b>{{ $property->price_label }}</b></td>
          <td>{{ $property->images->count() }}</td>
          <td>
            <span class="badge {{ $property->is_active ? 'badge-ok' : 'badge-off' }}">
              {{ $property->is_active ? ucfirst($property->status) : 'Oculta' }}
            </span>
          </td>
          <td>
            <form method="POST" action="{{ route('admin.properties.featured', $property) }}">
              @csrf @method('PATCH')
              <button class="btn btn-sm {{ $property->is_featured ? 'btn-gold' : 'btn-line' }}" type="submit">
                {{ $property->is_featured ? '★ Sí' : '☆ No' }}
              </button>
            </form>
          </td>
          <td>
            <div class="actions">
              <a href="{{ route('admin.properties.edit', $property) }}" class="btn btn-sm btn-primary">Editar</a>
              <form method="POST" action="{{ route('admin.properties.destroy', $property) }}" data-confirm="¿Eliminar esta propiedad y todas sus fotos?">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger" type="submit">✕</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted)">No hay propiedades. ¡Creá la primera!</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="pagination">{{ $properties->links('pagination::simple-default') }}</div>
</div>
@endsection