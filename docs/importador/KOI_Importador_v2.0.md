
# 📦 KOI - Importador de Tablas v2.0

## ✨ Descripción General

El comando `importar:tabla` es una utilidad de línea de comandos de Laravel que permite importar una tabla desde SQL Server 2000 hacia MySQL, generar los modelos correspondientes en Laravel, y preparar la estructura necesaria para la integración con KOI (sincronizador, ABMs, etc).

---

## 🧪 Comando Artisan

```bash
php artisan importar:tabla {nombre_tabla} [opciones]
```

---

## ⚙️ Opciones disponibles

| Opción             | Descripción                                                                 |
|--------------------|-----------------------------------------------------------------------------|
| `--force-models`   | Fuerza la creación del modelo aunque ya exista.                             |
| `--force-table`    | Borra y recrea la tabla en MySQL.                                           |
| `--with-sql-model` | Genera también el modelo conectado a SQL Server.                            |
| `--fill-all`       | Llena `$fillable` con **todas** las columnas, no solo las PKs.              |
| `--skip-data`      | No importa los registros, solo estructura y modelos.                        |
| `--insert-simple`  | Inserta los datos sin intentar `updateOrInsert`. Mejora performance inicial.|

---

## 🧠 Funcionalidades Clave

### 1. **Importación desde SQL Server 2000**
- Lee estructura de la tabla con `sp_columns`.
- Lee claves primarias con `sp_pkeys`.
- Lee índices únicos con `sp_helpindex`.

### 2. **Generación de la tabla en MySQL**
- Traduce tipos de datos SQL Server a Laravel.
- Agrega automáticamente `id`, `created_at`, `updated_at`, `sync_status`.
- Crea índices únicos compuestos detectados.

### 3. **Generación de Modelos**
#### MySQL:
- `App\Models\{NombreModelo}`
- Incluye: `$table`, `$primaryKey`, `$timestamps`, `$fillable`, `$sincronizable`.
- Genera método `fieldsMeta()` con metadata e índices amigables.

#### SQL Server:
- `App\Models\Sql\{NombreModelo}`
- Incluye: `$table`, `$primaryKeySql`, `$incrementing = false`, `$fillable`, `$connection`.

### 4. **fieldsMeta()**
Generado automáticamente, contiene:
- Metadata por columna: tipo, nullable, default, primary.
- Índices únicos detectados de SQL Server:
```php
'indices' => [
  'idx_unico_cod_ruta_cod_paso' => [
    'columns' => ['cod_ruta', 'cod_paso'],
    'unique' => true
  ]
]
```

---

## 📁 Estructura de Archivos

```plaintext
app/
├── Console/
│   └── Commands/
│       └── ImportarTablaKoi.php
├── Models/
│   └── NombreModelo.php
│
├── Models/
│   └── Sql/
│       └── NombreModelo.php
```

---

## 📌 Limitaciones actuales

- Solo soporta conexión a `sqlsrv_koi`.
- No genera relaciones Eloquent automáticamente.
- Usa `sp_helpindex`, por lo que es específico para SQL Server 2000.
- No soporta índices múltiples con mismo nombre.

---

## 📦 Uso típico

```bash
php artisan importar:tabla articulos --force-models --force-table --with-sql-model --fill-all
```

---

## 🧩 Siguientes módulos que pueden usar esta estructura

- 🧠 `ABM Creator` (validaciones basadas en `fieldsMeta`)
- 🔄 `Sincronizador KOI` (basado en `primaryKeySql` o en `indices`)
- 🧪 Automatización de pruebas o importación masiva

---

## 🏁 Última Versión

**KOI Importador v2.0**  
- Actualizado: abril 2025  
- Compatible con: Laravel 10/11/12  
- Probado en: Ubuntu Server 20.04 con PHP 8.2 y FreeTDS + unixODBC
