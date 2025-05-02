{{-- resources/views/sistemas/abms/partials/menu_config.blade.php --}}
@php
    // ⚡ Agregá este bloque justo antes del include del menú
    $menuDefault = [
        [
            'mostrar' => true,
            'label' => $modelo ?? 'SinNombre',
            'icon' => '📄',
            'modulo' => strtolower($namespace ?? 'general'),
            'grupo' => ucfirst($namespace ?? 'General'),
            'posicion' => 99,
        ]
    ];
    $menuData = $config['menu'] ?? $menuDefault;
@endphp

<h4 class="mt-4">🧭 Configuración de Menú</h4>

<div id="menu-configuracion">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mostrar</th>
                <th>Label</th>
                <th>Grupo</th>
                <th>Módulo</th>
                <th>Ícono</th>
                <th>Posición</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="menu-entries">
            {{-- Esto se completa dinámicamente con JavaScript --}}
            <input type="hidden" name="menu_json" id="menu_json" value="{{ old('menu_json', json_encode($menuData)) }}">

        </tbody>
    </table>

    <button class="btn btn-sm btn-primary" onclick="agregarEntradaMenu()">➕ Agregar entrada</button>
</div>

@php
    $menuDefault = [
        [
            'mostrar' => true,
            'label' => $config['modelo'] ?? 'SinNombre',
            'icon' => '📄',
            'modulo' => strtolower($config['namespace'] ?? 'general'),
            'grupo' => ucfirst($config['namespace'] ?? 'General'),
            'posicion' => 99,
        ]
    ];
    $menuData = $config['menu'] ?? $menuDefault;
@endphp

<script>
let entradasMenu = @json($menuData);

function renderMenuEntries() {
    const tbody = document.getElementById('menu-entries');
    tbody.innerHTML = '';
    entradasMenu.forEach((entrada, index) => {
        tbody.innerHTML += `
            <tr>
                <td><input type="checkbox" ${entrada.mostrar ? 'checked' : ''} onchange="actualizarCampo(${index}, 'mostrar', this.checked)"></td>
                <td><input class="form-control form-control-sm" value="${entrada.label}" onchange="actualizarCampo(${index}, 'label', this.value)"></td>
                <td><input class="form-control form-control-sm" value="${entrada.grupo}" onchange="actualizarCampo(${index}, 'grupo', this.value)"></td>
                <td><input class="form-control form-control-sm" value="${entrada.modulo}" onchange="actualizarCampo(${index}, 'modulo', this.value)"></td>
                <td><input class="form-control form-control-sm" value="${entrada.icon}" onchange="actualizarCampo(${index}, 'icon', this.value)"></td>
                <td><input type="number" class="form-control form-control-sm" value="${entrada.posicion}" onchange="actualizarCampo(${index}, 'posicion', this.value)"></td>
                <td><button class="btn btn-sm btn-danger" onclick="eliminarEntradaMenu(${index})">🗑️</button></td>
            </tr>
        `;
    });
    guardarJSONMenu();
}

function actualizarCampo(index, campo, valor) {
    entradasMenu[index][campo] = valor;
    guardarJSONMenu();
}

function agregarEntradaMenu() {
    entradasMenu.push({
        mostrar: true,
        label: 'Nuevo',
        icon: '📄',
        modulo: '{{ strtolower($config["namespace"] ?? "general") }}',
        grupo: '{{ ucfirst($config["namespace"] ?? "General") }}',
        posicion: 99
    });
    renderMenuEntries();
}

function eliminarEntradaMenu(index) {
    if (confirm('¿Eliminar esta entrada de menú?')) {
        entradasMenu.splice(index, 1);
        renderMenuEntries();
    }
}

function guardarJSONMenu() {
    const input = document.querySelector('input[name="menu_json"]');
    if (input) {
        input.value = JSON.stringify(entradasMenu);
    } else {
        const nuevoInput = document.createElement('input');
        nuevoInput.type = 'hidden';
        nuevoInput.name = 'menu_json';
        nuevoInput.value = JSON.stringify(entradasMenu);
        document.forms[0].appendChild(nuevoInput);
    }

}

renderMenuEntries();
</script>
