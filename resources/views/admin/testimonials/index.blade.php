@extends('layouts.admin')
@section('title', 'Historias')
@section('content')
<div class="page-head">
  <div><h1>Historias de clientes</h1><div class="sub">Testimonios de la sección 08.</div></div>
  <a href="{{ route('admin.testimonials.create') }}" class="btn btn-gold">+ Nueva historia</a>
</div>
<div class="card">
  <table class="table">
    <thead><tr><th>Foto</th><th>Cliente</th><th>Testimonio</th><th>Operación</th><th></th></tr></thead>
    <tbody>
      @foreach($testimonials as $t)
        <tr>
          <td><img src="{{ $t->photo_url }}" class="avatar" alt=""></td>
          <td><b style="color:var(--ink)">{{ $t->author }}</b></td>
          <td style="max-width:420px">{{ Str::limit($t->quote, 90) }}</td>
          <td><span class="badge badge-terra">{{ $t->operation }} · {{ $t->location }}</span></td>
          <td><div class="actions">
            <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-sm btn-primary">Editar</a>
            <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" data-confirm="¿Eliminar?">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">✕</button></form>
          </div></td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="pagination">{{ $testimonials->links('pagination::simple-default') }}</div>
</div>
@endsection