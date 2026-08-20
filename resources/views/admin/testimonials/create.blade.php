{{-- create.blade.php --}}
@extends('layouts.admin')
@section('content')
<div class="page-head"><h1>Nueva historia</h1><a href="{{ route('admin.testimonials.index') }}" class="btn btn-line">← Volver</a></div>
<div class="card" style="max-width:620px">
  <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data">@csrf @include('admin.testimonials._form')</form>
</div>
@endsection