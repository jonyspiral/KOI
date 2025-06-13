
# 🧼 Comando KOI - Limpiar Sistema (`koi:limpiar`)

**Fecha:** Junio 2025  
**Responsable:** Vicente  
**Ubicación:** `app/Console/Commands/LimpiarKoi.php`  
**Comando:** `php artisan koi:limpiar`

---

## 🎯 Propósito

Este comando limpia por completo los recursos cacheados de Laravel después de realizar cambios en:

- Formularios ABM (`create/edit/index`)
- Modelos generados (`fillable`, `fieldsMeta()`)
- Rutas web (`routes/web.php`)
- Configuraciones del sistema
- Vistas compiladas (Blade)

Es útil luego de ejecutar el `ABM Creator`, el `importador:tabla`, o tras despliegues.

---

## 🧰 Código del Comando

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class LimpiarKoi extends Command
{
    protected $signature = 'koi:limpiar';
    protected $description = '🧼 Limpieza post cambios KOI: vistas, cachés, rutas, config y archivos temporales';

    public function handle()
    {
        $this->info("🚿 Limpiando KOI...");

        $this->line("🧹 Borrando vistas compiladas...");
        File::cleanDirectory(storage_path('framework/views'));

        $this->line("🧠 Borrando caché de configuración...");
        Artisan::call('config:clear');
        $this->info(Artisan::output());

        $this->line("🔁 Borrando caché de rutas...");
        Artisan::call('route:clear');
        $this->info(Artisan::output());

        $this->line("📦 Borrando caché de eventos...");
        Artisan::call('event:clear');
        $this->info(Artisan::output());

        $this->line("🧠 Borrando caché de vista (blade)...");
        Artisan::call('view:clear');
        $this->info(Artisan::output());

        $this->line("📜 Borrando caché general...");
        Artisan::call('cache:clear');
        $this->info(Artisan::output());

        $this->info("✅ KOI limpio y listo. Si hay cambios en rutas o servicios, reiniciá Apache o php-fpm.");
    }
}
```

---

## 📦 Registro en `Kernel.php`

Agregar en `app/Console/Kernel.php`:

```php
protected $commands = [
    \App\Console\Commands\LimpiarKoi::class,
];
```

---

## 🚀 Ejecución

```bash
php artisan koi:limpiar
```

---

## 📌 Notas

- No borra archivos `config_form_*.json` ni vistas generadas por el ABM Creator.
- Se puede extender para limpiar archivos huérfanos o sincronizar metadata si se desea.

