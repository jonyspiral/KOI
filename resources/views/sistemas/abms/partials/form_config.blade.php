{{-- sistemas/abms/partials/form_config.blade.php --}}

<fieldset class="border rounded p-3 mt-4">
    <legend class="w-auto px-2">🧾 Configuración del Formulario Principal</legend>

    {{-- Checkbox visible --}}
    <div class="form-check mt-3">
        <input type="hidden" name="force_controlador" value="0">
        <input class="form-check-input" type="checkbox" name="force_controlador" id="force_controlador" value="1" {{ old('force_controlador') ? 'checked' : '' }}>
        <label class="form-check-label" for="force_controlador">
            Reemplazar controlador existente
        </label>
    </div>

    <div class="mt-3">
        <label><input type="checkbox" name="timestamps" value="1" @checked(session('timestamps', true))> Usar timestamps</label><br>
        <label><input type="checkbox" name="sincronizable" value="1" @checked(session('sincronizable', false))> Es sincronizable</label>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary">📏 Guardar configuración</button>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">🏡 Formato del Índice</label>
            <select name="form_config[index_view_type]" class="form-select">
                <option value="default" {{ ($form_config['index_view_type'] ?? 'default') == 'default' ? 'selected' : '' }}>Clásico</option>
                <option value="inline" {{ ($form_config['index_view_type'] ?? 'default') == 'inline' ? 'selected' : '' }}>Inline</option>
                <option value="ficha" {{ ($form_config['index_view_type'] ?? 'default') == 'ficha' ? 'selected' : '' }}>Ficha (1 padre + N hijos)</option>
                <option value="tab" {{ ($form_config['index_view_type'] ?? 'default') == 'tab' ? 'selected' : '' }}>Pestañas</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="form_view_type" class="form-label">🔈 Formato del Create/Edit</label>
            <select name="form_config[form_view_type]" id="form_view_type" class="form-select">
                <option value="default" {{ ($form_config['form_view_type'] ?? 'default') == 'default' ? 'selected' : '' }}>Pantalla Completa</option>
                <option value="inline" {{ ($form_config['form_view_type'] ?? 'default') == 'inline' ? 'selected' : '' }}>Inline (en tabla)</option>
                <option value="modal" {{ ($form_config['form_view_type'] ?? 'default') == 'modal' ? 'selected' : '' }}>Modal (experimental)</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="usa_paginador" class="form-label">📚 Usar paginador</label>
            <select name="form_config[usa_paginador]" id="usa_paginador" class="form-select">
                <option value="1" {{ old('form_config.usa_paginador', $form_config['usa_paginador'] ?? '1') == '1' ? 'selected' : '' }}>✅ Sí</option>
                <option value="0" {{ old('form_config.usa_paginador', $form_config['usa_paginador'] ?? '1') == '0' ? 'selected' : '' }}>❌ No</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="per_page" class="form-label">📦 Registros por página</label>
            <input type="number" name="form_config[per_page]" id="per_page" class="form-control"
                   min="1" max="500" value="{{ old('form_config.per_page', $form_config['per_page'] ?? 100) }}">
        </div>

        <div class="col-md-6">
            <label for="form_name" class="form-label">🆔 Nombre del Formulario</label>
            <input type="text" name="form_config[form_name]" class="form-control"
                   value="{{ old('form_config.form_name', $form_config['form_name'] ?? $modelo) }}"
                   placeholder="Nombre técnico del formulario">
            <small class="text-muted">Nombre técnico o identificador del formulario para uso interno.</small>
        </div>

        <div class="col-md-6">
            <label for="form_route" class="form-label">📍 Ruta del Formulario <span class="text-danger">*</span></label>
            <input type="text" name="form_config[form_route]" id="form_route" class="form-control" required
                   value="{{ old('form_config.form_route', $form_config['form_route'] ?? '') }}"
                   placeholder="Ej: produccion/abms/marcas">
            <small class="text-muted">Usada para generar automáticamente las rutas web.</small>
        </div>
    </div>
</fieldset>
