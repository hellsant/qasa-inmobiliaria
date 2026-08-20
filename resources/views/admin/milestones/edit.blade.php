{{-- edit.blade.php --}}
@extends('layouts.admin')
@section('content')
<div class="page-head"><h1>Editar hito</h1><a href="{{ route('admin.milestones.index') }}" class="btn btn-line">← Volver</a></div>
<div class="card" style="max-width:560px">
  <form method="POST" action="{{ route('admin.milestones.update', $milestone) }}">@csrf @method('PUT') @include('admin.milestones._form')</form>
</div>
@endsection