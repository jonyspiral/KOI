# Open Issues - KOI Harness

## Riesgos obligatorios vigentes
1. MySQL escucha en `0.0.0.0:3306`.
2. UFW esta inactivo.
3. Actualmente solo esta confirmado `koi_odbc@192.168.2.44`.
4. No asumir permisos para otras PCs hasta validar o crear grants especificos.
5. `SSL Mode=Preferred` no confirma negociacion TLS efectiva.
6. Los DSN no deben contener ni versionar passwords.
7. Stage (`koi1_stage`) es rollback/baseline y no entorno de trabajo normal.
8. No realizar cambios de grants, firewall, MySQL o DSN durante este bootstrap.

## Documentacion faltante
- Procedimiento formal de validacion por PC para los DSN ODBC.
- Evidencia tecnica de TLS negociado para conexiones cliente.

## Dudas tecnicas pendientes
- Arquitectura exacta de Microsoft Access en cada PC cliente (x86 o x64).
- Necesidad de perfiles adicionales read-only diferenciados por red/usuario.

## Requiere validacion humana
- Confirmar matriz de hosts autorizados para `koi_odbc`.
- Confirmar politica final de SSL/TLS para conexiones ODBC de usuarios.
