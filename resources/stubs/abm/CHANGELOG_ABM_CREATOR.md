# 📦 ABM Creator - Registro de Cambios

---

## 🧩 Versión 1.1 - 2025-04-06

🔧 **Soporte de Subformularios Inline en Index**

- Se actualizó el `controller.stub.php` para cargar automáticamente los `subformularios` desde el archivo `config_form_{Modelo}.json`.
- Se pasan a la vista como variable `$subformularios`.
- Esto permite que `index.stub.blade.php` los renderice automáticamente con el componente `<x-koi-subformulario>` si existen.

🗂 Archivos modificados:
- `controller.stub.php`
- `index.stub.blade.php` (requiere bloque de subformularios dentro del loop)

✅ Validado en:
- `RutasProduccionController` con `PasosRutasProduccion` como subformulario.
