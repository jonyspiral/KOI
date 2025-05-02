## Estrategia de Formularios Dinámicos para ABM Creator (Form Builder)

### ✨ Objetivo
Permitir que los formularios de subformularios en ABMs generados dinámicamente respeten la configuración definida en los archivos `config_form_*.json`.

---

### 🔧 Componentes Principales

#### 1. **Archivo JSON de Configuración**
Ejemplo: `resources/meta_abms/config_form_PasosRutasProduccion.json`

Contiene información clave como:
- `modelo`, `namespace`, `carpeta_vistas`
- `campos`: cada campo con metadatos (`input_type`, `nullable`, `default`, `select_list_data`, etc.)
- `subformularios`: lista de subformularios dependientes con `tabla`, `modelo`, `foreign_key`, etc.

#### 2. **Componente Blade: `koi-subformulario.blade.php`**

Controla toda la lógica de renderización del subformulario inline:
- Renderiza `form create` inline, con inputs según `config_form_*.json`
- Muestra lista (`index`) de registros hijos
- Soporta edición inline (`PUT`) de cada fila
- Soporta eliminación (`DELETE`)
- Toggle de visualización: mostrar/ocultar subformularios

#### 3. **Comportamientos Dinámicos**
- **Inputs**: se renderizan dinámicamente según el `input_type` del JSON.
  - `select`: consulta DB según `referenced_table`
  - `select_list`: usa `select_list_data` del JSON
  - `checkbox`: valor "S"/"N"
  - `text`, `number`, `textarea`, etc.: según configuración
- **Defaults**: respeta el valor por defecto
- **Validación**: si `nullable = false`, agrega `required`
- **Key foránea**: el `foreign_key` se incluye como `hidden`, sin label

#### 4. **Controlador del Padre**
En el `index()` del modelo padre:
- Se cargan los subformularios desde el archivo JSON
- Se pasa la variable `subformularios` a la vista index
- Dentro del `@foreach`, se renderiza:
  ```blade
  <x-koi-subformulario
      :registro="$registro"
      :subform="$sub"
      :rutaBase="basename($sub['carpeta_vistas'])"
  />
  ```

---

### 🚀 Resultado Funcional

- Un ABM generado por ABM Creator puede tener **subformularios inline** totalmente funcionales.
- Los formularios y listados se adaptan a la configuración sin escribir código manual.
- Permite mantener la lógica de negocio centralizada en archivos JSON configurables.

---

### ⚖️ Beneficios
- Separación entre lógica de visualización y configuración
- Evita duplicar vistas para cada subformulario
- Permite trabajar con relaciones de 1:N sin complejidad adicional
- Acelera la creación de nuevos ABMs con relaciones embebidas

---

### 🌟 Próximos pasos sugeridos
- Agregar validaciones automáticas en el backend desde el JSON
- Exportación del esquema a doc automática (por ejemplo: tabla resumen por ABM)
- Agregar soporte para `textarea`, `date`, `color`, `file`, etc.
- Extensión para tabs o modals como modo de subformulario (`modo = tab | modal | inline`)


