@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <h1 class="mb-3">Órdenes Mercado Libre</h1>

  {{-- Flash & errores --}}
  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Filtros --}}
  <form method="GET" action="{{ route('mlibre.orders.index') }}" class="row g-2 align-items-end mb-3">
    <div class="col-auto">
      <label class="form-label mb-0">Desde</label>
      <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto">
      <label class="form-label mb-0">Hasta</label>
      <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto">
      <label class="form-label mb-0">Sólo pagadas</label>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="only_paid" name="only_paid" value="1" {{ $onlyPaid ? 'checked' : '' }}>
        <label for="only_paid" class="form-check-label small">paid</label>
      </div>
    </div>
    <div class="col-auto">
      <label class="form-label mb-0">CAE</label>
      <select name="cae" class="form-select form-select-sm">
        <option value="any" {{ $caeFilter==='any'?'selected':'' }}>Todos</option>
        <option value="con" {{ $caeFilter==='con'?'selected':'' }}>Con CAE</option>
        <option value="sin" {{ $caeFilter==='sin'?'selected':'' }}>Sin CAE</option>
      </select>
    </div>
    <div class="col-auto">
      <label class="form-label mb-0">ARCA</label>
      <select name="arca_status" class="form-select form-select-sm">
        @php $opts = ['any'=>'Todos','pending'=>'Pending','processing'=>'Processing','success'=>'Success','warning'=>'Warning','error'=>'Error']; @endphp
        @foreach ($opts as $k=>$v)
          <option value="{{ $k }}" {{ $arcaFilter===$k?'selected':'' }}>{{ $v }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-sm btn-primary">Filtrar</button>
      <a class="btn btn-sm btn-outline-secondary" href="{{ route('mlibre.orders.index') }}">Limpiar</a>
    </div>
  </form>

  {{-- Formulario de lote --}}
  <form method="POST" action="{{ route('mlibre.orders.facturar') }}">
    @csrf

    <div class="d-flex gap-2 mb-2">
      <button type="submit" class="btn btn-success btn-sm">
        Facturar seleccionadas
      </button>
      <span class="text-muted small">Sólo se facturan las elegibles (pagadas, no facturadas y sin factura ML previa).</span>
    </div>

    <div class="table-responsive">
      <table class="table table-sm table-striped align-middle">
        <thead>
          <tr>
            <th style="width:28px;">
              <input type="checkbox" onclick="toggleSelectAll(this)">
            </th>
            <th>Orden</th>
            <th>Fecha</th>
            <th>Comprador</th>
            <th class="text-end">Monto</th>
            <th>Envío</th>
            <th>Facturación</th>
            <th style="width:220px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $o)
            @php
              $mlHasInvoice  = (bool) ($o->ml_invoice_attached ?? false);
              $mlAutoInvoice = (bool) ($o->ml_invoiced_by_ml ?? false);
              $yaFacturada   = (bool) ($o->invoiced ?? false);
              $eligible      = ($o->status === 'paid' && !$yaFacturada && !$mlHasInvoice && !$mlAutoInvoice);

              $lastLog = $o->relationLoaded('logs') ? $o->logs->first() : $o->logs()->latest()->first();
              $monto = $o->total_amount ?? $o->paid_amount ?? 0;
              $fecha = $o->date_created ? \Carbon\Carbon::parse($o->date_created)->format('d/m/Y H:i') : '';
            @endphp
            <tr>
              <td>
                <input type="checkbox" name="order_ids[]" value="{{ $o->id }}" {{ $eligible ? '' : 'disabled' }}>
              </td>
              <td>
                @php
                  // Plantilla corregida (sin "/venta/"):
                  // https://www.mercadolibre.com.ar/ventas/{ORDER_ID}/detalle
                  $mlOrderUrlTpl = env('ML_ORDER_URL', 'https://www.mercadolibre.com.ar/ventas/%s/detalle');
                  $mlOrderUrl    = sprintf($mlOrderUrlTpl, $o->order_id);
                @endphp

                <div class="fw-semibold">
                  <a href="{{ $mlOrderUrl }}" target="_blank" rel="noopener noreferrer">
                    {{ $o->order_id }}
                  </a>
                </div>
                <div class="small text-muted">{{ strtoupper($o->status ?? '-') }}</div>
              </td>
              <td>{{ $fecha }}</td>
              <td>
                <div class="fw-semibold">{{ $o->buyer_name ?? '-' }}</div>
                <div class="small text-muted">
                  @if($o->buyer_doc_type || $o->buyer_doc_number)
                    {{ $o->buyer_doc_type }} {{ $o->buyer_doc_number }}
                  @else
                    —
                  @endif
                </div>
              </td>
              <td class="text-end">{{ number_format($monto, 2, ',', '.') }}</td>
              <td class="small">
                @if(!empty($o->shipping_status))
                  <div>{{ $o->shipping_status }}</div>
                @endif
                @if(!empty($o->shipping_tracking_number))
                  <div class="text-muted">Tk: {{ $o->shipping_tracking_number }}</div>
                @endif
              </td>

              {{-- Facturación / CAE / Nota ML --}}
              <td>
                @if($o->invoiced)
                  <span class="badge bg-success">Facturada</span>
                  @if($o->invoice_type && $o->pos_number && $o->invoice_number)
                    <small class="text-muted d-block">
                      {{ $o->invoice_type }}
                      {{ str_pad($o->pos_number, 4, '0', STR_PAD_LEFT) }}-{{ str_pad($o->invoice_number, 8, '0', STR_PAD_LEFT) }}
                    </small>
                  @endif
                  @if($o->cae)
                    <small class="text-muted d-block">CAE: {{ $o->cae }}</small>
                  @endif
                  @if($o->cae_due_date)
                    <small class="text-muted d-block">Vto: 
                      @php
                        $v = $o->cae_due_date;
                        if (preg_match('/^\d{8}$/', $v ?? '')) {
                          $v = substr($v,0,4).'-'.substr($v,4,2).'-'.substr($v,6,2);
                        }
                      @endphp
                      {{ $v }}
                    </small>
                  @endif
                @else
                  @php
                    $status = strtolower($o->arca_status ?? 'pending');
                    $map = [
                      'success'   => 'success',
                      'error'     => 'danger',
                      'warning'   => 'warning text-dark',
                      'processing'=> 'secondary',
                      'pending'   => 'secondary',
                    ];
                    $cls = $map[$status] ?? 'secondary';
                  @endphp
                  <span class="badge bg-{{ $cls }}">{{ strtoupper($o->arca_status ?? 'PENDING') }}</span>
                @endif

                {{-- Nota ML (si existe) --}}
                @if($o->ml_note_id)
                  <small class="d-block mt-1">
                    <span class="badge bg-info text-dark">Nota ML</span>
                    <span class="text-muted">
                      @if($o->ml_note_posted_at)
                        {{ \Carbon\Carbon::parse($o->ml_note_posted_at)->format('d/m/Y H:i') }}
                      @endif
                    </span>
                  </small>
                  @if($o->ml_note_text)
                    <small class="text-muted d-block" title="{{ $o->ml_note_text }}">
                      {{ \Illuminate\Support\Str::limit($o->ml_note_text, 60) }}
                    </small>
                  @endif
                @endif
              </td>

              {{-- Acciones --}}
              <td class="d-flex gap-2">
                <button
                  formaction="{{ route('mlibre.orders.facturar') }}"
                  name="order_ids[]"
                  value="{{ $o->id }}"
                  class="btn btn-sm btn-outline-success"
                  {{ $eligible ? '' : 'disabled' }}
                  title="{{ $eligible ? 'Facturar ahora' : 'Ya facturada o no elegible' }}"
                >
                  Facturar
                </button>

                {{-- Ver log (fallback sin Bootstrap JS) --}}
                @if($lastLog)
                  <button type="button"
                          class="btn btn-sm btn-outline-secondary"
                          onclick="toggleLog('{{ $o->id }}')">
                    Ver log
                  </button>
                @endif
              </td>
            </tr>

            {{-- Panel de log (colapsable) --}}
            @if($lastLog)
              <tr id="log-{{ $o->id }}" class="d-none">
                <td></td>
                <td colspan="7">
                  <div class="card">
                    <div class="card-body small">
                      <div><strong>Status:</strong> {{ $lastLog->status }}</div>

                      <div class="mt-1">
                        <strong>Error:</strong>
                        @if(!empty($lastLog->error_message))
                          <pre class="mb-0 bg-light p-2">{{ $lastLog->error_message }}</pre>
                        @else
                          <span class="text-muted">—</span>
                        @endif
                      </div>

                      @if(!empty($lastLog->response_json))
                        <details class="mt-2">
                          <summary>Respuesta</summary>
                          <pre class="mb-0 bg-light p-2">{{ \Illuminate\Support\Str::limit($lastLog->response_json, 20000) }}</pre>
                        </details>
                      @endif

                      @if(!empty($lastLog->request_json))
                        <details class="mt-2">
                          <summary>Request</summary>
                          <pre class="mb-0 bg-light p-2">{{ \Illuminate\Support\Str::limit($lastLog->request_json, 20000) }}</pre>
                        </details>
                      @endif
                    </div>
                  </div>
                </td>
              </tr>
            @endif

          @empty
            <tr><td colspan="8" class="text-center text-muted">Sin resultados…</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $orders->links() }}
    </div>
  </form>
</div>

{{-- Scripts mínimos --}}
<script>
  function toggleLog(id) {
    const row = document.getElementById('log-' + id);
    if (!row) return;
    row.classList.toggle('d-none');
    if (!row.classList.contains('d-none')) {
      row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  }
  function toggleSelectAll(src) {
    const checks = document.querySelectorAll('input[type=checkbox][name="order_ids[]"]:not(:disabled)');
    checks.forEach(ch => ch.checked = src.checked);
  }
</script>
@endsection
