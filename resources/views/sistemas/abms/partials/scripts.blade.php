<script>
document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.getElementById('formulario-configuracion');

    if (formulario) {
        formulario.addEventListener('submit', function (event) {
            console.log('🚀 Submit detectado');

            // 🚧 Evitamos enviar todavía
            event.preventDefault();

            // ✍️ Capturamos bien todo
            const formConfig = {
                form_view_type: document.getElementById('form_view_type')?.value || 'default',
                index_view_type: document.getElementById('index_view_type')?.value || 'default',
                usa_paginador: document.getElementById('usa_paginador')?.value === '1' ? 1 : 0,
                per_page: parseInt(document.getElementById('per_page')?.value || 100),
                form_name: document.getElementById('form_name')?.value || '',
                form_route: document.getElementById('form_route')?.value || '',
            };

            console.log('📦 Form Config preparado para enviar:', formConfig);

            // Guardamos el JSON en el hidden
            const inputFormConfig = document.getElementById('form_config_json');
            if (inputFormConfig) {
                inputFormConfig.value = JSON.stringify(formConfig);
            }

            // (opcional) Serializar menú y subformularios si quieres
            const inputMenu = document.getElementById('menu_json');
            if (inputMenu) {
                inputMenu.value = JSON.stringify(window.menuData || []);
            }

            const inputSubformularios = document.getElementById('subformularios_json');
            if (inputSubformularios) {
                inputSubformularios.value = JSON.stringify(window.subformulariosData || []);
            }

            // ✅ Ahora sí, enviar realmente
            formulario.submit();
        });
    }
});
</script>
