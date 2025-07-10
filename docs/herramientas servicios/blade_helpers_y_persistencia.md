# 🧩 Implementación de BladeServiceProvider y Helpers en KOI

## 📦 Objetivo

Centralizar y estandarizar los helpers de filtros, sort y multiselección en los ABMs de KOI, mediante la creación de directivas Blade y su registro en un `BladeServiceProvider`.

---

## 🛠️ BladeServiceProvider

```php
// app/Providers/BladeServiceProvider.php

use Illuminate\Support\Facades\Blade;

public function boot()
{
    Blade::directive('sortableth', function ($campo) {
        return "<?php echo view('components.filtros.sortable-th', ['campo' => $campo]); ?>";
    });

    Blade::directive('ordenIcon', function ($campo) {
        return "<?php echo view('components.filtros.orden-icon', ['campo' => $campo]); ?>";
    });

    Blade::directive('filterInput', function ($campo) {
        return "<?php echo view('components.filtros.input', ['campo' => $campo]); ?>";
    });

    Blade::directive('filterInputLike', function ($campo) {
        return "<?php echo view('components.filtros.input-like', ['campo' => $campo]); ?>";
    });

    Blade::directive('filterSelect', function ($expresion) {
        return "<?php echo view('components.filtros.select', ['campo' => {$expresion}[0], 'opciones' => {$expresion}[1]]); ?>";
    });
}
```

---

## 🧱 Componentes Blade Relacionados

**1. Sortable**
```blade
@sortableth('marca', 'Marca')
```

**2. Input común**
```blade
@filterInput('denom_articulo')
```

**3. Input tipo LIKE**
```blade
@filterInputLike('descripcion')
```

**4. Select simple**
```blade
@filterSelect('vigente', ['S' => 'Sí', 'N' => 'No'])
```

---

## 🧬 Relación con Trait de Persistencia

Se utiliza el Trait:
```php
use App\Traits\FiltrosPersistentes;
```

En el Controller se define:
```php
$requestFiltrado = $this->manejarFiltros($request, 'articulocolor_filtros', $campos);
if ($requestFiltrado instanceof \Illuminate\Http\RedirectResponse) return $requestFiltrado;
```

Esto permite que los filtros se mantengan entre páginas (`paginate()->appends()`), y al usar botones de "reset" se limpien correctamente.

---

## ✅ Componente Blade para Multiselección

En lugar de usar directiva Blade (que no escala bien con `:`), se utiliza un componente:

```blade
<x-filtros.select-multiple campo="marca" :opciones="$marcas->pluck('denom_marca', 'denom_marca')" />
```

Funciona con Select2 y acepta múltiples selecciones, manteniendo la persistencia si se usa con `FiltrosPersistentes`.

---

## ✅ Caso especial: campo sin FK

Para `forma_comercializacion` se construyen las opciones así:

```php
$formasComercializacion = Articulo::whereNotNull('forma_comercializacion')
    ->select('forma_comercializacion')
    ->distinct()
    ->orderBy('forma_comercializacion')
    ->pluck('forma_comercializacion', 'forma_comercializacion');
```

Y se usa:

```blade
<x-filtros.select-multiple campo="forma_comercializacion" :opciones="$formasComercializacion" />
```

---

## 🗓️ Fecha

Generado: 2025-07-09