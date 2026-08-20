@extends('layouts.admin')
@section('title', 'FAQ')
@section('content')
<div class="page-head">
  <div><h1>Preguntas frecuentes</h1></div>
  <a href="{{ route('admin.faqs.create') }}" class="btn btn-gold">+ Nueva pregunta</a>
</div>
<div class="card">
  <table class="table">
    <thead><tr><th>Pregunta</th><th>Orden</th><th></th></tr></thead>
    <tbody>
      @foreach($faqs as $faq)
        <tr>
          <td><b style="color:var(--ink)">{{ $faq->question }}</b><br><small style="color:var(--muted)">{{ Str::limit($faq->answer, 80) }}</small></td>
          <td>{{ $faq->sort }}</td>
          <td><div class="actions">
            <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-primary">Editar</a>
            <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" data-confirm="¿Eliminar?">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">✕</button></form>
          </div></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection