# KOI2 – ABM Consolidado (Creator + Artículos con Colores + Subform Inline)

**Versión:** 2025-09-09  
**Responsable:** Sistemas KOI (Infra KOI)  
**Ámbito:** KOI2 (Laravel)  

---

## 1) Objetivo
Consolidar en un único documento los lineamientos y snippets para:
- **ABM Creator** (modal / inline / default) y sus *stubs*.
- **ABM Artículos** con **subformulario inline** de `ColoresPorArticulo`.
- **Buenas prácticas** de filtros/sort (directivas Blade), controladores y vistas.
- **Plantilla de documentación por ABM** para registrar en la base.

> Este documento sirve como **fuente de implementación** y **guía de documentación** por ABM.

---

## 2) Checklist Seguro (previo a cambios)
- [ ] Backup de DB producción (dump completo + tablas involucradas).
- [ ] Backup de `resources/views` y `resources/meta_abms`.
- [ ] Rama de git dedicada (`feature/abm-<nombre>`).
- [ ] Variables de entorno revisadas (sin exponer credenciales en commits).
- [ ] Deploy con *downtime* controlado si hay migraciones.

---

## 3) ABM Creator – Conceptos y Configuración
...
(Contenido completo del consolidado aquí)
...
## 12) Control de Cambios
- 2025-09-09: Consolidación inicial (Creator + Artículos + Subform Inline).
