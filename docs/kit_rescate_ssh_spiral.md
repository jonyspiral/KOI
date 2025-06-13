# 🛠️ KIT DE COMANDOS DE RESCATE POR SSH – SPIRAL SERVER UBUNTU

## 🔗 Conexión desde Windows

Desde PowerShell o CMD:

```bash
ssh root@192.168.2.210
```

---

## ✅ 1. Reiniciar el entorno gráfico (LightDM)

```bash
systemctl restart lightdm
```

---

## ✅ 2. Liberar memoria RAM (caché de disco)

```bash
sync; echo 3 > /proc/sys/vm/drop_caches
```

---

## ✅ 3. Reiniciar la SWAP (si está llena)

```bash
swapoff -a && swapon -a
```

---

## ✅ 4. Ver uso actual de memoria

```bash
free -h
```

O más detallado:

```bash
top
```

Salir con `q`.

---

## ✅ 5. Ver los procesos que más RAM consumen

```bash
ps aux --sort=-%mem | head -n 20
```

---

## ✅ 6. Matar proceso específico (reemplazar `PID`)

```bash
kill -9 PID
```

---

## ✅ 7. Buscar procesos zombies

```bash
ps aux | awk '{ if ($8 == "Z") print $0; }'
```

---

## ✅ 8. Buscar procesos gráficos activos

```bash
ps aux | grep -E 'Xorg|lightdm|xfce|gnome'
```

---

## ✅ 9. Consultar el log del monitor de RAM

```bash
tail -n 50 /var/log/ram_monitor.log
```

---

## ✅ 10. Reiniciar el servidor completo (último recurso)

```bash
reboot
```

---

## ✅ 11. Alias sugerido: `modo_rescate`

Para tener un comando rápido con todas las acciones:

1. Editar bashrc:
   ```bash
   nano ~/.bashrc
   ```

2. Agregar:
   ```bash
   alias modo_rescate='sync; echo 3 > /proc/sys/vm/drop_caches && swapoff -a && swapon -a && systemctl restart lightdm'
   ```

3. Cargar los cambios:
   ```bash
   source ~/.bashrc
   ```

4. Usar:
   ```bash
   modo_rescate
   ```

---

📁 **Archivo generado por Sofía para Vicente (Spiral Shoes). Última actualización: 2025-06-11**
