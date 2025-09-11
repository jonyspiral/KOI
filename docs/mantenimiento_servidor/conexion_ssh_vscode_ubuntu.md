**Canvas Guía: Conexión SSH desde VSCode a Ubuntu Server (Vicente / UbuntuServer)**

---

### 🔍 Objetivo
Conectarse correctamente desde VSCode (Windows) por SSH a un servidor Ubuntu remoto (`192.168.2.210`) usando clave privada, con la extensión Remote - SSH, sin que VSCode intente usar PowerShell por error.

---

### 🔧 Pasos realizados y configuraciones clave

#### ✅ 1. Generación de claves SSH en Windows
```bash
ssh-keygen -t rsa -b 4096
```
- Guardado en: `C:\Users\jony\.ssh\id_rsa`

#### ✅ 2. Copiado de clave pública al servidor Ubuntu
Desde PowerShell o CMD:
```powershell
type $env:USERPROFILE\.ssh\id_rsa.pub | ssh root@192.168.2.210 "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 700 ~/.ssh && chmod 600 ~/.ssh/authorized_keys"
```

#### ✅ 3. Conexión manual probada correctamente:
```bash
ssh -i C:/Users/jony/.ssh/id_rsa root@192.168.2.210
```
Resultado: Autenticado con clave, sin password.

---

### 🔧 4. Configuración de archivo SSH config (`C:\Users\jony\.ssh\config`)
```ssh
Host UbuntuServer
    HostName 192.168.2.210
    User root
    IdentityFile C:/Users/jony/.ssh/id_rsa
```

---

### 🔧 5. VSCode - settings.json (`settings.json` global)
Ruta: `C:\Users\jony\AppData\Roaming\Code\User\settings.json`

```json
{
  "remote.SSH.configFile": "C:\\Users\\jony\\.ssh\\config",
  "remote.SSH.enableRemoteCommand": true,
  "remote.SSH.remotePlatform": {
    "UbuntuServer": "linux"
  }
}
```

Esto forzó a VSCode a dejar de intentar usar `powershell` como shell remoto (error: `bash: powershell: command not found`).

---

### 📁 6. Instalación de dependencias en Ubuntu
```bash
apt update && apt install -y nodejs npm bash curl tar gzip unzip git locales
```
- Conflicto detectado con `npm` y `nodejs` (NodeSource): solucionado con instalación limpia:

```bash
apt remove nodejs npm -y
curl -L https://raw.githubusercontent.com/tj/n/master/bin/n -o /usr/local/bin/n
chmod +x /usr/local/bin/n
n lts
```
- Resultado: Node.js actualizado y `npm` funcional

---

### 🚀 Resultado esperado final
- ✅ VSCode ya no ejecuta `powershell` en el host remoto
- ✅ Se detecta correctamente que el host es Linux
- ✅ Se instala el servidor VS Code correctamente
- ✅ La conexión se abre con terminal remota y acceso total

---

### 🎉 Listo para comenzar a trabajar
- Se puede abrir carpeta remota desde VSCode
- Terminal: `bash`
- Usuario: `root` (se puede cambiar a `vicente` si se desea un usuario dedicado)

---

### ✨ Notas adicionales
- VSCode puede quedarse colgado si no se indica que el servidor es Linux.
- La configuración de `remote.SSH.remotePlatform` es clave para que funcione correctamente.
- Se recomienda mantener las claves SSH con permisos restringidos:
```powershell
icacls "$env:USERPROFILE\.ssh\id_rsa" /inheritance:r /grant:r "$env:USERNAME:R"
```

---

📄 **Estado final: CONECTADO Y OPERATIVO** ✅

