# KOI1 a MySQL: compatibilidad case-sensitive

## Decision

KOI1 viene de SQL Server, donde las diferencias de mayusculas y minusculas en nombres de tablas no generaban conflicto practico.

En Ubuntu/MySQL la base koi1_stage corre con lower_case_table_names = 0.

Esto hace que los nombres de tablas sean case-sensitive.

Ejemplo real detectado:

- Existe: Almacenes
- No existe: almacenes
- Error: Table 'koi1_stage.almacenes' doesn't exist

## Criterio adoptado

No se modifica lower_case_table_names.

Se mantiene MySQL estricto y se crea una capa temporal de compatibilidad mediante vistas alias en minuscula.

Ejemplo:

CREATE OR REPLACE VIEW almacenes AS SELECT * FROM Almacenes;

## Motivo

Cambiar lower_case_table_names es una decision global y riesgosa. Puede requerir recrear el datadir de MySQL.

Las vistas alias permiten desbloquear KOI1 sin modificar logica de negocio.

## Objetos detectados

El script genero vistas alias para tablas base migradas con nombres mixtos o mayusculas, por ejemplo:

- Almacenes -> almacenes
- Clientes -> clientes
- Contactos -> contactos
- Marcas -> marcas
- Materias_primas -> materias_primas
- Orden_fabricacion -> orden_fabricacion
- Tareas_cabecera -> tareas_cabecera
- Tareas_detalle -> tareas_detalle
- Tipo_producto_Stock -> tipo_producto_stock

## Politica

Esta capa es temporal.

Fase 1: crear aliases para desbloquear funcionamiento.
Fase 2: auditar objetos realmente usados por KOI1.
Fase 3: normalizar nombres definitivamente con backup previo.
Fase 4: eliminar aliases temporales.

## Relacion con Memcache

Durante la misma depuracion se detecto que PHP 5.6 Docker no tenia disponible la clase Memcache.

Error:

Class 'Memcache' not found en factory/Cache.php

Decision aplicada:

- no instalar Memcache como dependencia obligatoria en esta etapa;
- agregar fallback no-cache en factory/Cache.php;
- permitir que la ausencia de cache no bloquee persistencias.
