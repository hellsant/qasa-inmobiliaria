{{-- create.blade.php --}}
@extends('layouts.admin')
@section('content')
<div class="page-head"><h1>Nueva zona</h1><a href="{{ route('admin.zones.index') }}" class="btn btn-line">← Volver</a></div>
<div class="card" style="max-width:560px">
  <form method="POST" action="{{ route('admin.zones.store') }}">
    @csrf
    @include('admin.zones._form')
  </form>
</div>
@endsection