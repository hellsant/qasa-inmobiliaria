@extends('layouts.admin')
@section('title', 'Mensajes')

@section('content')
<div class="page-head">
  <div><h1>Mensajes y tasaciones</h1><div class="sub">Consultas del formulario de contacto y solicitudes de tasación.</div></div>
  <div style="display:flex;gap:8px">
    <a href="{{ route('admin.leads.index') }}" class="btn btn-sm {{ !request('kind') ? 'btn-primary' : 'btn-line' }}">Todos</a>
    <a href="{{ route('admin.leads.index', ['kind' => 'tasacion']) }}" class="btn btn-sm {{ request('kind') === 'tasacion' ? 'btn-primary' : 'btn-line' }}">Tasaciones</a>
    <a href="{{ route('admin.leads.index', ['kind' => 'contacto']) }}" class="btn btn-sm {{ request('kind') === 'contacto' ? 'btn-primary' : 'btn-line' }}">Contacto</a>
  </div>
</div>

<div class="card">
  <table class="table">
    <thead><tr><th>Tipo</th><th>Nombre</th><th>Teléfono</th><th>Detalle</th><th>Fecha</th><th></th></tr></thead>
    <tbody>
      @forelse($leads as $lead)
        <tr style="{{ $lead->is_read ? 'opacity:.65' : '' }}">
          <td><span class="badge {{ $lead->kind === 'tasacion' ? 'badge-terra' : 'badge-alquiler' }}">{{ $lead->kind_label }}</span></td>
          <td><b style="color:var(--ink)">{{ $lead->name }}</b></td>
          <td>
            @if($lead->phone)
              <a href="https://wa.me/{{ preg_replace('/\D/', '', $lead->phone) }}" target="_blank" style="color:var(--ok);font-weight:700">{{ $lead->phone }}</a>
            @endif
          </td>
          <td style="max-width:380px">
            @if($lead->kind === 'tasacion')
              <small>
                {{ $lead->operation }} · {{ $lead->property_type }} · {{ $lead->zone }}
                @if($lead->area_m2) · {{ number_format($lead->area_m2, 0, ',', '.') }} m² @endif
              </small><br>
            @elseif($lead->interest)
              <small>{{ $lead->interest }}</small><br>
            @endif
            {{ Str::limit($lead->message, 90) }}
          </td>
          <td><small>{{ $lead->created_at->format('d/m/Y H:i') }}</small></td>
          <td><div class="actions">
            <form method="POST" action="{{ route('admin.leads.read', $lead) }}">
              @csrf @method('PATCH')
              <button class="btn btn-sm {{ $lead->is_read ? 'btn-line' : 'btn-gold' }}" type="submit">
                {{ $lead->is_read ? 'No leído' : '✓ Leído' }}
              </button>
            </form>
            <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}" data-confirm="¿Eliminar mensaje?">
              @csrf @method('DELETE')<button class="btn btn-sm btn-danger">✕</button>
            </form>
          </div></td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--muted)">No hay mensajes.</td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination">{{ $leads->links('pagination::simple-default') }}</div>
</div>
@endsection