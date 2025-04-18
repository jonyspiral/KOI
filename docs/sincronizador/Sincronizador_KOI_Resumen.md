# 🧩 Sincronizador KOI – Estrategia Oficial

**Fecha de registro:** 2025-04-14

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
- JSON config_form_*.json
- Metadata fieldsMeta() con claves, tipos, nullable, etc.
- Definición de índices únicos (indices[])

---

## 2. Uso del Campo sync_status

Cada registro de la tabla importada tendrá un campo sync_status con posibles valores:

| Estado | Descripción |
|--------|-------------|
| N      | Nuevo registro (no sincronizado) |
| U      | Actualizado localmente (pendiente de sincronización) |
| D      | Eliminado (si se implementa soft-delete) |
| S      | Sincronizado exitosamente |

---

## 🔁 Flujo de Sincronización

1. Detección:
   - Se detectan registros con sync_status != 'S'

2. Validación de existencia en SQL Server:
   - Se usa el modelo App\Models\Sql\NombreModelo
   - Se utiliza la primaryKeySql generada en fieldsMeta()

3. Insert / Update / Delete:
   - Si no existe en SQL Server → INSERT
   - Si existe pero cambió → UPDATE
   - Si fue eliminado localmente → DELETE o marcar como D

4. Confirmación:
   - Si se sincronizó correctamente → sync_status = 'S'

---

## 🧠 Inteligencia Automática

Gracias al uso de fieldsMeta() y JSON:
- Se pueden construir updateOrInsert automáticos
- Detecta claves de negocio compuestas o no estándar
- Compatible con lógica Laravel (id, timestamps, fillables)

---

## 🚀 Posibles Extensiones

- Colas con queue:work para evitar bloquear KOI
- Comandos periódicos (cron) para auto-sincronizar
- Inversión del flujo: sincronizar cambios desde SQL Server a KOI
- Auditoría: usar tablas _logs para registro de sincronización

---

## 🧪 Debug y Métricas

- Medición de tiempos con microtime(true) (ya implementado)
- Logging con Log::info() para seguimiento detallado
- Monitoreo por tabla, campo, estado o tiempo de respuesta

---

## 📌 Observaciones

- Este módulo complementa al ABM Creator con control de sync_status
- La creación y actualización deben agregar:
  - created_at, updated_at (si usa timestamps)
  - sync_status = 'N' o 'U' según corresponda
- KOI puede operar incluso si el servidor legacy no está activo, usando MySQL como cache de datos y luego sincronizando cuando esté disponible.

---

**Fin del documento**
