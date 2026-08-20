{{-- create.blade.php --}}
@extends('layouts.admin')
@section('content')
<div class="page-head"><h1>Nuevo miembro</h1><a href="{{ route('admin.team.index') }}" class="btn btn-line">← Volver</a></div>
<div class="card" style="max-width:560px"><form method="POST" action="{{ route('admin.team.store') }}" enctype="multipart/form-data">@csrf @include('admin.team._form')</form></div>
@endsection