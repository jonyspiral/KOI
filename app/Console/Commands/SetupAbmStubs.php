<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupAbmStubs extends Command
{
    protected $signature = 'abm:setup-stubs';
    protected $description = 'Crea los stubs necesarios para la generación de ABMs';

    public function handle()
    {
        $stubPath = resource_path('stubs/abm');

        if (!File::exists($stubPath)) {
            File::makeDirectory($stubPath, 0755, true);
            $this->info("📁 Carpeta creada: $stubPath");
        }

        $stubs = [
            'index.stub.blade.php' => '@extends(\'layouts.app\')

@section(\'content\')
<div class="container">
<h2>Listado de {{ $modelo }}</h2>
<div class="table-responsive">
<table class="table">
<thead><tr>@foreach($columnas as $col)<th>{{ $col }}</th>@endforeach<th>Acciones</th></tr></thead>
<tbody>@foreach($registros as $registro)<tr>@foreach($columnas as $col)<td>{{ $registro->$col }}</td>@endforeach<td>...</td></tr>@endforeach</tbody>
</table>
</div>
</div>
@endsection',

            'create.stub.blade.php' => '<form action="{{ route($modelo . \'.store\') }}" method="POST">
@csrf
<!-- Campos -->
<button type="submit">Guardar</button>
</form>',

            'edit.stub.blade.php' => '<form action="{{ route($modelo . \'.update\', $registro->id) }}" method="POST">
@csrf
@method(\'PUT\')
<!-- Campos -->
<button type="submit">Actualizar</button>
</form>',

            'controller.stub.php' => '<?php

namespace App\Http\Controllers\{{ namespace }};

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{{ modelo }};

class {{ modelo }}Controller extends Controller
{
    public function index()
    {
        $registros = {{ modelo }}::all();
        return view(\'{{ carpeta_vistas }}.index\', compact(\'registros\'));
    }
}
',

            'request.stub.php' => '<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class {{ modelo }}Request extends FormRequest
{
    public function rules()
    {
        return [
            // Agregar reglas desde fieldsMeta
        ];
    }
}'
        ];

        foreach ($stubs as $filename => $content) {
            $file = $stubPath . '/' . $filename;
            File::put($file, $content);
            $this->info("✅ Stub creado: $filename");
        }

        $this->info("🎉 Todos los stubs del ABM fueron creados correctamente.");
    }
}