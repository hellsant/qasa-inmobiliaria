{{-- edit.blade.php --}}
@extends('layouts.admin')
@section('content')
<div class="page-head"><h1>Editar: {{ $zone->name }}</h1><a href="{{ route('admin.zones.index') }}" class="btn btn-line">← Volver</a></div>
<div class="card" style="max-width:560px">
  <form method="POST" action="{{ route('admin.zones.update', $zone) }}">
    @csrf @method('PUT')
    @include('admin.zones._form')
  </form>
</div>
@endsection