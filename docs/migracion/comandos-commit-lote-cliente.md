# Comandos de commit (lote cliente + motor)

Objetivo: separar en 2 commits limpios dentro del entorno dev `encinitas5.6`.

## 1) Commit motor

```powershell
git add `
  factory/Config.php `
  factory/Datos.php `
  factory/Factory.php `
  factory/Transaction.php

git commit -m "koi1: alinea motor mysql (config, datos y transacciones)"
```

## 2) Commit cliente + docs

```powershell
git add `
  content/cliente/favoritos/agregarVarios.php `
  content/cliente/favoritos/borrarTodos.php `
  content/cliente/favoritos/borrarVarios.php `
  content/cliente/favoritos/index.php `
  content/cliente/favoritos/reporte/index.php `
  content/cliente/favoritos/reporte/reporte.php `
  content/cliente/pedidos/index.php `
  content/cliente/index.php `
  content/cliente/menu.php `
  content/cliente/mobilemenu.php `
  content/cliente/usermenu.php `
  docs/migracion/smoke-test-cliente-php56.md `
  docs/migracion/estado-lote-cliente-dev.md `
  docs/migracion/comandos-commit-lote-cliente.md

git commit -m "koi1 cliente: hardening favoritos/pedidos y compat php tags"
```

## 3) Push de rama dev

```powershell
git push origin <tu-rama-dev>
```

## 4) Merge a main

Solo despues de ejecutar `docs/migracion/smoke-test-cliente-php56.md` en runtime PHP 5.6 real.
