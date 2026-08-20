@extends('layouts.admin')
@section('title', 'Zonas')
@section('content')
<div class="page-head">
  <div><h1>Zonas</h1><div class="sub">Se muestran en el mapa y en la sección “El valle, cuadra por cuadra”.</div></div>
  <a href="{{ route('admin.zones.create') }}" class="btn btn-gold">+ Nueva zona</a>
</div>
<div class="card">
  <table class="table">
    <thead><tr><th>Zona</th><th>Grupo</th><th>$us / m²</th><th>Propiedades</th><th>Activa</th><th></th></tr></thead>
    <tbody>
      @foreach($zones as $zone)
        <tr>
          <td><b style="color:var(--ink)">{{ $zone->name }}</b></td>
          <td>{{ $zone->group_label }}</td>
          <td>{{ number_format($zone->price_m2 ?? 0, 0, ',', '.') }}</td>
          <td>{{ $zone->properties_count }}</td>
          <td><span class="badge {{ $zone->active ? 'badge-ok' : 'badge-off' }}">{{ $zone->active ? 'Sí' : 'No' }}</span></td>
          <td><div class="actions">
            <a href="{{ route('admin.zones.edit', $zone) }}" class="btn btn-sm btn-primary">Editar</a>
            <form method="POST" action="{{ route('admin.zones.destroy', $zone) }}" data-confirm="¿Eliminar esta zona?">
              @csrf @method('DELETE')<button class="btn btn-sm btn-danger">✕</button>
            </form>
          </div></td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="pagination">{{ $zones->links('pagination::simple-default') }}</div>
</div>
@endsection