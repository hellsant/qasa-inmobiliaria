@extends('layouts.admin')
@section('title', 'Equipo')
@section('content')
<div class="page-head">
  <div><h1>Equipo</h1><div class="sub">Se muestra en la sección Nosotros.</div></div>
  <a href="{{ route('admin.team.create') }}" class="btn btn-gold">+ Agregar miembro</a>
</div>
<div class="card">
  <table class="table">
    <thead><tr><th>Foto</th><th>Nombre</th><th>Cargo</th><th>Orden</th><th></th></tr></thead>
    <tbody>
      @foreach($members as $m)
        <tr>
          <td><img src="{{ $m->photo_url }}" class="avatar" alt=""></td>
          <td><b style="color:var(--ink)">{{ $m->name }}</b></td>
          <td>{{ $m->role }}</td>
          <td>{{ $m->sort }}</td>
          <td><div class="actions">
            <a href="{{ route('admin.team.edit', $m) }}" class="btn btn-sm btn-primary">Editar</a>
            <form method="POST" action="{{ route('admin.team.destroy', $m) }}" data-confirm="¿Eliminar?">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">✕</button></form>
          </div></td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="pagination">{{ $members->links('pagination::simple-default') }}</div>
</div>
@endsection