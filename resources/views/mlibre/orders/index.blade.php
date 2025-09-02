@extends('layouts.app')

@section('content')
<div class="container-xxl">

  <h3 class="mb-3">Órdenes pagas</h3>

  <form method="get" class="row g-2 mb-3">
    <div class="col-auto"><input type="date" name="from" value="{{ $from }}" class="form-control"></div>
    <div class="col-auto"><input type="date" name="to" value="{{ $to }}" class="form-control"></div>
    <div class="col-auto form-check mt-2">
      <input class="form-check-input" type="checkbox" name="only_paid" value="1" {{ $onlyPaid ? 'checked':'' }} id="chkPaid">
      <label class="form-check-label" for="chkPaid">Solo pagadas</label>
    </div>
    <div class="col-auto">
      <select name="cae" class="form-select">
        <option value="any" {{ $caeFilter==='any'?'selected':'' }}>CAE: todas</option>
        <option value="con" {{ $caeFilter==='con'?'selected':'' }}>Con CAE</option>
        <option value="sin" {{ $caeFilter==='sin'?'selected':'' }}>Sin CAE</option>
      </select>
    </div>
    <div class="col-auto">
      <select name="arca_status" class="form-select">
        @php $ops=['any'=>'ARCA: todos','success'=>'success','warning'=>'warning','error'=>'error','processing'=>'processing','pending'=>'pending']; @endphp
        @foreach($ops as $k=>$v)
          <option value="{{ $k }}" {{ $arcaFilter===$k?'selected':'' }}>{{ $v }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-primary">Filtrar</button>
    </div>
  </form>

  <form method="post" action="{{ route('mlibre.orders.facturar') }}">
    @csrf

    <div class="d-flex gap-2 mb-2">
      <button type="submit" class="btn btn-success btn-sm">Facturar seleccionadas</button>
      <small class="text-muted">Una factura por orden. Para pack en una sola factura, usa el botón del encabezado del pack.</small>
    </div>

    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th style="width:32px;"></th>
            <th>Orden / Pack</th>
            <th>Fecha</th>
            <th>Cliente / Doc</th>
            <th class="text-end">Monto</th>
            <th>Envío</th>
            <th>Facturación</th>
            <th style="width:220px;">Acciones</th>
          </tr>
        </thead>
        <tbody>

        @forelse ($groups as $gKey => $rows)
          @php
            $isPack = str_starts_with($gKey,'pack:');
            $packId = $isPack ? substr($gKey,5) : null;

            $head = $rows->sortBy('date_created')->first();
            $total = $rows->sum(fn($o)=>$o->total_amount ?? $o->paid_amount ?? 0);
            $fecha = $head->date_created ? \Carbon\Carbon::parse($head->date_created)->format('d/m/Y H:i') : '';
            $buyer = $head->buyer_name ?? '-';
            $doc   = trim(($head->buyer_doc_type ?? '').' '.($head->buyer_doc_number ?? ''));

            $anyInvoiced = $rows->contains(fn($o)=>(bool)$o->invoiced);
            $allEligible = $rows->every(function($o){
              $mlHas=(bool)($o->ml_invoice_attached??false); $mlAuto=(bool)($o->ml_invoiced_by_ml??false);
              return ($o->status==='paid') && !$o->invoiced && !$mlHas && !$mlAuto;
            });

            $lastLog = $rows->first()->relationLoaded('logs') ? $rows->first()->logs->first() : $rows->first()->logs()->latest()->first();

            $ship = [
              'status' => $head->shipping_status ?? '',
              'track'  => $head->shipping_tracking_number ?? '',
            ];
          @endphp

          {{-- Encabezado de pack/orden (colapsable) --}}
          <tr class="table-active">
            <td>
              @if(!$isPack)
                {{-- checkbox por orden suelta --}}
                <input type="checkbox" name="order_ids[]" value="{{ $head->id }}"
                       {{ ($head->status==='paid' && !$head->invoiced && !($head->ml_invoice_attached||$head->ml_invoiced_by_ml)) ? '' : 'disabled' }}>
              @endif
            </td>
            <td>
              @if($isPack)
                <a href="javascript:void(0)" onclick="togglePack('{{ $packId }}')"
                   class="text-decoration-none">
                  <strong>PACK {{ $packId }}</strong>
                  <span class="text-muted">({{ $rows->count() }} órdenes)</span>
                </a>
              @else
                @php
                  $mlUrl = config('services.mlibre.order_url', 'https://www.mercadolibre.com.ar/ventas/%s/detalle');
                  $href  = sprintf($mlUrl, $head->order_id);
                @endphp
                <a href="{{ $href }}" target="_blank" rel="noopener">
                  <strong>{{ $head->order_id }}</strong>
                </a>
              @endif
              <div class="small text-muted mt-1">{{ strtoupper($head->status ?? '-') }}</div>
            </td>
            <td>{{ $fecha }}</td>
            <td>
              <div class="fw-semibold">{{ $buyer }}</div>
              <div class="small text-muted">{{ $doc ?: '—' }}</div>
            </td>
            <td class="text-end">{{ number_format($total, 2, ',', '.') }}</td>
            <td class="small">
              @if($ship['status']) <div>{{ $ship['status'] }}</div> @endif
              @if($ship['track'])  <div class="text-muted">Tk: {{ $ship['track'] }}</div> @endif
            </td>
            <td>
              @if($anyInvoiced)
                <span class="badge bg-success">Facturada</span>
                @php $inv = $rows->firstWhere('invoiced', true) ?? $head; @endphp

                @if(!empty($inv->invoice_type) && !empty($inv->pos_number) && !empty($inv->invoice_number))
                  @php
                      $tipoRaw = $inv->invoice_type;
                      if (is_numeric($tipoRaw)) {
                          $t = (int)$tipoRaw;
                          $tipo = $t === 1 ? 'A' : ($t === 6 ? 'B' : ($t === 11 ? 'C' : $tipoRaw));
                      } else {
                          $s = strtoupper((string)$tipoRaw);
                          $tipo = in_array($s, ['A','B','C'], true) ? $s : $s;
                      }
                  @endphp
                  <small class="text-muted d-block">
                    {{ $tipo }}
                    {{ str_pad((string)$inv->pos_number, 4, '0', STR_PAD_LEFT) }}-{{ str_pad((string)$inv->invoice_number, 8, '0', STR_PAD_LEFT) }}
                  </small>
                @endif

                @if(!empty($inv->cae))
                  <small class="text-muted d-block">CAE: {{ $inv->cae }}</small>
                @endif
                @if(!empty($inv->cae_due_date))
                  <small class="text-muted d-block">Vto: {{ $inv->cae_due_date }}</small>
                @endif

              @else
                @php
                  $status = strtolower($head->arca_status ?? 'pending');
                  $map = [
                    'success'    => 'success',
                    'error'      => 'danger',
                    'warning'    => 'warning text-dark',
                    'processing' => 'secondary',
                    'pending'    => 'secondary',
                  ];
                  $cls = $map[$status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $cls }}">{{ strtoupper($head->arca_status ?? 'PENDING') }}</span>
              @endif

              {{-- Nota ML: mostrar aunque el header no tenga los campos, usando cualquier orden del grupo o el último log --}}
              @php
                  // 1) ¿Alguna orden del grupo tiene la nota persistida?
                  $noteRow = $rows->first(function($o) {
                      return !empty($o->ml_note_id);
                  });

                  // 2) Si no, buscar en el último log una acción ml_note
                  if (!$noteRow) {
                      $noteRow = $rows->first(function($o) {
                          if (!$o->relationLoaded('logs') || $o->logs->isEmpty()) return false;
                          $rq = json_decode($o->logs->first()->request_json ?? '[]', true);
                          return isset($rq['action']) && $rq['action'] === 'ml_note';
                      });
                  }

                  // 3) Preparar texto y fecha
                  $notaTxt = null;
                  $notaAt  = null;
                  if ($noteRow) {
                      if (!empty($noteRow->ml_note_text)) {
                          $notaTxt = $noteRow->ml_note_text;
                      } elseif ($noteRow->relationLoaded('logs') && $noteRow->logs->isNotEmpty()) {
                          $rq = json_decode($noteRow->logs->first()->request_json ?? '[]', true);
                          $notaTxt = $rq['note'] ?? 'Nota ML enviada';
                      }
                      $notaAt = $noteRow->ml_note_posted_at ?? optional($noteRow->logs->first())->created_at;
                  }
              @endphp

              @if($noteRow)
                <small class="d-block mt-1">
                  <span class="badge bg-info text-dark">Nota ML</span>
                  @if($notaAt)
                    <span class="text-muted">{{ \Carbon\Carbon::parse($notaAt)->format('d/m/Y H:i') }}</span>
                  @endif
                </small>
                @if($notaTxt)
                  <small class="text-muted d-block" title="{{ $notaTxt }}">
                    {{ \Illuminate\Support\Str::limit($notaTxt, 60) }}
                  </small>
                @endif
              @endif
            </td>

            <td class="d-flex gap-2">
              @if($isPack)
                <form method="post" action="{{ route('mlibre.packs.facturar') }}">
                  @csrf
                  <input type="hidden" name="pack_id" value="{{ $packId }}">
                  <button class="btn btn-sm btn-outline-success" {{ $allEligible ? '' : 'disabled' }}
                    title="{{ $allEligible ? 'Facturar pack': 'No elegible' }}">
                    Facturar pack
                  </button>
                </form>
                @if($lastLog)
                  <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleLog('pack-{{ $packId }}')">Ver log</button>
                @endif
              @else
                <button formaction="{{ route('mlibre.orders.facturar') }}"
                        name="order_ids[]"
                        value="{{ $head->id }}"
                        class="btn btn-sm btn-outline-success"
                        {{ ($head->status==='paid' && !$head->invoiced && !($head->ml_invoice_attached||$head->ml_invoiced_by_ml)) ? '' : 'disabled' }}>
                  Facturar
                </button>
                @if($lastLog)
                  <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleLog('order-{{ $head->id }}')">Ver log</button>
                @endif
              @endif
            </td>
          </tr>

          {{-- Detalle colapsable del pack (si corresponde) --}}
          @if($isPack)
            <tr id="pack-{{ $packId }}" class="d-none">
              <td></td>
              <td colspan="7">
                <div class="border rounded p-2">
                  @foreach($rows as $o)
                    @php
                      $fechaO = $o->date_created ? \Carbon\Carbon::parse($o->date_created)->format('d/m/Y H:i') : '';
                      $montoO = $o->total_amount ?? $o->paid_amount ?? 0;
                      $eligible = ($o->status==='paid' && !$o->invoiced && !($o->ml_invoice_attached||$o->ml_invoiced_by_ml));
                      $lastLogO = $o->relationLoaded('logs') ? $o->logs->first() : $o->logs()->latest()->first();
                      $mlUrl = config('services.mlibre.order_url', 'https://www.mercadolibre.com.ar/ventas/%s/detalle');
                      $href  = sprintf($mlUrl, $o->order_id);
                    @endphp
                    <div class="row align-items-center py-1 border-bottom">
                      <div class="col-auto">
                        <input type="checkbox" name="order_ids[]" value="{{ $o->id }}" {{ $eligible ? '' : 'disabled' }}>
                      </div>
                      <div class="col">
                        <a href="{{ $href }}" target="_blank" rel="noopener" class="fw-semibold">{{ $o->order_id }}</a>
                        <div class="small text-muted">{{ $fechaO }}</div>
                        @if($o->items && $o->items->count())
                          <div class="small text-muted">
                            {{ $o->items->map(fn($it)=> trim(($it->title ?? ''). ($it->variation_text ? ' ('.$it->variation_text.')' : '')))->implode(' • ') }}
                          </div>
                        @endif
                      </div>
                      <div class="col text-end">{{ number_format($montoO, 2, ',', '.') }}</div>
                      <div class="col-auto">
                        @if($lastLogO)
                          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleLog('order-{{ $o->id }}')">Log</button>
                        @endif
                      </div>
                    </div>
                  @endforeach
                </div>
              </td>
            </tr>
          @endif

          {{-- Panel de log para el encabezado --}}
          @if($lastLog)
            <tr id="log-{{ $isPack ? 'pack-'.$packId : 'order-'.$head->id }}" class="d-none">
              <td></td>
              <td colspan="7">
                <div class="card"><div class="card-body small">
                  <div><strong>Status:</strong> {{ $lastLog->status }}</div>
                  <div class="mt-1"><strong>Error:</strong> {!! $lastLog->error_message ? '<pre class="mb-0 bg-light p-2">'.e($lastLog->error_message).'</pre>' : '<span class="text-muted">—</span>' !!}</div>
                  @if(!empty($lastLog->response_json))
                    <details class="mt-2"><summary>Respuesta</summary>
                      <pre class="mb-0 bg-light p-2">{{ \Illuminate\Support\Str::limit($lastLog->response_json, 20000) }}</pre>
                    </details>
                  @endif
                  @if(!empty($lastLog->request_json))
                    <details class="mt-2"><summary>Request</summary>
                      <pre class="mb-0 bg-light p-2">{{ \Illuminate\Support\Str::limit($lastLog->request_json, 20000) }}</pre>
                    </details>
                  @endif
                </div></div>
              </td>
            </tr>
          @endif

        @empty
          <tr><td colspan="8" class="text-center text-muted">Sin resultados…</td></tr>
        @endforelse

        </tbody>
      </table>
    </div>

  </form>

  <div class="mt-3">
    {{ $orders->links() }}
  </div>
</div>

<script>
  function togglePack(pid){ const row = document.getElementById('pack-'+pid); if(row) row.classList.toggle('d-none'); }
  function toggleLog(id){ const row = document.getElementById('log-'+id); if(row) row.classList.toggle('d-none'); }
</script>
@endsection
