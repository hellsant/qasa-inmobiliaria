{{-- edit.blade.php --}}
@extends('layouts.admin')
@section('content')
<div class="page-head"><h1>Editar: {{ $member->name }}</h1><a href="{{ route('admin.team.index') }}" class="btn btn-line">← Volver</a></div>
<div class="card" style="max-width:560px"><form method="POST" action="{{ route('admin.team.update', $member) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.team._form')</form></div>
@endsection