
# 📄 Documentación KOI: Helpers de Filtros y FilterProvider

## 🧩 Objetivo
Establecer un estándar de renderizado de filtros activos en vistas analíticas del sistema KOI, centralizando su procesamiento en una clase `FilterProvider` y un conjunto de Blade directives reutilizables.

---

## 📁 Archivos Involucrados

- `app/Helpers/FilterProvider.php`
- `app/Providers/BladeServiceProvider.php`
- `resources/views/components/filtros/*.blade.php`
- Blades como `index.blade.php` en módulos analíticos

---

## 🧠 Lógica General

### 🔎 `FilterProvider::getActiveLabels(Request|array $filters)`

Devuelve una lista legible de los filtros activos, resolviendo nombres por ID o códigos, e indicando si se trata de filtros parciales (LIKE).

**Ejemplo de salida:**
```php
['*POW SKATE*', 'Lanzamiento', 'Discontinuo', 'Stock Central']
```

**Casos tratados:**
- `tipo_producto_stock`: Usa mapa hardcodeado.
- `vigente`: Convierte `S`/`N` en etiquetas.
- `familia`, `linea`, `almacen`: Consulta modelos asociados.
- `forma_comercializacion`: Texto libre.
- Filtros con LIKE: Añade `*` como prefijo y sufijo.

---

## 🧩 BladeServiceProvider

Define directivas:

- `@filterInput('campo')`
- `@filterInputLike('campo')`
- `@filterSelect('campo', opciones)`
- `@filterSelectMultiple('campo', opciones)`
- `@sortableth('campo', 'Label')`
- `@ordenIcon('campo')`

---

## 🧪 Ejemplo de uso en blade

```blade
@if(request()->has('aplicar'))
    @php
        $activeFilters = \App\Helpers\FilterProvider::getActiveLabels(request()->all());
    @endphp
    @if(count($activeFilters))
        <div class="mb-3">
            <strong>🧮 Active filters:</strong>
            @foreach($activeFilters as $label)
                <span class="badge bg-primary me-1">{{ $label }}</span>
            @endforeach
        </div>
    @endif
@endif
```

---

## ✅ Estándar KOI
Este patrón debe usarse en todos los módulos analíticos que presenten filtros en tabla para mantener coherencia y claridad visual para el usuario final.
