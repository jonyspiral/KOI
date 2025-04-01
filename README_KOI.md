# KOI — Sistema de Gestión Integral para Spiral Shoes

> Proyecto en Laravel + MySQL + SQL Server · Última actualización: 2025-03-30

KOI es un sistema integral desarrollado para la empresa **Spiral Shoes**, gestionando sus procesos operativos, comerciales y de producción. El sistema se encuentra en proceso de migración desde SQL Server 2000 hacia una arquitectura moderna basada en Laravel y MySQL, integrando funcionalidades nuevas y reutilizando datos históricos.

---

## ⚙️ Tecnologías utilizadas

- **Laravel 12**
- **PHP 8.1+**
- **MySQL** como base de datos principal (Laravel)
- **SQL Server 2000** conectado mediante **ODBC en Ubuntu Server**
- **Apache/Nginx** en entorno Ubuntu
- **VSCode + Continue** como entorno de desarrollo conectado a GPT-4
- **GitHub (SSH)** para versionado del código

---

## 📁 Estructura de carpetas (resumen)

/var/www/koi2_v1 → entorno de desarrollo  
/var/www/koi2     → entorno de producción estable

Cada entorno tiene su propio archivo `.env` y configuración individual de virtual host.

---

## 🚀 Instalación del proyecto (devs)

```bash
git clone git@github.com:jonyspiral/KOI.git
cd KOI
cp .env.example .env
composer install
php artisan key:generate
npm install && npm run dev
```

---

## 🔌 Conexión a bases de datos

### MySQL (Laravel)

Configurado por defecto en `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koi2
DB_USERNAME=usuario
DB_PASSWORD=clave
```

### SQL Server (datos históricos/productivos)

Conexión vía ODBC definida en `.env` y `config/database.php`:

```dotenv
DB_SQLSRV_HOST=192.168.x.x
DB_SQLSRV_DATABASE=KOI_PRODUCCION
DB_SQLSRV_USERNAME=usuario
DB_SQLSRV_PASSWORD=clave
```

Modelos que usan SQL Server se ubican en `App\Models\Sql`.

---

## 🧠 ABM Creator

KOI cuenta con un sistema automatizado para generar **ABMs completos** (altas, bajas, modificaciones) con:

- Modelo
- Policy
- FormRequest
- Validaciones (incluyendo claves únicas)
- Controlador con lógica CRUD
- Vistas Blade
- Rutas
- Permisos (si aplica)

### Comando para crear un ABM:

```bash
php artisan abm:crear NombreDelModelo
```

Opciones disponibles:
- `--fill-all`: incluye todos los campos en `$fillable`
- `--unique=campo1,campo2`: define claves únicas
- `--with-sql-model`: genera también el modelo para SQL Server

---

## 🔁 Importador de tablas (`importar:tabla`)

El comando `importar:tabla` permite **importar una tabla desde SQL Server o MySQL** y generar el modelo Eloquent correspondiente.

### Ejemplo:

```bash
php artisan importar:tabla tareas_detalle --fill-all --unique=orden,tarea
```

Lo que hace:
- Crea el modelo `TareaDetalle` en `App\Models`
- Si usás `--with-sql-model`, también lo crea en `App\Models\Sql`
- Incluye claves primarias como `$fillable`
- Agrega `public $timestamps = false;` si no detecta timestamps
- Genera índice único con `Schema::table(...)->unique([...])`
- No define `$primaryKey`, usa `id` como autoincremental

---

## 🧩 Módulo de Producción

### Funcionalidades principales:
- Lanzamiento de tareas
- Cumplido de tareas
- Confirmación de stock (cuando tareas finalizan en sección 60: Empaque)

### Estructura interna:
- Controladores: `App\Http\Controllers\Produccion`
- Modelos: `App\Models\...`
- Vistas Blade: `resources/views/produccion/`
- Servicios: `App\Services\Produccion`

---

## 📂 Organización general

```bash
app/
├── Models/              ← Modelos Eloquent
│   └── Sql/             ← Modelos conectados a SQL Server
├── Http/
│   └── Controllers/     ← Separados por módulos (Comercial, Producción, etc)
├── Services/            ← Lógica de negocio modular
resources/
└── views/               ← Vistas Blade separadas por módulo
```

---

## 🛡 Seguridad y Git

- `.env` está ignorado por `.gitignore`
- Se incluye `.env.example` como plantilla de configuración
- El proyecto está conectado vía SSH a GitHub para evitar uso de tokens

---

## 📬 Contacto

Para colaborar, escribir a **Vicente (aka Johnny)** o abrir un issue en el repo:  
🔗 [https://github.com/jonyspiral/KOI](https://github.com/jonyspiral/KOI)

---

¡Gracias por contribuir al desarrollo de Spiral Shoes!
