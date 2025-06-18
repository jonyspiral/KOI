# 📚 Trait `PersisteFiltrosTrait` — Documentación

Este trait permite **guardar y aplicar filtros de listados automáticamente** en sesiones de Laravel. Ideal para controladores `index()` que usan filtros múltiples en tablas.

---

## 🧩 Ubicación

```
app/Traits/PersisteFiltrosTrait.php
```

---

## 🧠 ¿Qué hace?

- Guarda automáticamente los filtros GET (como los de un buscador o select2).
- Restaura los filtros si el usuario vuelve a la pantalla.
- Permite borrar todos los filtros con `?reset=1`.

---

## 🔧 Código completo del trait

```php
<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

/**
 * Trait PersisteFiltrosTrait
 *
 * Este trait permite guardar automáticamente filtros aplicados en listados (GET)
 * dentro de la sesión, para mantenerlos activos entre navegaciones.
 *
 * Uso típico:
 *   $requestFiltrado = $this->manejarFiltros($request, 'clave_filtros', ['campo1', 'campo2']);
 *   if ($requestFiltrado instanceof RedirectResponse) return $requestFiltrado;
 *   $request = $requestFiltrado;
 */
trait PersisteFiltrosTrait
{
    /**
     * Aplica persistencia de filtros GET en sesión y permite limpiarlos.
     *
     * @param Request $request
     * @param string $claveSesion
     * @param array $camposPermitidos
     * @return Request|RedirectResponse
     */
    public function manejarFiltros(Request $request, string $claveSesion, array $camposPermitidos): Request|RedirectResponse
    {
        if ($request->get('reset') === '1') {
            session()->forget($claveSesion);
            return redirect()->route($request->route()->getName());
        }

        if ($request->isMethod('get') && $request->query()) {
            $filtros = collect($request->query())
                ->only($camposPermitidos)
                ->toArray();

            session([$claveSesion => $filtros]);
        }

        $filtrosGuardados = session($claveSesion, []);
        $request->merge($filtrosGuardados);

        return $request;
    }
}
```

---

## ✅ Ejemplo de uso en un controlador

```php
use App\Traits\PersisteFiltrosTrait;

class MlibreVariantesController extends Controller
{
    use PersisteFiltrosTrait;

    public function index(Request $request)
    {
        $campos = [
            'ml_id', 'variation_id', 'status', 'sync_status', 'sort', 'dir', 'page'
        ];

        $requestFiltrado = $this->manejarFiltros($request, 'ml_variantes_filtros', $campos);

        if ($requestFiltrado instanceof \Illuminate\Http\RedirectResponse) {
            return $requestFiltrado;
        }

        $request = $requestFiltrado;

        // Luego continuás con tu lógica de filtros y paginación...
    }
}
```

---

## 🧼 Limpieza de filtros

Agregá este botón en el Blade para permitir reset:

```blade
<a href="{{ route('mlibre.variantes.index', ['reset' => 1]) }}" class="btn btn-danger">
    ❌ Borrar filtros guardados
</a>
```

---

## 📌 Ventajas

- Reutilizable en múltiples controladores.
- Aumenta la experiencia del usuario.
- Se integra fácilmente con cualquier sistema de filtros.