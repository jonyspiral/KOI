@php
    $valor = request($campo, '');
@endphp
<input type="text" name="{{ $campo }}" value="{{ $valor }}" class="form-control form-control-sm" placeholder="Buscar {{ $campo }}">
