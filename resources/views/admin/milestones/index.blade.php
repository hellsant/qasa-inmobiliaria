@extends('layouts.admin')
@section('title', 'Hitos')
@section('content')
<div class="page-head">
  <div><h1>Hitos (línea de tiempo)</h1></div>
  <a href="{{ route('admin.milestones.create') }}" class="btn btn-gold">+ Nuevo hito</a>
</div>
<div class="card">
  <table class="table">
    <thead><tr><th>Año</th><th>Descripción</th><th></th></tr></thead>
    <tbody>
      @foreach($milestones as $ms)
        <tr>
          <td><b style="color:var(--pine-2)">{{ $ms->year }}</b></td>
          <td>{{ $ms->description }}</td>
          <td><div class="actions">
            <a href="{{ route('admin.milestones.edit', $ms) }}" class="btn btn-sm btn-primary">Editar</a>
            <form method="POST" action="{{ route('admin.milestones.destroy', $ms) }}" data-confirm="¿Eliminar?">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">✕</button></form>
          </div></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection