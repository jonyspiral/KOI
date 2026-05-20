# Smoke Test Lote D - BO interactivo - 2026-05-19

## Objetivo

Validar un primer ABM simple y una primera pantalla BO sensible a datos despues del saneamiento tecnico de `Lote C`.

## Precondicion

Iniciar sesion contra `/` y no contra `/login.php`:

```bash
rm -f /tmp/koi.cookie /tmp/post_login.html

curl -sS -D /tmp/login.headers -o /tmp/post_login.html \
  -c /tmp/koi.cookie -b /tmp/koi.cookie \
  -d 'user=jony&pass=Route667&empresa=1' \
  http://127.0.0.1:8195/
```

## Pantalla 1: ABM simple

### Modulo

- `content/abm/bancos`

### Estado actual

- `index`: validado
- `buscar.php`: validado por `GET`
- `agregar.php`: validado
- `editar.php`: validado
- `borrar.php`: validado

### Evidencia minima

```bash
curl -s -b /tmp/koi.cookie -c /tmp/koi.cookie \
  "http://127.0.0.1:8195/content/abm/bancos/buscar.php?id=1"

curl -s -b /tmp/koi.cookie -c /tmp/koi.cookie \
  -d 'id=1&nombre=ABN AMRO Editado&codigoBanco=005' \
  http://127.0.0.1:8195/content/abm/bancos/editar.php

curl -s -b /tmp/koi.cookie -c /tmp/koi.cookie \
  -d 'id=1' \
  http://127.0.0.1:8195/content/abm/bancos/borrar.php
```

Nota:

- `buscar.php` usa `GET`; si se envia `id` por `POST`, devuelve objeto vacio y no es un bug del modulo
- el borrado es logico (`fechaBaja`), no fisico

## Pantalla 2: BO sensible a datos

### Modulo

- `content/administracion/proveedores/gestion_proveedores`

### Estado actual

- `index`: validado
- `buscar.php`: validado
- `editar.php`: validado

### Hallazgo y fix aplicado

- la grilla trabaja con `cod_prov`, pero `editar.php` estaba cargando proveedor con `getProveedor()`
- esto dejaba casos donde el proveedor aparecia en la tabla y fallaba la edicion
- se ajusto `editar.php` para usar `getProveedorTodos($idProveedor)`

### Evidencia minima

```bash
curl -s -b /tmp/koi.cookie -c /tmp/koi.cookie \
  "http://127.0.0.1:8195/content/administracion/proveedores/gestion_proveedores/buscar.php?empresa=0&mostrarSaldoCero=S&orden=0" \
  | head -c 800

curl -s -b /tmp/koi.cookie -c /tmp/koi.cookie \
  -d 'idProveedor=810&observaciones=Prueba BO Codex 2026-05-20' \
  http://127.0.0.1:8195/content/administracion/proveedores/gestion_proveedores/editar.php

curl -s -b /tmp/koi.cookie -c /tmp/koi.cookie \
  "http://127.0.0.1:8195/content/administracion/proveedores/gestion_proveedores/buscar.php?idProveedor=810&empresa=0&mostrarSaldoCero=S&orden=0" \
  | head -c 1200
```

## Criterio de aprobacion

- la pantalla renderiza sin fatal PHP
- la busqueda carga datos o devuelve error de negocio controlado
- las acciones AJAX responden JSON o HTML limpio, sin salida basura previa
- no aparecen regresiones de permisos o de sesion respecto del flujo navegador
- en `gestion_proveedores`, la edicion de observaciones persiste y se refleja en una consulta posterior