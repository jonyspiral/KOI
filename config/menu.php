<?php

return [

    'Producción' => [
        [
            'nombre' => 'Análisis de Stock',
            'ruta' => 'produccion.analisis-stock.index',
            'icono' => 'fas fa-boxes',
        ],
        [
            'nombre' => 'Lanzamiento',
            'ruta' => 'produccion.lanzamiento.index',
            'icono' => 'fas fa-rocket',
        ],
        [
            'nombre' => 'Cumplido',
            'ruta' => 'produccion.cumplido.index',
            'icono' => 'fas fa-check-double',
        ],
    ],

    'ABMs' => [
        [
        'nombre' => 'Artículo + Color (Custom)',
        'ruta' => 'articulocolor.index',
        'icono' => 'fas fa-layer-group',
    ],
    [
        'nombre' => 'Artículos',
        'ruta' => 'produccion.abms.articulos.index',
        'icono' => 'fas fa-tags',
    ],
    [
        'nombre' => 'Colores por Artículo',
        'ruta' => 'produccion.abms.colores_por_articulo.index',
        'icono' => 'fas fa-palette',
    ],
    [
        'nombre' => 'Rutas de Producción',
        'ruta' => 'produccion.abms.rutas_produccion.index',
        'icono' => 'fas fa-route',
    ],
    [
        'nombre' => 'Familias de Producto',
        'ruta' => 'produccion.abms.familias_producto.index',
        'icono' => 'fas fa-cubes',
    ],
    [
        'nombre' => 'Líneas de Producto',
        'ruta' => 'produccion.abms.lineas_producto.index',
        'icono' => 'fas fa-stream',
    ],
    
],

    'Mercado Libre' => [
        [
            'nombre' => 'SKU Variantes',
            'ruta' => 'mlibre.variantes.index',
            'icono' => 'fab fa-whatsapp',
        ],
    ],

    'Sistemas' => [
        [
            'nombre' => 'Importar Tablas',
            'ruta' => 'sistemas.importar.form',
            'icono' => 'fas fa-file-import',
        ],
        [
            'nombre' => 'ABM Creator',
            'ruta' => 'sistemas.abms.crear',
            'icono' => 'fas fa-tools',
        ],
    ],
];
