@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-3">🧾 Listado de {{ Str::headline($modelo) }}</h2>

    @php
        $tabs = __TABS__;
        $fieldLabels = __FIELD_LABELS__;
    @endphp

    <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Contenido</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registros as $registro)
                <tr>
                    <form method="POST" action="{{ route('__RUTA__.update', $registro->id) }}">
                        @csrf
                        @method('PUT')

                        <td>{{ $registro->id }}</td>

                        <td>
                            {{-- 🧩 Tabs internos por fila --}}
                            <ul class="nav nav-tabs" id="tabs-{{ $registro->id }}" role="tablist">
                                @foreach (array_keys($tabs) as $tab)
                                    <li class="nav-item">
                                        <button class="nav-link @if($loop->first) active @endif" data-bs-toggle="tab" data-bs-target="#tab-{{ $registro->id }}-{{ $tab }}" type="button">
                                            {{ ucfirst($tab) }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content border p-2 bg-light">
                                @foreach ($tabs as $tab => $campos)
                                    <div class="tab-pane fade @if($loop->first) show active @endif" id="tab-{{ $registro->id }}-{{ $tab }}">
                                        <div class="row g-2">
                                            @foreach ($campos as $campo)
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ $fieldLabels[$campo] ?? Str::headline($campo) }}</label>
                                                    <input type="text" name="{{ $campo }}" value="{{ old($campo, $registro->{$campo}) }}" class="form-control form-control-sm">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>

                        <td class="text-end">
                            <button type="submit" class="btn btn-sm btn-success">💾</button>
                        </td>
                    </form>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
