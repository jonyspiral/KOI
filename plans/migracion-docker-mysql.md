---
title: Migración KOI1 — Eliminar SQL Server 2000, full Docker Ubuntu
status: active
created: 2026-04-20
---

## Objetivo
Validar y arreglar todos los módulos de KOI1 para que funcionen 100% en el
container Docker (PHP 5.6 + MySQL 8), eliminando la dependencia al servidor
Windows Server 2003 + SQL Server 2000 (`192.168.2.100`).

## Infraestructura — COMPLETADO ✅
- [x] BD migrada: todas las tablas y vistas de SQL Server → MySQL `koi1_stage`
- [x] Código: migración masiva T-SQL→MySQL en `clases/` (ISNULL→IFNULL, etc.)
- [x] Git: repo `github.com/jonyspiral/KOI`, rama `main` como única rama activa
- [x] CLAUDE.md y .antigravity-context.md creados

## Módulos — EN CURSO ⚠️

| Módulo | Estado | Notas |
|--------|--------|-------|
| Login | ✅ OK | |
| Catálogo / main | ✅ OK | |
| Favoritos (cliente) | ✅ OK | agregar/borrar/editarCurva/confirmarPedido |
| Clientes (listado) | ❓ Pendiente | |
| Clientes (mayoristas) | ❓ Pendiente | |
| Stock producción | ❓ Pendiente | vistas MySQL creadas |
| Pedidos | ❓ Pendiente | |
| Facturación | ❓ Pendiente | |
| Cuenta corriente | ❓ Pendiente | |
| Documentos | ❓ Pendiente | |
| Cheques / Caja | ❓ Pendiente | |

## Bug pendiente
- [ ] `factory/Transaction.php` → `driverName()` fallback a `'sqlsrv'`
  ```php
  // Fix: hardcodear mysql
  private static function driverName() {
      return 'mysql';
  }
  ```

## Cutover final
- [ ] Todos los módulos en ✅ OK
- [ ] Smoke test completo en Docker
- [ ] Apagar servicios en `192.168.2.100`
- [ ] Actualizar DNS/nginx para `koi.spiralshoes.com` → port 8195
