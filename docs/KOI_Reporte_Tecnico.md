
# 🧾 Reporte Técnico - Proyecto KOI (Laravel + SQL Server 2000)
**Fecha:** Abril 2025  
**Responsable:** Vicente  
**Framework:** Laravel 12  
**PHP:** 8.2  
**Frontend:** Blade + Alpine.js  
**Bases de Datos:** SQL Server 2000 (ODBC + FreeTDS) y MySQL 8

---

## 🏗️ Estructura del Proyecto

```
/var/www/koi2_v1/
├── app/
│   ├── Console/Commands/              # Artisan commands personalizados
│   ├── Http/Controllers/
│   │   ├── Produccion/
│   │   └── Sistemas/Importar/         # Formularios web
│   ├── Models/                        # Modelos Eloquent (MySQL)
│   └── Models/Sql/                    # Modelos SQL Server (ODBC)
├── resources/views/sistemas/importar/ # Vistas de formulario
├── routes/web.php                     # Rutas web
```

---

## 🔌 Conexión a Base de Datos

`.env`:

```
DB_CONNECTION=mysql
DB_DATABASE=koi2
DB_USERNAME=root
DB_PASSWORD=

SQLSRV_KOI_CONNECTION=odbc
DB_ODBC_DSN=sqlsrv_koi
DB_ODBC_HOST=192.168.2.100
```

---

## ⚙️ Funcionalidades

### 1. Importación de Tablas `php artisan importar:tabla`

- Crea estructura de MySQL basada en `sp_columns`
- Detecta claves únicas (`sp_pkeys`)
- Soporte para:
  - `--force-models`
  - `--force-table`
  - `--with-sql-model`
  - `--unique=campo1,campo2`
  - `--fill-all`

### 2. Generación de Modelos

- Eloquent MySQL + SQL Server (`App\Models` y `App\Models\Sql`)
- Genera `fillable` automáticamente
- Incluye claves únicas y todos los campos (`--fill-all`)

### 3. Formulario Web

- URL: `/sistemas/importar/form`
- Permite seleccionar tabla desde SQL Server
- Ejecuta Artisan en segundo plano
- Salida técnica explicativa

### 4. Subformularios Inline

- Edición de registros hijas
- Blade + Alpine.js
- Usa configuración en JSON (`meta_abms/config_form_<modelo>.json`)

### 5. ABM Creator

- Generador de CRUD completo
- Basado en configuración JSON
- Compatibilidad con sincronización

---

## 🔁 Sincronización

- Detecta `sync_status` y empuja cambios hacia SQL Server
- Invocación manual o futura automatización
- Controlado por modelo

---

## 🧠 Observaciones Técnicas

- Backslashes corregidos
- Generación dinámica de `fillable` y claves únicas
- Listo para integración con APIs o migración a nuevos motores

---

## 🏁 Estado Actual

✅ Importación y generación de modelos completa  
✅ Formulario web funcional  
✅ ABM Creator en uso con mejoras en curso  
🚧 Mejora de sincronización y relaciones

---

## Próximos Pasos

- Prueba de relaciones en modelos (`belongsTo`)
- Consolidación de lógica de sincronización bidireccional
- Refactor del ABM Creator para casos especiales
