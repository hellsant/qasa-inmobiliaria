@if(session('success'))
  <div class="flash flash-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="flash flash-error">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif