@extends('layouts.app')

@section('content')
<div class="container">
  <h3>✏ Vistas generadas para: {{ $modelo }}</h3>

  <h5>📄 index.blade.php</h5>
  <pre><code>{{ $indexView }}</code></pre>

  <h5>📝 form.blade.php</h5>
  <pre><code>{{ $formView }}</code></pre>

  <p class="mt-4">✅ Ahora podés visitar: <code>/{{ str_replace('.', '/', strtolower($namespace_controlador)) }}/{{ Str::snake($modelo) }}</code></p>

</div>
@endsection
