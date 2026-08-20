@php use App\Models\Lead; @endphp
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Panel') · QASA Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<aside class="sidebar">
  <div class="brand">QASA<span>.</span></div>
  <nav>
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">▦ Dashboard</a>
    <a href="{{ route('admin.properties.index') }}" class="{{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">🏠 Propiedades</a>
    <a href="{{ route('admin.zones.index') }}" class="{{ request()->routeIs('admin.zones.*') ? 'active' : '' }}">📍 Zonas</a>
    <a href="{{ route('admin.testimonials.index') }}" class="{{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">💬 Historias</a>
    <a href="{{ route('admin.team.index') }}" class="{{ request()->routeIs('admin.team.*') ? 'active' : '' }}">👥 Equipo</a>
    <a href="{{ route('admin.milestones.index') }}" class="{{ request()->routeIs('admin.milestones.*') ? 'active' : '' }}">🗓 Hitos</a>
    <a href="{{ route('admin.faqs.index') }}" class="{{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">❓ FAQ</a>
    <a href="{{ route('admin.leads.index') }}" class="{{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">
      ✉ Mensajes
      @if($unread = Lead::where('is_read', false)->count())<span class="pill">{{ $unread }}</span>@endif
    </a>
    <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">⚙ Configuración</a>
  </nav>
  <div class="sidebar-bottom">
    <a href="{{ route('home') }}" target="_blank">Ver sitio ↗</a>
    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Cerrar sesión</button></form>
  </div>
</aside>

<main class="main">
  @if(session('success'))<div class="alert alert-ok">{{ session('success') }}</div>@endif
  @if($errors->any())
    <div class="alert alert-error"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif
  @yield('content')
</main>

<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>