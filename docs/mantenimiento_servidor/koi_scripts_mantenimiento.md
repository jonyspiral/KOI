
# 🧾 KOI - Scripts de Diagnóstico y Mantenimiento del Servidor

**Autor:** Vicente  
**Fecha:** 2025-07-12  
**Servidor:** Ubuntu Server 20.04 (192.168.2.210)  
**Contexto:** Sistema KOI2 (Laravel 12, PHP 8.2, Apache, MySQL)

---

## 🧠 Script: `koidiag`

### 📌 Ubicación
`/usr/local/bin/koidiag`

### ✅ Funcionalidad
Diagnóstico completo del estado del servidor:

- RAM, Swap y uso de caché
- Procesos con mayor uso de memoria
- Estado de MySQL (`active`, `max_connections`)
- Estado de Apache o Nginx
- Versión de PHP y Laravel (`koi2_v1`)
- Procesos zombie
- Espacio en disco
- Hora del servidor

### ▶️ Ejecución

```bash
koidiag
```

---

## 🧹 Script: `liberar_memoria`

### 📌 Ubicación
`/usr/local/bin/liberar_memoria`

### ✅ Funcionalidad
Limpieza manual de memoria y reinicio de servicios:

1. Mata procesos de **VSCode Server** (`node`) que consumen RAM excesiva.
2. Ejecuta `sync` + `drop_caches` para liberar caché de sistema.
3. Reinicia **MySQL** si está activo.
4. Muestra el estado final de RAM y Swap.

### ▶️ Ejecución

```bash
liberar_memoria
```

---

## 📦 Instalación manual

```bash
sudo nano /usr/local/bin/koidiag
# (pegar script de diagnóstico)

sudo nano /usr/local/bin/liberar_memoria
# (pegar script de limpieza)

sudo chmod +x /usr/local/bin/koidiag
sudo chmod +x /usr/local/bin/liberar_memoria
```

---

## 🧯 Resultado obtenido

- Reducción de uso de RAM al eliminar procesos innecesarios.
- Confirmación de estado óptimo de Apache, MySQL y Laravel.
- Cero procesos zombie.
- Scripts listos para automatización futura con `cron`.

---

**Siguiente sugerencia:** integrar ambos scripts en un módulo `modo_mantenimiento` y opcionalmente programarlos en `cron` o vía interfaz KOI.
