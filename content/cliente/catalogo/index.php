<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$menuactual    = array_key_exists('c', $_REQUEST) ? $_REQUEST['c'] : '';
$submenuactual = array_key_exists('f', $_REQUEST) ? $_REQUEST['f'] : '';

$catalogo = Catalogo::ultimo();

$tituloCatalogo     = '';
$familiaProducto    = '';
$familiaDescripcion = '';
$imagenLateral      = null;

// ---------- SELECCIÓN DE ARTÍCULOS (FILTRADO SEGURO) ----------
$articulosTodos = array();

if ($submenuactual != 'all') {
    // Familia específica dentro de la sección
    $familia = CatalogoSeccionFamilia::find($catalogo->id, $menuactual, $submenuactual);

    if ($familia) {
        foreach ($familia->articulos as $a) {
            /** @var CatalogoSeccionFamiliaArticulo $a */
            if ((int)$a->idCatalogo === (int)$catalogo->id
                && (int)$a->idLineaProducto === (int)$menuactual
                && (int)$a->idFamiliaProducto === (int)$submenuactual) {
                $articulosTodos[] = $a;
            }
        }

        // Metadatos de la familia (aunque no haya matches, para no romper)
        $tituloCatalogo     = $familia->lineaProducto->tituloCatalogo;
        $familiaProducto    = $familia->familiaProducto->nombre;
        $familiaDescripcion = $familia->descripcion;
        $imagenLateral      = $familia->imagenLateral;
    }

} else {
    // “Todos” dentro de una sección
    $catSec = CatalogoSeccion::find($catalogo->id, $menuactual);

    if ($catSec) {
        foreach ($catSec->familias as $fams) {
            foreach ($fams->articulos as $articulo) {
                /** @var CatalogoSeccionFamiliaArticulo $articulo */
                if ((int)$articulo->idCatalogo === (int)$catalogo->id
                    && (int)$articulo->idLineaProducto === (int)$menuactual) {
                    $articulosTodos[] = $articulo;
                }
            }
            if (!empty($fams->imagenLateral)) {
                $imagenLateral = $fams->imagenLateral;
            }
        }
        $tituloCatalogo  = $catSec->lineaProducto->tituloCatalogo;
        $familiaProducto = 'Todos';
    }
}

// ---------- CONTEXTO DE USUARIO ----------
$distribuidor = (Usuario::logueado()->cliente->listaAplicable == 'D');

// ---------- FAVORITOS DEL USUARIO ----------
$favoritos = Base::getListObject(
    'FavoritoCliente',
    'cod_cliente = ' . Datos::objectToDB(Usuario::logueado()->cliente->id)
);
$arrayFavoritos = array();
foreach ($favoritos as $favorito) {
    $arrayFavoritos[] = $favorito->idArticulo . '_' . $favorito->idColorPorArticulo;
}

// ---------- STOCK ----------
$stock          = array(); // [articulo][color][talleIndex] => cantidad
$where          = '';
$whereImagenes  = ' articulo in (';

$x = 0;
$conector = '';

foreach ($articulosTodos as $articulo) {
    /** @var CatalogoSeccionFamiliaArticulo $articulo */
    $x++;
    $where .= '(cod_articulo = ' . Datos::objectToDB($articulo->idArticulo) .
              ' AND cod_color_articulo = ' . Datos::objectToDB($articulo->idColorPorArticulo) . ') OR ';
    if ($x > 1) $conector = ',';
    $whereImagenes .= $conector . Datos::objectToDB($articulo->idArticulo);
}

$whereImagenes .= ')';

// Consultar stocks solo si hay artículos
if (!empty($where)) {
    $where = 'cod_almacen = ' . Datos::objectToDB('01') . ' AND (' . rtrim($where, ' OR ') . ')';
    $stocks = Factory::getInstance()->getArrayFromView('stock_menos_pendiente_vw', $where);

    foreach ($stocks as $item) {
        $art = $item['cod_articulo'];
        $col = $item['cod_color_articulo'];
        if (!isset($stock[$art])) $stock[$art] = array();
        if (!isset($stock[$art][$col])) $stock[$art][$col] = array();
        for ($j = 1; $j <= 10; $j++) {
            $clave = 'S' . $j;
            $stock[$art][$col][$j] = Funciones::toNatural(Funciones::keyIsSet($item, $clave, 0));
        }
    }
}

// ---------- IMÁGENES ----------
// OJO: NO pisar variables; usar mapas con nombres únicos
$mapImagenesColor     = array(); // [producto][] = {ruta,lado,tipo}
$mapImagenesItem      = array(); // [articulo][] = {ruta,lado,tipo}
$mapImagenesSoloColor = array(); // [articulo+codigo_color][] = {ruta,lado,tipo}

if ($x > 0) {
    $articulosImagenes = Factory::getInstance()->getArrayFromView('articulos_imagenes_v', $whereImagenes);
    foreach ($articulosImagenes as $item) {
        $prodKey = $item['producto'];
        $artKey  = $item['articulo'];
        $colKey  = $item['codigo_color'];

        $img = array(
            'ruta'        => $item['imagen'],
            'lado_imagen' => $item['lado_imagen'],
            'tipo'        => $item['tipo']
        );

        $mapImagenesColor[$prodKey][]                  = $img;
        $mapImagenesItem[$artKey][]                    = $img;
        $mapImagenesSoloColor[$artKey . $colKey][]     = $img;
    }
}

// ---------- ARMADO DE RESULTADO ----------
$articulos        = array();
$articulosUnicos  = array();

foreach ($articulosTodos as $articulo) {
    /** @var CatalogoSeccionFamiliaArticulo $articulo */

    // Seguridad extra: no dejar pasar “otros” por si viene ensuciado
    if ((int)$articulo->idCatalogo !== (int)$catalogo->id) continue;
    if ((int)$articulo->idLineaProducto !== (int)$menuactual) continue;
    if ($submenuactual != 'all' && (int)$articulo->idFamiliaProducto !== (int)$submenuactual) continue;

    $cantidadTalles  = array();
    $imagenesItemI   = array();
    $imagenesArticulo= array();
    $imagenesColorDeEste = array();
    $stockInterno    = 0;
    $primerTalle     = '';
    $ultimoTalle     = '';

    // Talles/Stock
    if (isset($articulo->articulo->rangoTalle->posicion) && is_array($articulo->articulo->rangoTalle->posicion)) {
        foreach ($articulo->articulo->rangoTalle->posicion as $key => $talle) {
            if ($talle !== 'X' && $talle !== '0' && $talle !== '') {
                $cant = 0;
                if (isset($stock[$articulo->articulo->id][$articulo->idColorPorArticulo][$key])) {
                    $cant = (int)$stock[$articulo->articulo->id][$articulo->idColorPorArticulo][$key];
                }
                $cantidadTalles[] = array('talle' => $talle, 'cantidad' => $cant);
                $stockInterno += $cant;
            }
        }
    }

    if (count($cantidadTalles) > 0) {
        $primerTalle = $cantidadTalles[0]['talle'];
        $ultimoTalle = $cantidadTalles[count($cantidadTalles) - 1]['talle'];
    }

    // Imágenes
    if (isset($mapImagenesSoloColor[$articulo->articulo->id . $articulo->idColorPorArticulo])) {
        $imagenesItemI = $mapImagenesSoloColor[$articulo->articulo->id . $articulo->idColorPorArticulo];
    }
    if (isset($mapImagenesItem[$articulo->articulo->id])) {
        $imagenesArticulo = $mapImagenesItem[$articulo->articulo->id];
    }
    if (isset($mapImagenesSoloColor[$articulo->articulo->id . $articulo->colorPorArticulo->id])) {
        $imagenesColorDeEste = $mapImagenesSoloColor[$articulo->articulo->id . $articulo->colorPorArticulo->id];
    }

    $keyUnico = $articulo->colorPorArticulo->referenciaWebMayorista . $articulo->colorPorArticulo->id;

    // Crear artículo “único” (grupo de subArtículos por color/ref)
    if (!isset($articulosUnicos[$keyUnico])) {
        $subArticulos = array();

        if (!empty($articulo->colorPorArticulo->tipoProductoStock->nombreCatalogo)) {
            $subArticulos[] = array(
                'idArticulo'              => $articulo->idArticulo,
                'nombre'                  => $articulo->articulo->nombre,
                'idColorPorArticulo'      => $articulo->idColorPorArticulo,
                'formaDeComercializacion' => $articulo->colorPorArticulo->formaDeComercializacion,
                'precioMayorista'         => ($distribuidor ? $articulo->colorPorArticulo->precioDistribuidor : $articulo->colorPorArticulo->precioMayoristaDolar),
                'precioMinorista'         => ($distribuidor ? $articulo->colorPorArticulo->precioDistribuidorMinorista : $articulo->colorPorArticulo->precioMinoristaDolar),
                'stock'                   => $stockInterno,
                'cantidadTalles'          => $cantidadTalles,
                'colorPorArticulo'        => array(
                    'nombre'            => $articulo->colorPorArticulo->nombre,
                    'id'                => $articulo->colorPorArticulo->id,
                    'tipoProductoStock' => array(
                        'id'            => $articulo->colorPorArticulo->idTipoProductoStock,
                        'nombre'        => $articulo->colorPorArticulo->tipoProductoStock->nombreCatalogo,
                        'descuentoPorc' => $articulo->colorPorArticulo->tipoProductoStock->descuentoPorc,
                    ),
                ),
                'primerTalle'             => $primerTalle,
                'ultimoTalle'             => $ultimoTalle,
            );
        }

        $articulosUnicos[$keyUnico] = array(
            'idArticulo'         => $articulo->idArticulo,
            'idColorPorArticulo' => '',
            'articulo'           => array('nombre' => $articulo->articulo->nombre),
            'colorPorArticulo'   => array(
                'nombre'            => $articulo->colorPorArticulo->nombre,
                'id'                => $articulo->colorPorArticulo->id,
                'tipoProductoStock' => array(
                    'id'            => $articulo->colorPorArticulo->idTipoProductoStock,
                    'nombre'        => $articulo->colorPorArticulo->tipoProductoStock->nombreCatalogo,
                    'descuentoPorc' => $articulo->colorPorArticulo->tipoProductoStock->descuentoPorc
                )
            ),
            'categoria'               => $articulo->articulo->nombre,
            'formaDeComercializacion' => $articulo->colorPorArticulo->formaDeComercializacion,
            'stock'                   => $stockInterno, // inicial
            'favorito'                => in_array($articulo->idArticulo . '_' . $articulo->idColorPorArticulo, $arrayFavoritos),
            'filtros'                 => false,
            'cantidadTalles'          => $cantidadTalles,
            'imagenesArticulo'        => $imagenesColorDeEste,
            'rutaIframe3D'            => $articulo->colorPorArticulo->ecommerceImage1,
            'subArticulos'            => $subArticulos,
        );

    } elseif (!empty($articulo->colorPorArticulo->tipoProductoStock->nombreCatalogo)) {
        // Agregar otro subArtículo al grupo
        $articulosUnicos[$keyUnico]['subArticulos'][] = array(
            'idArticulo'              => $articulo->idArticulo,
            'nombre'                  => $articulo->articulo->nombre,
            'idColorPorArticulo'      => $articulo->idColorPorArticulo,
            'formaDeComercializacion' => $articulo->colorPorArticulo->formaDeComercializacion,
            'precioMayorista'         => ($distribuidor ? $articulo->colorPorArticulo->precioDistribuidor : $articulo->colorPorArticulo->precioMayoristaDolar),
            'precioMinorista'         => ($distribuidor ? $articulo->colorPorArticulo->precioDistribuidorMinorista : $articulo->colorPorArticulo->precioMinoristaDolar),
            'stock'                   => $stockInterno,
            'cantidadTalles'          => $cantidadTalles,
            'colorPorArticulo'        => array(
                'nombre'            => $articulo->colorPorArticulo->nombre,
                'id'                => $articulo->colorPorArticulo->id,
                'tipoProductoStock' => array(
                    'id'            => $articulo->colorPorArticulo->idTipoProductoStock,
                    'nombre'        => $articulo->colorPorArticulo->tipoProductoStock->nombreCatalogo,
                    'descuentoPorc' => $articulo->colorPorArticulo->tipoProductoStock->descuentoPorc
                )
            ),
            'primerTalle'             => $primerTalle,
            'ultimoTalle'             => $ultimoTalle,
        );
    }
}

// Normalización final de items (sumo stock de subartículos)
$newItems = array();
foreach ($articulosUnicos as $idArticulo => $articulo) {
    $totalStock = (int)$articulo['stock'];
    if (!empty($articulo['subArticulos'])) {
        foreach ($articulo['subArticulos'] as $sub) {
            $totalStock += (int)$sub['stock'];
        }
    }

    $newItems[] = array(
        'idArticulo'            => $articulo['idArticulo'],
        'idColorPorArticulo'    => '',
        'articulo'              => $articulo['articulo'],
        'colorPorArticulo'      => $articulo['colorPorArticulo'],
        'categoria'             => $articulo['categoria'],
        'formaDeComercializacion'=> $articulo['formaDeComercializacion'],
        'stock'                 => $totalStock,
        'favorito'              => $articulo['favorito'],
        'filtros'               => $articulo['filtros'],
        'cantidadTalles'        => $articulo['cantidadTalles'],
        'imagenesArticulo'      => $articulo['imagenesArticulo'],
        'rutaIframe3D'          => $articulo['rutaIframe3D'],
        'subArticulos'          => $articulo['subArticulos'],
    );
}

$articulos = $newItems;

?>


<style>

    a {
        text-decoration: none;
        color: inherit;
    }
    h1 {
        margin-top: 10px;
        margin-bottom: 20px;
    }

    .item-stock {
      font-size: 100%;
      cursor: pointer;
      left: 0;
      right: 0;
      display: flex;
      flex-direction: row;
      justify-content: space-around;
    }

    .tipo-boton {
      cursor: pointer;
    }

    span.item-stock-badge {
      color: #5DB44C;
    }

    .item-stock-produccion {
      top: 66px;
    }

    div.modal-body iframe {
      width: 100%;
    }
    .descripcion_lateral {
      font-size: large;
    font-weight: 400;
      padding-bottom: 10px;
    display: flex;
    flex-direction: column;
     flex-wrap: wrap;
     padding-top: 3.666667%;
     padding-left: 5px;
	 }
   .subarticulo {
     /* display: block; */
     display: flex;
     justify-content: space-between;
   }
   .subarticulo.sub-inline-grid {
     display: grid;
     justify-content: center;
   }

   .subarticulo .badge {
     background-color: #cacaca;
     margin-bottom: 2px;
   }
   .subarticulo .badge.badge-danger {
     background-color: #d9534f;
   }
   .subarticulo .badge.inverted {
     border: 1px solid #afafaf;
     color: #afafaf;
     background: none;
   }
   .subitem-talles {
     position: absolute;
     bottom: 0;
     left: 10px;
     right: 10px;
}

img {
  max-width: 100%;
}

    @media (min-width: 786px) { /* Para smartphones */
    }
.familia-header {
  display: flex
;
  /* flex-direction: column; */
  /* align-items: center; */
  width: 100%;
  /* margin: 0 auto; */
  /* padding: 10px; */
  background-color: #black;
}
  
    @media (max-width: 480px) { /* Para smartphones */

    .skip-row-top {
        padding-top:4%;
    }

        div.modal-body iframe {
          height: 270px;
        }
        .familia-header {
            width: 100%;
            /* height: 200px; /* Ajusta según necesidad */ */
            overflow: hidden;
            display: flex;
            padding: 0px;
            align-items: center;
            justify-content: center;
            background-color: #f4f4f4; /* Fondo en caso de que la imagen no cargue */


        }
    }

    @media (max-width: 768px) {



        .skip-row-top {
            padding-top:4%;
        }
        div.modal-body iframe {
          height: 350px;
        }
        .familia-header {
            width: 100%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f4f4f4; /* Fondo en caso de que la imagen no cargue */
        }
    }



    @media (max-width: 1200px) {
        .familia-header {
          max-width: 100%;   /* Para que no se desborde */
      }
      .familia-header {

            .skip-row-top {
            padding-top: 4%;
        }
        .row{    width: 100%;
}
        .descripcion_lateral {
            width: 100%;
      display: flex  ;
      flex-direction: column;
      flex-wrap: wrap;
      padding-top: 3.666667%;
      margin-bottom: 4%;
      background-color: #black; /* Fondo en caso de que la imagen no cargue */
        }

  }}
</style>



  <script>
$(document).ready(function() {
    <?php if (!$menuactual || !$submenuactual) echo 'window.location.href = "/";'; ?>
});

Koi.controller('CatalogoCtrl', function ($scope, $filter, $timeout, ServiceCliente, ServiceCatalogo) {
    ServiceCatalogo.filtros.show = true;

    $scope.funciones = funciones;
    $scope.imagesUrl = 'https://www.spiralshoes.com/zapatillas/jpg/';
    $scope.sortBySelect = '';
    $scope.sortMenuVisible = false;
$scope.favoriteMenuVisible = false;
    let orderBy = $filter('orderBy');
    let sortMenu = document.getElementById('sort-menu');
    let favoritesMenu = document.getElementById('favorites-menu');
    let spinner = document.querySelector('div.div-loader');

    $scope.articlesSort = function(sortBy, reverse) {
        $scope.articulos = orderBy($scope.articulos, sortBy, reverse);
    };

    $scope.$on('Catalogo:FiltrosAplicados:changed', function (e, filtrosAplicados) {
        angular.forEach($scope.articulos, function (item) {
            if (filtrosAplicados.tipoProductoStock.indexOf('12') >= 0) {
                item.filtros = filtrosAplicados.tipoProductoStock.length === 1
                    ? item.stock > 0
                    : item.stock > 0 && filtrosAplicados.tipoProductoStock.includes(item.colorPorArticulo.tipoProductoStock.id + '');
            } else {
                item.filtros = filtrosAplicados.tipoProductoStock.includes(item.colorPorArticulo.tipoProductoStock.id + '');
            }
        });
    });

    $scope.$on('Catalogo:SortBy:changed', function (e, sortBy) {
        let mapSort = {
            catalogo: { field: 'articulos.nombre', reverse: false },
            stock: { field: 'stock', reverse: false },
            category: { field: 'categoria', reverse: false },
            "mayor-precio-mayorista": { field: 'precioMayorista', reverse: true },
            "menor-precio-mayorista": { field: 'precioMayorista', reverse: false }
        };
        $scope.articlesSort(mapSort[sortBy].field, mapSort[sortBy].reverse);
    });

    // Mostrar menús


    $scope.showMenuSort = function (visible) {
        sortMenu.classList.toggle('equal-sort-items-hover');
    };
    $scope.showMenuFavorite = function (visible) {
        favoritesMenu.classList.toggle('equal-favorites-items-hover');
    };


function actualizarIconoFavorito(articulo, estadoForzado = null) {
  const idArticulo = articulo.idArticulo || articulo?.articulo?.id;
  const idColor    = articulo.idColorPorArticulo || articulo?.colorPorArticulo?.id;

  if (!idArticulo || !idColor) return;

  const id    = `star-${idArticulo}-${idColor}`;
  const icon  = document.getElementById(id);
  const estado = (estadoForzado !== null ? estadoForzado : articulo.favorito) ? true : false;

  if (!icon) return; // <- si no hay icono (subArt), no ensuciamos consola

  icon.classList.remove("fa-star", "fa-star-o", "star-on", "star-off");
  icon.classList.add(estado ? "fa-star" : "fa-star-o");
  icon.classList.add(estado ? "star-on" : "star-off");
}


    
    // Toggle individual
$scope.toggleFavorito = function (articulo) {
  if (!articulo) return;

  const estaba = !!articulo.favorito;
  const nuevo  = !estaba;

  // Actualizar modelo (esto pinta el ícono vía ng-class de inmediato)
  articulo.favorito = nuevo;
  if (articulo.subArticulos && articulo.subArticulos.length) {
    articulo.subArticulos.forEach(sa => sa.favorito = nuevo);
  }
  $scope.$applyAsync(); // asegurar digest

  // Armar payload
  const payload = (articulo.subArticulos && articulo.subArticulos.length)
    ? articulo.subArticulos.map(sa => ({
        idArticulo: sa.idArticulo,
        idColorPorArticulo: sa.colorPorArticulo?.id
      })).filter(p => p.idArticulo && p.idColorPorArticulo)
    : [{ idArticulo: articulo.idArticulo, idColorPorArticulo: articulo.colorPorArticulo?.id }];

  // Llamada a backend
  const p = estaba
    ? ServiceCliente.removeFavoritoBatch(payload)
    : ServiceCliente.addFavoritoBatch(payload);

  p.then(res => {
    console.log("✔️ Backend confirmó favoritos:", res);
    $scope.$applyAsync();
  }).catch(err => {
    console.error("🔥 Error desde backend, revirtiendo...", err);
    // Revertir si falló
    articulo.favorito = estaba;
    if (articulo.subArticulos && articulo.subArticulos.length) {
      articulo.subArticulos.forEach(sa => sa.favorito = estaba);
    }
    $scope.$applyAsync();
  });
};


    // Acción múltiple: seleccionar favoritos
$scope.addFavoriteButton = async function () {
  spinner.style.display = 'block';
  let favorites = [];

  $scope.articulos.forEach(art => {
    art.favorito = true;
    // sactualizarIconoFavorito(art); 

    if (art.subArticulos?.length) {
      art.subArticulos.forEach(sa => {
        sa.favorito = true;
        favorites.push({ idArticulo: sa.idArticulo, idColorPorArticulo: sa.colorPorArticulo?.id });
      });
    } else {
      favorites.push({ idArticulo: art.idArticulo, idColorPorArticulo: art.colorPorArticulo?.id });
    }
  });


  try {
    await ServiceCliente.addFavoritoBatch(favorites);
    console.log("✅ Todos marcados como favoritos.");
    if (!$scope.$$phase) $scope.$apply();
  } catch (e) {
    console.error("🔥 Error al agregar favoritos:", e);
  } finally {
    spinner.style.display = 'none';
  }
};
$scope.articulos = <?php echo json_encode($articulos); ?>;
$scope.articulos.forEach(a => { a.favorito = !!a.favorito; });


$scope.removeFavoriteButton = async function () {
  spinner.style.display = 'block';
  let favorites = [];

  $scope.articulos.forEach(art => {
    art.favorito = false;
    // actualizarIconoFavorito(art); 

    if (art.subArticulos?.length) {
      art.subArticulos.forEach(sa => {
        sa.favorito = false;
        favorites.push({ idArticulo: sa.idArticulo, idColorPorArticulo: sa.colorPorArticulo?.id });
      });
    } else {
      favorites.push({ idArticulo: art.idArticulo, idColorPorArticulo: art.colorPorArticulo?.id });
    }
  });

  try {
    await ServiceCliente.removeFavoritoBatch(favorites);
    console.log("✅ Todos desmarcados.");
    if (!$scope.$$phase) $scope.$apply();
  } catch (e) {
    console.error("🔥 Error quitando favoritos:", e);
  } finally {
    spinner.style.display = 'none';
  }
};






    // Utilidades
    $scope.getArticulo = function (index) {
        return $scope.articulos[($scope.pagina - 1) * $scope.cantPorPagina + index];
    };
    $scope.getName = function (articulo) {
        return !articulo ? '' : articulo.articulo.nombre + ' - ' + articulo.idArticulo + ' ' + articulo.idColorPorArticulo;
    };
    $scope.getPrecioMayorista = function (articulo) {
        return articulo.precioMayorista - (articulo.colorPorArticulo.tipoProductoStock.descuentoPorc / 100) * articulo.precioMayorista;
    };
    $scope.getPrecioMinorista = function (articulo) {
        return articulo.precioMinorista;
    };
    $scope.getImageUrl = function (articulo) {
        let result = articulo.imagenesArticulo ? articulo.imagenesArticulo.filter(imagen => imagen.lado_imagen == 'e') : [];
        return result.length > 0
            ? $scope.imagesUrl + result[0].ruta
            : articulo.imagenesArticulo?.length
                ? $scope.imagesUrl + articulo.imagenesArticulo[0].ruta
                : $scope.getEmptyImageUrl();
    };
    $scope.getImageUrls = function (articulo) {
        let images = articulo.imagenesArticulo?.map(imagen => ({
            ruta: $scope.imagesUrl + imagen.ruta,
            orden: imagen.orden || 0
        })) || [];
        return images.length > 0 ? images : [{ ruta: $scope.getEmptyImageUrl(), orden: 0 }];
    };
    $scope.getEmptyImageUrl = function () {
        return $scope.imagesUrl + 'empty.jpg';
    };
    $scope.getUnavailableImageUrl = function () {
        return $scope.imagesUrl + 'empty.jpg';
    };
    $scope.getIdNameModal3d = articulo => '#modal' + articulo.idArticulo + articulo.idColorPorArticulo;
    $scope.getNameModal3d = articulo => 'modal' + articulo.idArticulo + articulo.idColorPorArticulo;
    $scope.getArticuloCodigoColor = articulo => articulo.idArticulo + '-' + articulo.idColorPorArticulo;
    $scope.getArticuloRutaIframe3D = articulo => articulo.rutaIframe3D;
   $scope.getIdStarArticulo = (a) => {
  const idArt = a.idArticulo || a?.articulo?.id || '';
  const idCol = a.idColorPorArticulo || a?.colorPorArticulo?.id || '';
  return `star-${idArt}-${idCol}`;
};


    // Modelo de datos inyectado desde PHP
    $scope.articulos = <?php echo json_encode($articulos); ?>;
});
</script>


<div id="catalogo" ng-controller="CatalogoCtrl">

<?php include('content/cliente/ordenamientosSelecciones.php'); ?>



  <div class="familia-header">
      <?php if (!empty($imagenLateral) && $imagenLateral !== null): ?>
          <img src="https://www.spiralshoes.com/koi/catalogos/img/familia_banner_lateral/<?php echo htmlspecialchars($imagenLateral); ?>">
      <?php endif; ?>
  </div>

    <!-- Mobile -->
    <div class="row">
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 text-left skip-row-top">
            <h3><?php echo $tituloCatalogo ?></h3>
            <h1><?php echo $familiaProducto ?></h1>
            <div class="descripcion_lateral">
              <?php echo $familiaDescripcion; ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-4 item" ng-repeat="articulo in articulos" ng-show="articulo.filtros">
                    <div id = "tarjeta" class="item-inner">
                        <a href="javascript:;" gallery-modal={{getImageUrls(articulo)}}>

                            <img ng-src="{{getImageUrl(articulo)}}" default-src="{{getUnavailableImageUrl()}}">

                            <div class="">
                              <a href="www.spiralshoes.com"></a>
                            </div>
                        </a>
                        <div class="item-tipo">
                        <span>
                      
                        </span>
                         
                       </div>
                        <div class="item-precios">
                             <!--<span>{{ funciones.formatearMoneda(getPrecioMayorista(articulo)) }} / {{ funciones.formatearMoneda(getPrecioMinorista(articulo)) }}</span> -->
                            <span class="subarticulo sub-inline-grid" ng-repeat="subarticulo in articulo.subArticulos">
                              <span>{{ subarticulo.nombre + ' - ' + subarticulo.idArticulo + ' ( ' + articulo.colorPorArticulo.id + ' )' }}</span>
                              <span>
                                <span data-toggle="popover" data-html="true" data-placement="bottom" tabindex="0"  item-stock={{subarticulo.cantidadTalles}}>
                                  <span class="badge item-stock-badge">Stock: {{ subarticulo.stock }}</span>
                                </span>
                            	   <span class="badge">Pronto: <span class="stock-produccion" stock-produccion={{getArticuloCodigoColor(subarticulo)}}>0</span> </span>
                             </span>
                          	</span>
                        </div>


<span class="item-star"
      ng-click="toggleFavorito(articulo)">
  <i class="fa fa-2x"
     ng-class="articulo.favorito ? 'fa-star star-on' : 'fa-star-o star-off'"
     ng-attr-id="star-{{articulo.idArticulo}}-{{articulo.colorPorArticulo.id}}">
  </i>
</span>


                        <div class="subitem-talles">

							            <span class="subarticulo" ng-repeat="subarticuloi in articulo.subArticulos">
                            <span>
                              <span class="badge">{{ subarticuloi.primerTalle }} - {{ subarticuloi.ultimoTalle }}</span>
                              <span class="badge inverted">{{ subarticuloi.formaDeComercializacion }}</span>
                            </span>
                            <span>
    	                        <span class="badge"  class="{'badge-danger': subarticuloi.colorPorArticulo.tipoProductoStock.id == '1'}">{{ subarticuloi.colorPorArticulo.tipoProductoStock.nombre }}</span>
    	                        <span class="badge badge-danger" ng-if="subarticuloi.colorPorArticulo.tipoProductoStock.descuentoPorc">-{{ subarticuloi.colorPorArticulo.tipoProductoStock.descuentoPorc }}%</span>
                            </span>
  	                        <span>  | {{ funciones.formatearMoneda(getPrecioMayorista(subarticuloi)) }} </span>
	                        </span>
                  
                        </div>
                      
        <div id={{getNameModal3d(articulo)}} class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
          <span class="_ruta_iframe_3d_" style="display: none">{{articulo.rutaIframe3D}}</span>
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
              </div>
              <div class="modal-body">

              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
