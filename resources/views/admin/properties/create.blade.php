@extends('layouts.admin')
@section('title', 'Nueva propiedad')

@section('content')
<div class="page-head">
  <div><h1>Nueva propiedad</h1><div class="sub">Completá los datos y subí al menos 6 fotos.</div></div>
  <a href="{{ route('admin.properties.index') }}" class="btn btn-line">← Volver</a>
</div>
<div class="card">
  <form method="POST" action="{{ route('admin.properties.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.properties._form')
  </form>
</div>
@endsection