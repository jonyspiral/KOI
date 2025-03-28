@extends('layouts.app')

@section('content')
<div class="container">
  <h3>🎨 Vistas generadas para: {{ $modelo }}</h3>

  <h5>📄 index.blade.php</h5>
  <pre><code>{{ $indexCode }}</code></pre>

  <h5>📝 form.blade.php</h5>
  <pre><code>{{ $formCode }}</code></pre>

  <p class="mt-4">¡Listo! Ahora podés acceder al ABM desde sus rutas.</p>
</div>
@endsection
