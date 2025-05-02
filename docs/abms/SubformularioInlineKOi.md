
# 🧠 Documentación KOI - Subformulario Inline con Alpine.js

## ✅ Estado: Funcional y estable
**Versión:** abril 2025  
**Responsable:** Vicente  
**Tecnologías:** Blade, Alpine.js, PHP, Laravel, JavaScript puro

---

## 📋 Objetivo

Implementar un subformulario de edición inline dinámico y reutilizable para registros hijos (relaciones padre-hijo), con lógica robusta y soporte para múltiples tipos de input, validaciones y redirecciones adecuadas al formulario padre.

---

## 🧩 Estructura del componente

### Componentes principales:

- `x-data` para manejar `editing`, `showForm`, `showTable`.
- Formularios de **creación** y **edición** separados.
- Inputs dinámicos generados desde la configuración JSON (`config_form_Modelo.json`).
- Botones de acción con alternancia visual.
- Redirecciones automáticas al padre desde el controlador.

---

## 📁 Archivo Blade (`koi-subformulario.blade.php`)

El siguiente es el código completo del subformulario:

```blade
<tr x-data="{ editing: false }">
    @foreach ($camposSub as $campo => $meta)
        @if ($campo === $foreignKey)
            @continue
        @endif
        <td>
            <div x-show="!editing">
                @if ($meta['input_type'] === 'checkbox')
                    {{ $sub->$campo === 'S' ? '✅' : '—' }}
                @elseif ($meta['input_type'] === 'select')
                    {{ DB::table($meta['referenced_table'])
                        ->where($meta['referenced_column'] ?? 'id', $sub->$campo)
                        ->value($meta['referenced_label'] ?? 'nombre') ?? $sub->$campo }}
                @elseif ($meta['input_type'] === 'select_list')
                    {{ collect(explode(',', $meta['select_list_data']))
                        ->mapWithKeys(fn($i) => [explode('=', $i)[1] => explode('=', $i)[0]])
                        ->get($sub->$campo) }}
                @else
                    {{ $sub->$campo ?? '—' }}
                @endif
            </div>
            <div x-show="editing">
                @includeIf('partials.koi-subform-input', ['campo' => $campo, 'meta' => $meta, 'valor' => $sub->$campo, 'formId' => "edit-form-{$sub->id}"])
            </div>
        </td>
    @endforeach

    <td class="text-center">
        <div x-show="!editing">
            <div class="d-flex gap-1">
                <button @click="editing = true" class="btn btn-sm btn-primary" type="button">✏️</button>
                <form action="{{ route("produccion.abms.{$rutaBase}.destroy", $sub->id) }}" method="POST" onsubmit="return confirm('¿Confirmar eliminación?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                </form>
            </div>
        </div>

        <div x-show="editing">
            <form id="edit-form-{{ $sub->id }}" action="{{ route("produccion.abms.{$rutaBase}.update", $sub->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="{{ $foreignKey }}" value="{{ $registro->$foreignKey }}">
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-success">💾</button>
                    <button type="button" class="btn btn-sm btn-secondary" @click="editing = false">❌</button>
                </div>
            </form>
        </div>
    </td>
</tr>
```

---

## 🧪 Estrategia técnica

### 🔧 Inputs renderizados dinámicamente

Cada input se genera de forma contextual según:

- `input_type` (`text`, `select`, `select_list`, `checkbox`)
- Datos externos (`referenced_table`, `select_list_data`)
- Campo oculto para checkbox (`value=N`)

### 🎯 Update sin conflictos

- `form` independiente por fila (`id="edit-form-{{ $id }}"`)
- Alpine controla el estado `editing`
- `@input`, `@change`, `@checked` registran valores actualizados
- `onsubmit` opcional para debug de datos enviados

---

## 🔁 Redirección tras actualizar o eliminar

```php
protected function redirectToParent(Request $request, string $modeloNombre)
{
    $configPath = resource_path("meta_abms/config_form_{$modeloNombre}.json");
    if (!file_exists($configPath)) return null;

    $config = json_decode(file_get_contents($configPath), true);
    $foreignKey = null;

    foreach ($config['campos'] ?? [] as $campo => $meta) {
        if (!empty($meta['referenced_table']) || str_starts_with($campo, 'cod_')) {
            if ($request->has($campo)) {
                $foreignKey = $campo;
                break;
            }
        }
    }

    if (!$foreignKey) return null;

    $parentId = $request->input($foreignKey);
    $parentRuta = null;

    foreach (glob(resource_path('meta_abms/config_form_*.json')) as $file) {
        $data = json_decode(file_get_contents($file), true);
        foreach ($data['subformularios'] ?? [] as $sub) {
            if ($sub['modelo'] === $modeloNombre) {
                $parentRuta = basename($data['carpeta_vistas'] ?? '');
                break 2;
            }
        }
    }

    if ($parentRuta && $parentId) {
        return redirect()->route("produccion.abms.{$parentRuta}.edit", $parentId)
                         ->with('success', 'Registro guardado correctamente.');
    }

    return null;
}
```

---

## 📌 Conclusión

**Este es el método oficial para edición inline de subformularios en KOI.**  
Brinda una solución versátil, limpia y altamente extensible, manteniendo la lógica desacoplada del frontend y backend. Listo para producción.
