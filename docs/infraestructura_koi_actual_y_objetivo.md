# 🧱 Infraestructura KOI – Estado Actual y Objetivo (Julio 2025)

## ✅ Estado Actual de Servidores y VMs

| VM / Hostname     | Tipo de sistema         | Función principal                      | Detalles clave                                                                 |
|-------------------|--------------------------|----------------------------------------|---------------------------------------------------------------------------------|
| UBUNTUSERVER       | VM sobre DL380           | KOI2 (Laravel + MySQL + API)           | Ubuntu Server 20.04, IP 192.168.2.210, 3GB RAM actuales, crecer a 6–8 GB       |
| SRV-NEW            | VM sobre DL380           | KOI1 + SQL Server 2019                 | Windows Server 2019, IP 192.168.2.227, Apache + PHP 5.2.9 + PHP 8 + SQL2019     |
| SERVER             | VM local (migrable)      | Hexágono + SQL Server 2000             | Windows Server 2003, IP 192.168.2.100, backup semanal programado               |
| JONYOFI            | PC local física          | Testing, staging, pruebas de conexión  | Windows 10, IP 192.168.2.44, servidor de pruebas y transición                  |

## 🔮 Infraestructura Objetivo y Pendientes

- Consolidar servidor definitivo para alojar la VM `SERVER` (Hexágono).
- Confirmar si UBUNTUSERVER mantiene VM o migra a bare metal.
- Asignar IPs estáticas definitivas para cada entorno.
- Implementar estrategia de backups para todas las VMs críticas.

## 💾 Requisitos Técnicos para VM `SERVER`

- SO host: Windows 10 o Windows Server
- Hypervisor: Hyper-V (preferido) o VirtualBox
- Recursos mínimos: 1 vCPU, 4GB RAM, 60GB disco
- Backup: local o en red, al menos semanal

## 🧩 Integraciones y Conectividad

- KOI2 (Laravel en Ubuntu) se conectará vía `sqlsrv` a SQL Server 2019 en SRV-NEW
- KOI1 mantendrá acceso desde Access/Legacy a SQL Server 2019
- Hexágono se mantendrá aislado sin conexión crítica, solo consulta

## 🛠️ Tareas técnicas próximas

1. Instalar SQL Server 2008 R2 en JONYOFI
2. Restaurar `.bak` de KOI/ENCINITAS en 2008 R2
3. Validar funcionamiento de estructuras en 2008
4. Generar backup y migrar a SQL Server 2019
5. Configurar Laravel para usar SQL Server 2019
6. Apagar SQL Server 2000 solo cuando esté todo validado