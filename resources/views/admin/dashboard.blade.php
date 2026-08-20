@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="page-head">
  <div><h1>Hola, {{ auth()->user()->name }} 👋</h1><div class="sub">Esto es lo que pasa en QASA hoy.</div></div>
  <a href="{{ route('admin.properties.create') }}" class="btn btn-gold">+ Nueva propiedad</a>
</div>

<div class="stat-cards">
  <div class="stat-card"><b>{{ $activeProperties }}</b><span>Propiedades activas</span></div>
  <div class="stat-card"><b>{{ $featuredCount }}</b><span>Destacadas</span></div>
  <div class="stat-card"><b>{{ $unreadLeads }}</b><span>Mensajes sin leer</span></div>
  <div class="stat-card"><b>{{ $totalProperties }}</b><span>Total en catálogo</span></div>
</div>

<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:24px">
  <div class="card">
    <h2>Últimos mensajes recibidos</h2>
    @forelse($recentLeads as $lead)
      <div style="display:flex;gap:12px;padding:12px 0;border-bottom:1px solid var(--line)">
        <span class="badge {{ $lead->kind === 'tasacion' ? 'badge-terra' : 'badge-alquiler' }}">{{ $lead->kind_label }}</span>
        <div><b style="color:var(--ink)">{{ $lead->name }}</b><div style="font-size:12.5px;color:var(--muted)">{{ Str::limit($lead->message ?: $lead->interest, 70) }}</div></div>
        <span style="margin-left:auto;font-size:12px;color:var(--muted)">{{ $lead->created_at->diffForHumans() }}</span>
      </div>
    @empty
      <p style="color:var(--muted)">Todavía no hay mensajes.</p>
    @endforelse
    <a href="{{ route('admin.leads.index') }}" class="btn btn-line btn-sm" style="margin-top:16px">Ver todos →</a>
  </div>

  <div class="card">
    <h2>Propiedades por operación</h2>
    @foreach(\App\Models\Property::OPERATIONS as $key => $label)
      <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)">
        <span class="badge badge-{{ $key }}">{{ $label }}</span>
        <b style="color:var(--pine-2)">{{ $byOperation[$key] ?? 0 }}</b>
      </div>
    @endforeach
  </div>
</div>
@endsection