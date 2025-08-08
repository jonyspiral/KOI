# Guía de Sincronización con Mercado Libre (ML)

Este documento resume el flujo, servicios, controladores, vistas y lógica de sincronización de variantes de productos con Mercado Libre, así como la creación de logs y el manejo de filtros, en base a la conversación y desarrollo realizado.

---

## 1. Objetivo

Sincronizar datos de **stock**, **precio** y **SCF** (Seller Custom Field) entre el sistema local y Mercado Libre, permitiendo:

- Sincronización masiva por **filtros**.
- Sincronización de **variantes seleccionadas**.
- Sincronización de **pendientes**.
- Registro detallado de logs por variante.
- Interacción directa desde la vista (Blade) con información clara del resultado.

---

## 2. Flujo General

1. **Vista Blade (**\`\`**)**

   - Muestra filtros y tabla de variantes ML.
   - Permite seleccionar registros o aplicar filtros.
   - Botones para:
     - Sincronizar seleccionados.
     - Sincronizar filtrados.
     - Sincronizar pendientes.
   - Campos editables: SCF, precio, stock, overrides.
   - Hover en estado de sincronización para mostrar `sync_log` detallado.

2. **Controlador (**\`\`**)**

   - **syncSeleccionados()** → Sincroniza variantes seleccionadas.
   - **syncFiltrados()** → Aplica filtros y sincroniza.
   - **syncPendientes()** → Sincroniza las variantes con `sync_status = 'U'`.
   - Llama al servicio `MlSyncService` configurando:
     - Modo (`seleccionados`, `filtrados`, `pendientes`).
     - Campo (`stock`, `precio`).
     - IDs de variantes.

3. **Servicio (**\`\`**)**

   - Recibe parámetros del controlador.
   - Obtiene las variantes según el modo.
   - Para cada variante:
     - Guarda SCF, precio, stock según overrides o datos SKU.
     - Llama a `syncStock()` o `syncPrecio()`.
     - Actualiza `sync_status` y `sync_log` detallado.
     - Inserta registro en `ml_sync_logs`.

4. **Modelo (**\`\`**)**

   - Método `actualizarStockML()`:
     - Detecta si la publicación tiene variantes.
     - Determina si es FULL o no.
     - Llama a la API de ML para actualizar stock.
   - Método `syncPrecio()`:
     - Actualiza precio mediante API ML.

5. **Logs (**\`\`**)**

   - Modelo para registrar:
     - ID de variante.
     - Campo sincronizado.
     - Resultado (éxito/error).
     - Mensaje detallado.

---

## 3. Estructura de Carpetas y Archivos

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Mlibre/
│   │       ├── MlSyncController.php
│   │       └── MlibreVariantesController.php
│   ├── Models/
│   │   ├── MlVariante.php
│   │   └── MlSyncLog.php
│   └── Services/
│       └── Mlibre/
│           └── MlSyncService.php
resources/
└── views/
    └── mlibre/
        └── variantes/
            └── index.blade.php
```

---

## 4. Lógica de Sincronización

### 4.1 Flujo de Ejecución

- **Vista** envía POST al controlador.
- **Controlador** prepara filtros, IDs, campo y llama al servicio.
- **Servicio** itera variantes y:
  1. Guarda datos locales según overrides.
  2. Llama a API ML para actualizar campo.
  3. Actualiza `sync_status` (`S`, `E`, `U`, `N`).
  4. Guarda mensaje detallado en `sync_log`.
  5. Inserta en `ml_sync_logs`.

### 4.2 Campos Sincronizados

- **Stock**:
  - Toma de SKU salvo override manual.
  - API: `/items/{id}` o `/items/{id}/variations/{variation_id}`.
- **Precio**:
  - Toma de SKU salvo override manual.
  - API: `/items/{id}`.
- **SCF**:
  - Actualiza en local y ML.

---

## 5. Manejo de FULL y Campañas

- **FULL**:
  - Si `inventory_id` no es null → no editable stock.
- **Campañas**:
  - Limitaciones para modificar precio si está en campaña activa.

---

## 6. Mejoras Pendientes

- Incluir validación y log más detallado para precio.
- Hover con detalle ampliado para errores.
- Sincronizar ambos campos en una misma ejecución y registrar por separado.
- Integrar API de campañas para conocer restricciones.

---

## 7. Estado Final

Con este flujo se tiene:

- Sincronización robusta para stock y precio.
- Registro histórico en `ml_sync_logs`.
- Interfaz clara para ver resultados.
- Soporte para diferentes modos de sincronización.

---

Este documento resume la implementación actual y es la base para próximas optimizaciones.

