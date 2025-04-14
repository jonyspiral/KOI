@if ($meta['input_type'] === 'checkbox')
    @php
        $checkedValue = $meta['checkbox_checked_value'] ?? 'S';
        $uncheckedValue = $meta['checkbox_unchecked_value'] ?? 'N';
        $valor = $sub->$campo ?? $uncheckedValue;
    @endphp
    <span class="fw-bold {{ $valor === $checkedValue ? 'text-success' : 'text-secondary' }}">
        {{ $valor === $checkedValue ? '✅ Sí' : '— No' }}
    </span>

@elseif ($meta['input_type'] === 'select' && !empty($meta['referenced_table']))
    @php
        $label = \DB::table($meta['referenced_table'])
            ->where($meta['referenced_column'] ?? 'id', $sub->$campo)
            ->value($meta['referenced_label'] ?? 'nombre') ?? $sub->$campo;
    @endphp
    <span class="text-dark">{{ $label }}</span>

@elseif ($meta['input_type'] === 'select_list' && !empty($meta['select_list_data']))
    @php
        $list = collect();
        foreach (explode(',', $meta['select_list_data']) as $item) {
            [$label, $value] = array_map('trim', explode('=', $item));
            $list->put($value, $label);
        }
    @endphp
    <span class="text-primary">{{ $list[$sub->$campo] ?? $sub->$campo }}</span>

@elseif (str_contains($campo, 'fecha') || $meta['input_type'] === 'date')
    @php
        try {
            $date = \Carbon\Carbon::parse($sub->$campo);
        } catch (\Throwable $e) {
            $date = null;
        }
    @endphp
    <span class="text-info">{{ $date ? $date->format('d/m/Y') : '—' }}</span>

@else
    <span class="text-muted">{{ $sub->$campo ?? '—' }}</span>
@endif
