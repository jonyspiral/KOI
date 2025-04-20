
# 🧾 Manual Técnico de Sincronización KOI ↔ SQL Server (2000, 2005, 2008)

## 📌 Contexto

Este documento resume las pruebas y limitaciones detectadas durante el desarrollo de sincronización entre Laravel y SQL Server 2000 (vía FreeTDS), con vistas a una futura migración a SQL Server 2005 o 2008.  
El objetivo es asegurar una implementación estable y compatible con todas las versiones mencionadas.

---

## ✅ Tipos de Campos Soportados y Reglas de Inserción

| Tipo SQL            | Laravel/FreeTDS          | Recomendación KOI                            |
|---------------------|--------------------------|----------------------------------------------|
| `VARCHAR(N)`        | `string`                 | Directo (máx N caracteres)                   |
| `CHAR(1)`           | `'S' / 'N'`              | Usar para checkboxes                         |
| `INT`               | `int`                    | Usar `CAST(valor AS INT)` vía `DB::raw()`    |
| `DECIMAL(10,2)`     | `float` / `decimal`      | Usar `CAST(valor AS DECIMAL(10,2))`          |
| `FLOAT`             | `float`                  | Usar `CAST(valor AS FLOAT)`                  |
| `DATETIME`          | `string`/`Carbon`        | Usar `CAST('YYYY-MM-DD HH:MM:SS' AS DATETIME)` |
| `TEXT`              | `string`                 | Evitar parámetros bound. Pasar literal directo. |

---

## ⚠️ Errores Comunes Detectados

| Código SQLSTATE | Error Técnico                                   | Solución                                      |
|-----------------|--------------------------------------------------|-----------------------------------------------|
| `22018`         | Operand type clash (text vs. decimal/datetime) | Usar `CAST()` en campos numéricos y fecha     |
| `HY090`         | Invalid string or buffer length                | Verificar longitud de campos `VARCHAR(N)`     |

---

## 🧰 Buenas Prácticas

1. **CAST obligatorio** para campos `DECIMAL`, `FLOAT`, `INT`, `DATETIME`.
2. **No usar binding** automático con campos `TEXT`, `DECIMAL`, `DATETIME`.
3. **Validar longitud** para `VARCHAR(N)` antes de enviar.
4. **Evitar `where()` con campos `TEXT`**.
5. **Usar `sync_status`** (`N`, `U`, `D`, `S`) como flag de sincronización.
6. **Controlar `timestamps`** manualmente desde Laravel (Laravel ↔ MySQL).

---

## 🧱 Ejemplo de Inserción Correcta

```php
DB::connection('sqlsrv_koi')->table('articulos_new')->insert([
    'cod_articulo' => 'ART001',
    'denom_articulo' => 'Zapatilla Urbana',
    'precio_unitario' => DB::raw("CAST(17999.90 AS DECIMAL(10,2))"),
    'fecha_lanzamiento' => DB::raw("CAST('2024-06-01 09:30:00' AS DATETIME)"),
    'created_at' => DB::raw("CAST('2024-06-01 09:30:00' AS DATETIME)"),
    'updated_at' => DB::raw("CAST('2024-06-01 09:30:00' AS DATETIME)"),
    'sync_status' => 'N'
]);
```

---

## 📄 Configuración Sugerida en JSON

```json
"precio_unitario": {
    "input_type": "decimal",
    "sql_type": "DECIMAL(10,2)",
    "sync": true,
    "nullable": false
},
"descripcion_larga": {
    "input_type": "textarea",
    "sql_type": "TEXT",
    "sync": true,
    "nullable": true
}
```

---

## 🔄 Flujo de Sincronización Actual

1. Laravel (MySQL) crea/actualiza el registro
2. Si el formulario es `sincronizable = true`:
   - Se filtran los campos con `sync = true`
   - Se hace `insert/update/delete` a SQL Server
   - Se mantiene `sync_status = N/U/D/S`
3. La sincronización se registra en el log y se muestra mensaje.

---

## 🔮 Proyecciones y Migración

- ✅ Este sistema ha sido testeado con SQL Server 2000.
- 🚀 Plan próximo: migración a SQL Server 2005/2008
- 🧱 Gracias a los `CAST()` y a la estandarización en KOI, se espera compatibilidad inmediata.

---

📅 Generado automáticamente: 2025-04-18 18:04:47
