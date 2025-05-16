
# 🧩 Sincronizador KOI – Estrategia Oficial

**Fecha de última actualización:** 2025-05-03

---

## ✅ ¿Qué es?
El **Sincronizador KOI** es un módulo técnico que permite mantener actualizadas las tablas entre:
- MySQL (Laravel, ambiente KOI)
- SQL Server 2000 (servidor legacy)

Su objetivo es:
- Mantener integridad entre bases de datos
- Permitir trabajar offline o con contingencia de conexión
- Detectar y transferir automáticamente datos nuevos, modificados o eliminados

---

## ⚙️ ¿Cómo se construye?

### 1. Importador de Tablas
Se utiliza el comando:

```bash
php artisan importar:tabla {tabla} --with-sql-model --fill-all
```

Esto genera automáticamente:
- Tabla MySQL con estructura idéntica a SQL Server
- Modelo Eloquent MySQL
- Modelo Eloquent SQL Server (en App\Models\Sql\)
- JSON `config_form_*.json`
- Metadata `fieldsMeta()` con claves, tipos, nullable, etc.
- Definición de índices únicos (`indices[]`)
- Inclusión de orden y propiedades de sincronización (`sync`) y edición (`readonly`) en cada campo

---

## 2. Uso del Campo `sync_status`

Cada registro de la tabla importada tendrá un campo `sync_status` con posibles valores:

| Estado | Descripción |
|--------|-------------|
| N      | Nuevo registro (no sincronizado) |
| U      | Actualizado localmente (pendiente de sincronización) |
| D      | Eliminado (si se implementa soft-delete) |
| S      | Sincronizado exitosamente |

Este campo se actualiza automáticamente al crear (`N`), modificar (`U`) o eliminar (`D`) un registro. La sincronización exitosa lo establece en `S`.

---

## 🔁 Flujo de Sincronización

1. **Detección**:
   - Se detectan registros con `sync_status` distinto de `'S'`

2. **Validación de existencia en SQL Server**:
   - Se usa el modelo `App\Models\Sql\NombreModelo`
   - Se utiliza la propiedad `primaryKeySql` generada en `fieldsMeta()`

3. **Insert / Update / Delete**:
   - Si no existe en SQL Server → `INSERT`
   - Si existe pero cambió → `UPDATE`
   - Si fue eliminado localmente → `DELETE` o marcar como `D`

4. **Confirmación**:
   - Si se sincronizó correctamente → `sync_status = 'S'`

---

## 🧠 Inteligencia Automática

Gracias al uso de `fieldsMeta()` y `config_form_*.json`:
- Se pueden construir `updateOrInsert` automáticos
- Detecta claves de negocio compuestas o no estándar
- Compatible con lógica Laravel (`id`, `timestamps`, `fillable`)
- Realiza casting correcto para tipos de datos SQL Server antiguos (ej. `CAST(x AS DATETIME)`)

---

## ⚙️ Consideraciones Técnicas del Sincronizador

- Todos los valores se escapan y transforman antes de enviarse a SQL Server.
- Se utilizan `DB::raw()` y casting explícito para `date`, `decimal`, `int`, `checkbox` y otros tipos.
- El campo clave (`$campoClave`) en `UPDATE` y `DELETE` se castea o escapa según su tipo.

```php
$valorClaveSql = is_numeric($valorClave)
    ? $valorClave
    : "'" . str_replace("'", "''", $valorClave) . "'";
```

- Los valores tipo texto se procesan mediante `wrapStr()` que encapsula y escapa correctamente los valores string.

---

## 🚀 Posibles Extensiones

- Colas con `queue:work` para evitar bloquear KOI
- Comandos periódicos (`cron`) para auto-sincronizar
- Inversión del flujo: sincronizar cambios desde SQL Server a KOI
- Auditoría: usar tablas `_logs` para registro de sincronización
- Validaciones adicionales por tipo de campo

---

## 🧪 Debug y Métricas

- Medición de tiempos con `microtime(true)`
- Logging con `Log::info()` y `Log::debug()` para seguimiento detallado
- `dd()` y trazas intermedias disponibles con modo `debug = true`
- Monitoreo por tabla, campo, estado o tiempo de respuesta

---

## 📌 Observaciones Finales

- Este módulo complementa al **ABM Creator** con control de `sync_status`
- La creación y actualización deben agregar:
  - `created_at`, `updated_at` (si usa timestamps)
  - `sync_status = 'N'` o `'U'` según corresponda
- KOI puede operar incluso si el servidor legacy no está activo, usando MySQL como cache de datos y luego sincronizando cuando esté disponible.
