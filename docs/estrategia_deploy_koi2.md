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
Junio 2025
