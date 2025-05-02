{{-- resources/views/sistemas/abms/partials/instrucciones.blade.php --}}

<div class="mt-4">
    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#infoPreview" aria-expanded="false">
        📖 Ver instrucciones detalladas
    </button>
    <div class="collapse" id="infoPreview">
        <div class="card card-body mt-3">
            <h4 class="mb-3">🛠 Guía de Uso - Configuración de ABMs en KOI</h4>

            <p>Esta herramienta permite definir de manera modular y asistida la estructura de un ABM (Alta, Baja, Modificación) en KOI.</p>

            <h5 class="mt-3">🔹 1. Configuración de Campos del Modelo</h5>
            <ul>
                <li>Se muestran todos los campos detectados automáticamente desde el modelo correspondiente.</li>
                <li>Por cada campo se puede definir:
                    <ul>
                        <li><strong>Label</strong>: Nombre que se mostrará en el formulario.</li>
                        <li><strong>Tipo de Input</strong>: Text, Number, Select, Checkbox, etc.</li>
                        <li><strong>Valor por defecto</strong>: Default inicial para el formulario.</li>
                        <li><strong>Incluir</strong>: Si debe formar parte del formulario o no.</li>
                        <li><strong>Sync</strong>: Si debe ser considerado para la sincronización de datos.</li>
                        <li><strong>Nullable</strong>: Si el campo puede ser nulo.</li>
                        <li><strong>Tabla FK / Columna FK / Label FK</strong>: Si es una relación a otra tabla (Foreign Key).</li>
                        <li><strong>Valores</strong>: Definición de listas para selects o checkboxes (por ejemplo "Sí=S,No=N").</li>
                        <li><strong>Tab</strong>: Categoría a la que pertenece (General, Técnicos, Producción, Ecommerce).</li>
                    </ul>
                </li>
            </ul>

            <h5 class="mt-3">🔹 2. Configuración del Formulario Principal</h5>
            <ul>
                <li>Define cómo se verá el ABM principal:
                    <ul>
                        <li><strong>Formato de Índice</strong> (Listado de registros): Clásico, Inline, Tab, Inline-Tab.</li>
                        <li><strong>Formato Create/Edit</strong> (Formulario de edición): Completo, Inline o Modal.</li>
                        <li><strong>Usar Paginador</strong>: Habilitar paginación automática en el listado.</li>
                        <li><strong>Registros por página</strong>: Número máximo de registros visibles por página.</li>
                        <li><strong>Nombre Interno del Formulario</strong>: Identificador técnico interno.</li>
                        <li><strong>Ruta Técnica del Formulario</strong>: URL que utilizará Laravel para registrar las rutas automáticamente.</li>
                    </ul>
                </li>
            </ul>

            <h5 class="mt-3">🔹 3. Configuración de Subformularios</h5>
            <ul>
                <li>Permite asociar modelos hijos a este ABM (relaciones 1 a N).</li>
                <li>Por cada subformulario se define:
                    <ul>
                        <li><strong>Modelo</strong>: Modelo hijo relacionado.</li>
                        <li><strong>Tabla</strong>: Nombre de la tabla asociada (opcional si sigue convenciones).</li>
                        <li><strong>Clave Foránea</strong> (FK): Campo de relación.</li>
                        <li><strong>Modo</strong>: Cómo se integrará (Inline, Modal, Tab).</li>
                        <li><strong>Título</strong>: Nombre descriptivo del subformulario.</li>
                        <li><strong>Carpeta Vistas</strong>: Ruta donde se almacenarán las vistas asociadas.</li>
                    </ul>
                </li>
            </ul>

            <h5 class="mt-3">🔹 4. Opciones de Sincronización</h5>
            <ul>
                <li><strong>Reemplazar Controlador</strong>: Si ya existe un controlador para el ABM, lo sobrescribe.</li>
                <li><strong>Usar timestamps</strong>: Agrega automáticamente `created_at` y `updated_at` si el modelo los maneja.</li>
                <li><strong>Sincronizable</strong>: Permite marcar si el ABM necesita sincronización con SQL Server (legado).</li>
            </ul>

            <h5 class="mt-3">🔹 5. Configuración del Menú</h5>
            <ul>
                <li>Define cómo aparecerá el ABM en el menú de KOI:
                    <ul>
                        <li><strong>Mostrar</strong>: Visible o no en el menú principal.</li>
                        <li><strong>Label</strong>: Nombre mostrado.</li>
                        <li><strong>Grupo</strong>: Sección donde se agrupará (por ejemplo, "Producción").</li>
                        <li><strong>Módulo</strong>: Namespace lógico en la organización del sistema.</li>
                        <li><strong>Ícono</strong>: Emoji o ícono representativo (puede ser reemplazado luego por íconos reales).</li>
                        <li><strong>Posición</strong>: Orden relativo en el menú.</li>
                    </ul>
                </li>
            </ul>

            <h5 class="mt-3">⚙️ Consideraciones Técnicas</h5>
            <ul>
                <li>La configuración se guarda en un archivo JSON dentro de <code>resources/meta_abms/config_form_{{ $modelo }}.json</code>.</li>
                <li>Se generan automáticamente:
                    <ul>
                        <li>Controlador Laravel.</li>
                        <li>Vistas Blade para index, create, edit, show.</li>
                        <li>Metadata para sincronización con sistemas externos si corresponde.</li>
                    </ul>
                </li>
                <li>Puede ser necesario regenerar el ABM si se agregan o eliminan campos estructurales.</li>
            </ul>

            <h5 class="mt-3">📋 Buenas prácticas</h5>
            <ul>
                <li>Antes de lanzar un ABM en producción, validar los nombres de los campos y los tabs.</li>
                <li>No dejar campos importantes sin marcar como "incluir".</li>
                <li>Siempre asignar correctamente los valores de listas y opciones de selects o checkboxes.</li>
                <li>En subformularios, definir bien la clave foránea y el modo de presentación.</li>
            </ul>

            <div class="alert alert-warning mt-4">
                ⚡ <strong>Recordá:</strong> Cada vez que configures un ABM, ¡podés regenerarlo desde esta misma pantalla de forma rápida sin perder el trabajo previo!
            </div>

        </div>
    </div>
</div>
