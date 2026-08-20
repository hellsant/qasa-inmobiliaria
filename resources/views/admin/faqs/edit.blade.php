{{-- edit.blade.php --}}
@extends('layouts.admin')
@section('content')
<div class="page-head"><h1>Editar pregunta</h1><a href="{{ route('admin.faqs.index') }}" class="btn btn-line">← Volver</a></div>
<div class="card" style="max-width:640px"><form method="POST" action="{{ route('admin.faqs.update', $faq) }}">@csrf @method('PUT') @include('admin.faqs._form')</form></div>
@endsection