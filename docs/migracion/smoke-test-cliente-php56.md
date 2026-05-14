# Smoke Test Cliente (PHP 5.6 + MySQL)

Este checklist valida el lote actual de migracion en entorno dev (`encinitas5.6`).

## Precondiciones

- Aplicacion levantada en runtime PHP 5.6.
- Base MySQL accesible con datos de prueba.
- Usuario cliente con permisos para `catalogo`, `favoritos`, `pedidos`.

## 1) Catalogo

- Abrir `/catalogo/?c=<linea>&f=<familia>`.
- Verificar que lista articulos sin error PHP/JS.
- Marcar y desmarcar un favorito desde la estrella.
- Verificar que el icono de favorito cambia y persiste al recargar.

## 2) Favoritos (listado y edicion)

- Abrir `/favoritos/`.
- Validar carga del listado y totales.
- Editar cantidades libres y curvas.
- Probar "Sacar Todos los Favoritos".
- Verificar que no haya error con `Content-Type: application/json; charset=utf-8`.

## 3) Favoritos (reporte)

- Abrir `/favoritos/reporte/`.
- Validar render de subarticulos.
- Validar casos con talles vacios (sin warnings/notice).
- Validar total por curva en articulos forma `M`.

## 4) Pedidos

- Abrir `/pedidos/`.
- Confirmar que lista pedidos existentes.
- Generar un pedido desde favoritos y revisar que aparece.
- Cancelar pedido en estado permitido.
- Probar PDF de pedido (`/cliente/pedidos/getPdf/?id=<id>`).

## 5) Transacciones motor

- Crear pedido y confirmar que persiste cabecera + detalle.
- Cancelar pedido y confirmar rollback limpio ante error simulado.
- Validar que no aparezcan errores por sintaxis SQL Server de transaccion.

## Criterio de merge

Se habilita merge a `main` cuando:

1. Todos los pasos anteriores pasan en PHP 5.6 real.
2. No hay error 500 ni warning bloqueante en flujo cliente.
3. Favoritos y pedidos mantienen paridad funcional base.
4. Se registra evidencia minima (capturas o notas breves por paso).

Si algun punto falla, mergear solo a rama dev/integracion y abrir fix puntual.
