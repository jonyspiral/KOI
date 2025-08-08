# 🧩 Infraestructura KOI - Producción y Desarrollo
sERVIDORES FISICOS
1_ 
Marca / Modelo	HP DL380 G5 2x(Intel Xeon E5335 @ 2.00GHz) 16GB (8x2GB)
Hostname	HYPERV01.spiral.local
SN / Product ID	2UX74604ND / 433525-001 
Sistema operativo	Windows Server 2012 R2
Arreglo Discos	
	
IP	192.168.2.201
	
RDP	192.168.2.201:33201
Acceso TV	918 894 612 :: Root,350
Usuario local	administrador :: Root,2317!
	
Antivirus 	-
Funcion	HYPERVISOR
	
iLO 2	192.168.2.193 
	Administrator  ::  Root,2317!
	Spiral Shoes  ::  SpiralServix

    
2_	
Marca / Modelo	HP DL380 G5 2x(Intel Xeon E5335 @ 2.00GHz) 16GB (8x2GB)
Hostname	HYPERV02.spiral.local
SN / Product ID	USE842N3RJ / 391835-B21 
Sistema operativo	Windows Server 2012 R2
Arreglo Discos	
	
IP	192.168.2.202
RDP	192.168.2.202
Acceso TV	918 805 848 :: Root,350
Usuario local	HYPERV02\Administrador :: Root,2317!
	
Antivirus 	-
Funcion	HYPERVISOR
	
iLO 2	192.168.2.196
	Administrator :: Root,2317!
	ILOHP2
	

Este documento describe la configuración actual de los entornos **Producción** y **Desarrollo** del sistema KOI, incluyendo servidores, rutas, y recomendaciones de uso.

---

## 🌐 Entornos

| Entorno    | Servidor | URL                                                    | Ruta en disco      | Estado   |
| ---------- | -------- | ------------------------------------------------------ | ------------------ | -------- |
| Producción | NGINX    | [http://192.168.2.210](http://192.168.2.210)           | `/var/www/koi2`    | ✅ Activo |
| Desarrollo | Apache   | [http://192.168.2.210:8191](http://192.168.2.210:8191) | `/var/www/koi2_v1` | ✅ Activo |

---

## 🖥️ Servidores Web

### NGINX (Producción)

- Sirve la app en el puerto `80`
- No usa `.htaccess` (toda la configuración está en el archivo `server`)
- Ruta: `/etc/nginx/sites-available/koi2`
- Requiere configuración manual de `fastcgi_pass` para conectar con PHP-FPM
- Importante: los errores 500 en Laravel suelen deberse a problemas con rutas duplicadas o `fastcgi` mal configurado

### Apache (Desarrollo)

- Sirve `koi2_v1` en el puerto `8191`
- Usa `.htaccess` para routing Laravel
- Ruta del VirtualHost: `/etc/apache2/sites-available/koi2_v1.conf`

---

## 🧠 Consideraciones

- Esta separación asegura que los cambios en desarrollo **no afectan** la producción.
- Cada entorno tiene su propio `.env`, base de datos, y configuración independiente.
- Apache es ideal para desarrollo rápido y flexible.
- NGINX es rápido y más robusto para entornos de producción.

**⚠️ Error común documentado:** Si aparece un error 500 en producción y Laravel lanza:
```
Cannot use Illuminate\Support\Facades\Route as Route because the name is already in use
```
Esto indica una doble importación en `routes/web.php`. Basta con eliminar la línea `use Illuminate\Support\Facades\Route;` si ya se encuentra definida o está en conflicto.

---

## 📁 Estructura de Carpetas

```
/var/www/
├── koi2/        # Producción
│   └── public/  # Apunta NGINX
├── koi2_v1/     # Desarrollo
│   └── public/  # Apunta Apache (puerto 8191)
```

---

## 🛡 Recomendación Actual

Mantener la configuración **mixta** actual:

- NGINX en puerto 80 sirviendo `/var/www/koi2` para producción
- Apache en puerto 8191 sirviendo `/var/www/koi2_v1` para desarrollo

Este esquema:

- Evita conflictos
- Permite probar nuevas funciones
- Asegura estabilidad

---

## 🧪 Diagnóstico rápido

Usar el comando Artisan personalizado:

```bash
php artisan koi:diagnostico
```

Para verificar:

- Ambiente (`APP_ENV`)
- Base de datos conectada
- Permisos de carpetas
- Rutas cargadas

---

## 🔐 Gestión de sesiones en producción

**Recomendación oficial para producción:**

```dotenv
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

- Laravel almacenará sesiones en archivos cifrados en `storage/framework/sessions/`
- No se requieren migraciones ni base de datos
- Es ideal para un servidor único (como el de KOI actual)
- Es más rápido y menos propenso a errores que usar `database` si no es estrictamente necesario

**Permisos necesarios:**

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

Para entornos escalables o multi-servidor, se podría considerar `redis` o `database`, pero no es necesario actualmente.

---

## 📄 Archivo creado: Abril 2025

Infraestructura validada y confirmada por Sofía para Vicente ✨