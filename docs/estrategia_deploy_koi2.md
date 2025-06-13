# 🧩 Estrategia de Deploy KOI2 (Producción)

## ✅ Objetivo

Realizar despliegues de la aplicación KOI2 de manera ordenada, permitiendo:
- Deploy total del **código** desde `/var/www/koi2_v1` a `/var/www/koi2`
- Deploy **selectivo de base de datos**: solo se sincronizan tablas nuevas permitidas
- Evitar errores en `.env` o sobrescritura de datos reales

---

## 📁 Estructura del Deploy

| Componente        | Tipo de Deploy     | Método                                |
|-------------------|--------------------|----------------------------------------|
| Código Laravel     | 🔁 Total           | `rsync` desde koi2_v1 (excluyendo `.env`) |
| Configuración Laravel (`.env`) | 🔁 Manual por entorno | Producción apunta a DB `koi2` |
| Base de datos      | 🎯 Selectivo       | Solo tablas listadas en `sync_allowed_tables.txt` |

---

## 🛠 Archivos involucrados

- `deploy_koi2.sh`  
  Script ejecutable de deploy: instala dependencias, limpia cachés, sincroniza tablas

- `sync_allowed_tables.txt`  
  Lista de tablas permitidas para clonar desde `koi2_v1` a `koi2`  
  Ubicación: `/var/www/koi2/deploy/sync_allowed_tables.txt`

---

## 🔁 Flujo de Deploy

1. Subir código a `koi2_v1`
2. Verificar migraciones nuevas
3. Editar `.env` en `koi2` si es necesario
4. Ejecutar:

```bash
cd /var/www/koi2
./deploy/deploy_koi2.sh
```

5. Verificar con:

```bash
php artisan migrate:status
mysql -u jony -pRoute667 -e "SHOW TABLES FROM koi2 LIKE 'ml_%';"
```

---

## 🧠 Convenciones

- `koi2` = base de datos de producción
- `koi2_v1` = base de desarrollo y staging
- Solo tablas nuevas internas deben sincronizarse (ML, ABM, cache, config, etc.)

---

## 📋 Validaciones automáticas (opcional futuro)

- Validar que `.env` no apunte a `koi2_prod`
- Mostrar cantidad de registros clonados por tabla
- Generar log `deploy.log`

---

## 🧾 Última revisión
# 🚀 Estrategia de Deploy KOI2 a Producción

## 🧾 Descripción

Este procedimiento automatiza el paso de la aplicación Laravel desde el entorno de desarrollo (`/var/www/koi2_v1`) al entorno de producción (`/var/www/koi2`), garantizando una transición limpia y sin errores manuales.

---

## 🧰 Comando Artisan

```bash
php artisan deploy:koi2
```

---

## ⚙️ Funcionalidad del Comando

1. **Sincronización de Archivos**
   - Usa `rsync` para copiar todos los archivos desde `koi2_v1` a `koi2`.
   - Excluye el archivo `.env` para no sobreescribir la configuración de producción.

2. **Limpieza de Cachés**
   - Ejecuta automáticamente:
     - `php artisan config:clear`
     - `php artisan cache:clear`
     - `php artisan view:clear`
     - `php artisan route:clear`
     - `php artisan optimize`

3. **Reinicio de Apache**
   - Ejecuta: `sudo systemctl restart apache2`
   - Reinicia el servidor web para aplicar los cambios.

---

## 📁 Ubicación del Comando

El archivo del comando se encuentra en:

```
app/Console/Commands/DeployKoi2.php
```

Y debe estar registrado en `app/Console/Kernel.php`:

```php
protected $commands = [
    \App\Console\Commands\DeployKoi2::class,
];
```

---

## ✅ Requisitos

- Tener configurado `sudo` para el usuario actual (para usar `rsync` y reiniciar Apache).
- Tener permisos de escritura sobre `/var/www/koi2/`.
- `rsync` y `systemctl` disponibles en el sistema.

---

## 🔐 Seguridad

Este comando solo debe ser ejecutado por usuarios autorizados. Se recomienda proteger el servidor con acceso restringido vía SSH.

---

## 📦 Resultado Esperado

Una vez ejecutado:

- Los archivos de desarrollo se copian a producción.
- La aplicación queda lista para uso en `https://koi2.spiralshoes.com`.
- Mercado Libre puede conectarse al entorno de producción sin errores.

