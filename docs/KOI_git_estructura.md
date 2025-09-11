# KOI — Estructura Git y Deploy

> **Decisión clave:**  
> - **KOI1 (legacy)** vive en el repo **`jonyspiral/KOI`**, rama **`koi1_legacy`**.  
> - **KOI2 (Laravel)** vive en el repo **`jonyspiral/koi2`**, rama **`master`** (directorio de trabajo: `koi2_v1`).  
> - **Producción KOI2** se publica en `/var/www/koi2` (**sin Git**).

---

## 1) Mapa de carpetas en el servidor

```
/var/www/
├─ encinitas/      → KOI1 (legacy)    [repo: jonyspiral/KOI, branch: koi1_legacy]
├─ koi2_v1/        → KOI2 (dev)       [repo: jonyspiral/koi2, branch: master]
└─ koi2/           → KOI2 (prod)      [carpeta de deploy, sin .git]
```

**Notas**  
- `encinitas/` es el código legacy (PHP 5.x / contenedores).  
- `koi2_v1/` es el código de Laravel 12 (desarrollo).  
- `koi2/` contiene solo el _resultado_ del deploy; no se versiona.

---

## 2) Repositorios y ramas

| Proyecto | Repositorio Git | Rama principal | Ruta local |
|---|---|---|---|
| KOI1 (legacy) | `git@github.com:jonyspiral/KOI.git` | `koi1_legacy` | `/var/www/encinitas` |
| KOI2 (Laravel) | `git@github.com:jonyspiral/koi2.git` | `master` | `/var/www/koi2_v1` |
| KOI2 (producción) | _sin repo_ | _n/a_ | `/var/www/koi2` |

> Sugerencia (opcional): en el repo **KOI**, fijar `koi1_legacy` como **Default branch** para evitar confusiones con `main/master`.

---

## 3) Remotos configurados (referencia rápida)

### KOI1 (legacy) — `/var/www/encinitas`
```bash
git remote -v
# origin  git@github.com:jonyspiral/KOI.git (fetch)
# origin  git@github.com:jonyspiral/KOI.git (push)
```

### KOI2 (dev) — `/var/www/koi2_v1`
```bash
git remote -v
# origin  git@github.com:jonyspiral/koi2.git (fetch)
# origin  git@github.com:jonyspiral/koi2.git (push)
```

---

## 4) Flujo de trabajo

### KOI1 (legacy)
```bash
cd /var/www/encinitas
git checkout koi1_legacy
git pull --rebase
# ... cambios ...
git add .
git commit -m "fix(legacy): ..."
git push
```

### KOI2 (desarrollo)
```bash
cd /var/www/koi2_v1
git checkout master
git pull --rebase
# ... cambios ...
git add .
git commit -m "feat(koi2): ..."
git push
```

---

## 5) Deploy KOI2 (resumen)
> Resultado: sincroniza `koi2_v1` → `koi2` (prod) **sin** incluir `.git`, instala dependencias y cachea config.

1. **Sincronizar archivos** (por ej. con `rsync`):
   ```bash
   rsync -av --delete \
     --exclude ".git" --exclude "node_modules" --exclude "vendor" \
     /var/www/koi2_v1/  /var/www/koi2/
   ```
2. **`.env` de producción**: mantenerlo fuera del repo (por ej. `/var/www/koi2/.env`).  
3. **Dependencias & optimizaciones**:
   ```bash
   cd /var/www/koi2
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force   # si aplica
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```

---

## 6) `.gitignore` estándar

Usar en **encinitas** y **koi2_v1**:

```
/.env
/.env.*
/vendor/
/node_modules/
/storage/
/cache/
/tmp/
/logs/
/*.log
.DS_Store
Thumbs.db
```

---

## 7) Comandos útiles

- Mostrar ramas locales y su upstream:
  ```bash
  git branch -vv
  ```
- Ver ramas remotas (y default):
  ```bash
  git remote show origin
  ```
- Probar acceso SSH a GitHub (usuario esperado: `jonyspiral`):
  ```bash
  ssh -T git@github.com
  ```

---

## 8) Política de uso

- **KOI1 (legacy):** mantenimiento en `koi1_legacy` (hotfixes puntuales).  
- **KOI2:** desarrollo activo en `master` (`koi2_v1`), deploy a `/var/www/koi2`.  
- **Producción KOI2:** nunca versionar; solo artefactos listos para correr.

---

_Responsable:_ Vicente (Johnny) — SPIRAL SHOES  
_Última actualización:_ (completar cuando se modifique)
