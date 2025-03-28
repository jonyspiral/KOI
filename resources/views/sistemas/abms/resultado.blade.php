@extends('layouts.app')

@section('content')
<div class="container">
  <h3>✏ Vistas generadas para: {{ $modeloNombre }}</h3>

  <h5>📄 index.blade.php</h5>
  <pre><code>{{ $indexCode }}</code></pre>

  <h5>📝 form.blade.php</h5>
  <pre><code>{{ $formCode }}</code></pre>

  <p class="mt-4">✅ Ahora podés visitar: <code>/abms/{{ strtolower($modeloNombre) }}</code></p>
</div>
@endsection
