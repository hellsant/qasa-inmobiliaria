<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ingresar · QASA Admin</title>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="login-body">
  <form class="login-card" method="POST" action="{{ route('login.attempt') }}">
    @csrf
    <div class="login-logo">QASA<span>.</span></div>
    <p style="color:var(--muted);font-size:14px;margin-bottom:26px">Panel de administración</p>

    @if($errors->any())
      <div class="alert alert-error" style="margin-bottom:16px">{{ $errors->first() }}</div>
    @endif

    <label class="field"><span>Email</span>
      <input type="email" name="email" value="{{ old('email') }}" required autofocus>
    </label>
    <label class="field"><span>Contraseña</span>
      <input type="password" name="password" required>
    </label>
    <label class="remember"><input type="checkbox" name="remember"> Recordarme</label>
    <button class="btn btn-primary" type="submit">Ingresar</button>
    <a href="{{ route('home') }}" style="display:block;text-align:center;margin-top:18px;font-size:13px;color:var(--muted)">← Volver al sitio</a>
  </form>
</body>
</html>