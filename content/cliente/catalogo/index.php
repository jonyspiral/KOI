<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$menuactual = (array_key_exists('c', $_REQUEST)) ? $_REQUEST['c'] : '';
//$menuactual = -1;
$submenuactual = (array_key_exists('f', $_REQUEST)) ? $_REQUEST['f'] : '';

$catalogo = Catalogo::ultimo();
//var_dump($catalogo); echo '<br>';
$tituloCatalogo = '';
$familiaProducto = '';
$familiaDescripcion = '';
$articulosTodos = array();
if ($submenuactual != 'all') {
  //echo '<br>no es all->';
  $familia = CatalogoSeccionFamilia::find($catalogo->id, $menuactual, $submenuactual);// WARNING: original
  //var_dump($familia);
  $articulosTodos = $familia->articulos;
  $tituloCatalogo = $familia->lineaProducto->tituloCatalogo;
  $familiaProducto = $familia->familiaProducto->nombre;
  $familiaDescripcion = $familia->descripcion;
  $imagenLateral = $familia->imagenLateral;
} else {
  //echo '<br>es all->';
  $catSec = CatalogoSeccion::find($catalogo->id, $menuactual);
  //var_dump($catSec);
  foreach ($catSec->familias as $fams) {
    //print_r('fams id'.$fams->id);
    foreach($fams->articulos as $articulo) {
      //print_r('articulo id'. $articulo->id);
      $articulosTodos[] = $articulo;
    }
    if ($fams->imagenLateral) {
      $imagenLateral = $fams->imagenLateral;
    }
  }
  $tituloCatalogo = $catSec->lineaProducto->tituloCatalogo;
  $familiaProducto = 'Todos';
}

//var_dump(htmlspecialchars($imagenLateral));
//die;

$distribuidor = Usuario::logueado()->cliente->listaAplicable == 'D';

// Favoritos
$favoritos = Base::getListObject('FavoritoCliente', 'cod_cliente = ' . Datos::objectToDB(Usuario::logueado()->cliente->id));
$arrayFavoritos = array();
foreach ($favoritos as $favorito) {
  //print_r('favorito->' . $favorito->idArticulo . '_' . $favorito->idColorPorArticulo);
    $arrayFavoritos[] = $favorito->idArticulo . '_' . $favorito->idColorPorArticulo;
}
// Stock
$stock = array();
$where = '';
//$whereImagenes = ' producto in (';
$whereImagenes = ' articulo in (';

$x = 0; $conector = '';
//foreach ($familia->articulos as $articulo) {
foreach ($articulosTodos as $articulo) {
    $x++;
    /** @var CatalogoSeccionFamiliaArticulo $articulo */
    $where .= '(cod_articulo = ' . Datos::objectToDB($articulo->idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($articulo->idColorPorArticulo) . ') OR ';
    if ($x > 1) {
      $conector = ',';
    }
    //$whereImagenes .= $conector . Datos::objectToDB($articulo->idArticulo . $articulo->idColorPorArticulo);
    $whereImagenes .= $conector . Datos::objectToDB($articulo->idArticulo);
}

$whereImagenes .= ')';
// $where = '(cod_almacen = ' . Datos::objectToDB('01') .') OR cod_almacen = ('. Datos::objectToDB('14') .') AND (' . trim($where, ' OR ') . ')';

//consultar stocks
$stocks = array();
if ($where) {
  //print_r('where ->' . $where);
  $where = 'cod_almacen = ' . Datos::objectToDB('01') . ' AND (' . trim($where, ' OR ') . ')';
  $stocks = Factory::getInstance()->getArrayFromView('stock_menos_pendiente_vw', $where);

  foreach ($stocks as $item) {
    for ($j = 1; $j <= 10; $j++) {
        if (!array_key_exists($item['cod_articulo'], $stock)) {
            $stock[$item['cod_articulo']] = array();
        }
        if (!array_key_exists($item['cod_color_articulo'], $stock[$item['cod_articulo']])) {
            $stock[$item['cod_articulo']][$item['cod_color_articulo']] = array();
        }
        $stock[$item['cod_articulo']][$item['cod_color_articulo']][$j] = Funciones::toNatural(Funciones::keyIsSet($item, 'S' . $j, 0));
    }
  }
}

//print_r($stocks); die;


$imagenesColor = array();
$imagenesItem = array();
$imagenesSoloColor = array();
//consultar las imagenes
if ($x > 0) {
  $articulosImagenes = Factory::getInstance()->getArrayFromView('articulos_imagenes_v', $whereImagenes);

  foreach ($articulosImagenes as $item) {
    //print_r($item['producto'] . '-' . $item['articulo'] . '-' . $item['codigo_color']  . '-' . $item['imagen'] . '<br>');
    $imagenesColor[$item['producto']][] = array('ruta' => $item['imagen'], 'lado_imagen' => $item['lado_imagen'], 'tipo' => $item['tipo']);
    $imagenesItem[$item['articulo']][] = array('ruta' => $item['imagen'], 'lado_imagen' => $item['lado_imagen'], 'tipo' => $item['tipo']);
    $imagenesSoloColor[$item['articulo'].$item['codigo_color']][] = array('ruta' => $item['imagen'], 'lado_imagen' => $item['lado_imagen'], 'tipo' => $item['tipo']);
  }
}
$articulos = array();
$articulosUnicos = array();

foreach ($articulosTodos as $articulo) {
    $cantidadTalles = array();
    $imagenesItemI = array();
    $imagenesArticulo = array();
    $imagenesColor = array();
    $stockInterno = 0; // Stock total del artículo principal
    $primerTalle = '';
    $ultimoTalle = '';

    // Calcular stock y talles para este artículo
    foreach ($articulo->articulo->rangoTalle->posicion as $key => $talle) {
        if ($talle != 'X' && $talle != '0' && $talle != '' && isset($stock[$articulo->articulo->id]) && isset($stock[$articulo->articulo->id][$articulo->idColorPorArticulo])) {
            $cantidadTalles[] = array(
                'talle' => $talle,
                'cantidad' => $stock[$articulo->articulo->id][$articulo->idColorPorArticulo][$key],
            );
            $stockInterno += intval($stock[$articulo->articulo->id][$articulo->idColorPorArticulo][$key]);
        }
    }

    if (count($cantidadTalles) > 0) {
        $primerTalle = $cantidadTalles[0]['talle'];
        $ultimoTalle = $cantidadTalles[count($cantidadTalles) - 1]['talle'];
    }

    if (isset($imagenesColor[$articulo->articulo->id . $articulo->idColorPorArticulo])) {
        $imagenesItemI = $imagenesColor[$articulo->articulo->id . $articulo->idColorPorArticulo];
    }

    if (isset($imagenesItem[$articulo->articulo->id])) {
        $imagenesArticulo = $imagenesItem[$articulo->articulo->id];
    }

    if (isset($imagenesSoloColor[$articulo->articulo->id . $articulo->colorPorArticulo->id])) {
        $imagenesColor = $imagenesSoloColor[$articulo->articulo->id . $articulo->colorPorArticulo->id];
    }

    // Si no existe el artículo único, crearlo con el stock total
    if (!isset($articulosUnicos[$articulo->colorPorArticulo->referenciaWebMayorista . $articulo->colorPorArticulo->id])) {
        $subArticulos = array();
        if ($articulo->colorPorArticulo->tipoProductoStock->nombreCatalogo) {
            $subArticulos[] = array(
                'idArticulo' => $articulo->idArticulo,
                'nombre' => $articulo->articulo->nombre,
                'idColorPorArticulo' => $articulo->idColorPorArticulo,
                'formaDeComercializacion' => $articulo->colorPorArticulo->formaDeComercializacion,
                'precioMayorista' => $distribuidor ? $articulo->colorPorArticulo->precioDistribuidor : $articulo->colorPorArticulo->precioMayoristaDolar,
                'precioMinorista' => $distribuidor ? $articulo->colorPorArticulo->precioDistribuidorMinorista : $articulo->colorPorArticulo->precioMinoristaDolar,
                'stock' => $stockInterno, // Stock del subartículo
                'cantidadTalles' => $cantidadTalles,
                'colorPorArticulo' => array(
                    'nombre' => $articulo->colorPorArticulo->nombre,
                    'id' => $articulo->colorPorArticulo->id,
                    'tipoProductoStock' => array(
                        'id' => $articulo->colorPorArticulo->idTipoProductoStock,
                        'nombre' => $articulo->colorPorArticulo->tipoProductoStock->nombreCatalogo,
                        'descuentoPorc' => $articulo->colorPorArticulo->tipoProductoStock->descuentoPorc,
                    ),
                ),
                'primerTalle' => $primerTalle,
                'ultimoTalle' => $ultimoTalle,
            );
        }

        $articulosUnicos[$articulo->colorPorArticulo->referenciaWebMayorista . $articulo->colorPorArticulo->id] = array(
            'idArticulo' => $articulo->idArticulo,
            'idColorPorArticulo' => '',
            'articulo' => array(
                'nombre' => $articulo->articulo->nombre
            ),
            'colorPorArticulo' => array(
                'nombre' => $articulo->colorPorArticulo->nombre,
                'id' => $articulo->colorPorArticulo->id,
                'tipoProductoStock' => array(
                    'id' => $articulo->colorPorArticulo->idTipoProductoStock,
                    'nombre' => $articulo->colorPorArticulo->tipoProductoStock->nombreCatalogo,
                    'descuentoPorc' => $articulo->colorPorArticulo->tipoProductoStock->descuentoPorc
                )
            ),
            'categoria' => $articulo->articulo->nombre,
            'formaDeComercializacion' => $articulo->colorPorArticulo->formaDeComercializacion,
            'stock' => $stockInterno, // Stock total del artículo principal (inicialmente el mismo que $stockInterno)
            'favorito' => in_array($articulo->idArticulo . '_' . $articulo->idColorPorArticulo, $arrayFavoritos),
            'filtros' => false,
            'cantidadTalles' => $cantidadTalles,
            'imagenesArticulo' => $imagenesColor,
            'rutaIframe3D' => $articulo->colorPorArticulo->ecommerceImage1,
            'subArticulos' => $subArticulos,
        );
    } elseif ($articulo->colorPorArticulo->tipoProductoStock->nombreCatalogo) {
        $articulosUnicos[$articulo->colorPorArticulo->referenciaWebMayorista . $articulo->colorPorArticulo->id]['subArticulos'][] = array(
            'idArticulo' => $articulo->idArticulo,
            'nombre' => $articulo->articulo->nombre,
            'idColorPorArticulo' => $articulo->idColorPorArticulo,
            'formaDeComercializacion' => $articulo->colorPorArticulo->formaDeComercializacion,
            'precioMayorista' => $distribuidor ? $articulo->colorPorArticulo->precioDistribuidor : $articulo->colorPorArticulo->precioMayoristaDolar,
            'precioMinorista' => $distribuidor ? $articulo->colorPorArticulo->precioDistribuidorMinorista : $articulo->colorPorArticulo->precioMinoristaDolar,
            'stock' => $stockInterno, // Stock del subartículo
            'cantidadTalles' => $cantidadTalles,
            'colorPorArticulo' => array(
                'nombre' => $articulo->colorPorArticulo->nombre,
                'id' => $articulo->colorPorArticulo->id,
                'tipoProductoStock' => array(
                    'id' => $articulo->colorPorArticulo->idTipoProductoStock,
                    'nombre' => $articulo->colorPorArticulo->tipoProductoStock->nombreCatalogo,
                    'descuentoPorc' => $articulo->colorPorArticulo->tipoProductoStock->descuentoPorc
                )
            ),
            'primerTalle' => $primerTalle,
            'ultimoTalle' => $ultimoTalle,
        );
    }
}

$newItems = array();
foreach ($articulosUnicos as $idArticulo => $articulo) {
    $totalStock = $articulo['stock']; // Stock inicial del artículo principal
    if (!empty($articulo['subArticulos'])) {
        // Sumar el stock de todos los subArticulos al stock del artículo principal
        foreach ($articulo['subArticulos'] as $sub) {
            $totalStock += $sub['stock'];
        }
    }

    $newItems[] = array(
        'idArticulo' => $articulo['idArticulo'],
        'idColorPorArticulo' => '',
        'articulo' => $articulo['articulo'],
        'colorPorArticulo' => $articulo['colorPorArticulo'],
        'categoria' => $articulo['categoria'],
        'formaDeComercializacion' => $articulo['formaDeComercializacion'],
        'stock' => $totalStock, // Stock total (principal + subArticulos)
        'favorito' => $articulo['favorito'],
        'filtros' => $articulo['filtros'],
        'cantidadTalles' => $articulo['cantidadTalles'],
        'imagenesArticulo' => $articulo['imagenesArticulo'],
        'rutaIframe3D' => $articulo['rutaIframe3D'],
        'subArticulos' => $articulo['subArticulos'],
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
    const idColor = articulo.idColorPorArticulo || articulo?.colorPorArticulo?.id;

    if (!idArticulo || !idColor) return;

    const id = `star-${idArticulo}-${idColor}`;
    const icon = document.getElementById(id);
    const estado = estadoForzado !== null ? estadoForzado : articulo.favorito;

    if (icon) {
        icon.classList.remove("fa-star", "fa-star-o", "star-on", "star-off");
        icon.classList.add(estado ? "fa-star" : "fa-star-o");
        icon.classList.add(estado ? "star-on" : "star-off");
    } else {
        console.warn("❌ No se encontró el icono:", id);
    }
}
    
    // Toggle individual
$scope.toggleFavorito = function (articulo) {
    if (!articulo?.subArticulos?.length) {
        console.error("❌ Artículo sin subArticulos:", articulo);
        return;
    }

    const esFavorito = articulo.favorito;

    // Cambia visual y lógica de todos los subArticulos
    articulo.subArticulos.forEach(sa => {
        sa.favorito = !esFavorito;
        actualizarIconoFavorito(sa);
    });
    articulo.favorito = !esFavorito;
    actualizarIconoFavorito(articulo); // También el principal

    const payload = articulo.subArticulos.map(sa => ({
        idArticulo: sa.idArticulo,
        idColorPorArticulo: sa.colorPorArticulo?.id
    })).filter(p => p.idArticulo && p.idColorPorArticulo);

    setTimeout(async () => {
        try {
            const res = esFavorito
                ? await ServiceCliente.removeFavoritoBatch(payload)
                : await ServiceCliente.addFavoritoBatch(payload);

            console.log("✔️ Backend confirmó favoritos:", res);

            // Reforzar íconos visuales (por si acaso)
            articulo.subArticulos.forEach(sa => actualizarIconoFavorito(sa));
            actualizarIconoFavorito(articulo);
            if (!$scope.$$phase) $scope.$apply();
        } catch (err) {
            console.error("🔥 Error desde backend, revirtiendo...", err);

            // Restaurar visualmente
            articulo.subArticulos.forEach(sa => {
                sa.favorito = esFavorito;
                actualizarIconoFavorito(sa);
            });
            articulo.favorito = esFavorito;
            actualizarIconoFavorito(articulo);
            if (!$scope.$$phase) $scope.$apply();
        }
    }, 50);
};







    // Acción múltiple: seleccionar favoritos
$scope.addFavoriteButton = async function () {
    console.log("🟡 Ejecutando addFavoriteButton (con subArticulos)");

    spinner.style.display = 'block';
    let favorites = [];

    $scope.articulos.forEach(articulo => {
        articulo.favorito = true;
        actualizarIconoFavorito(articulo);

        if (articulo.subArticulos?.length) {
            articulo.subArticulos.forEach(sa => {
                sa.favorito = true;
                favorites.push({
                    idArticulo: sa.idArticulo,
                    idColorPorArticulo: sa.colorPorArticulo?.id
                });
                actualizarIconoFavorito(sa);
            });
        } else {
            favorites.push({
                idArticulo: articulo.idArticulo,
                idColorPorArticulo: articulo.colorPorArticulo?.id
            });
        }
    });

    try {
        await ServiceCliente.addFavoritoBatch(favorites);
        console.log("✅ Todos marcados como favoritos.");
        if (!$scope.$$phase) $scope.$apply();
    } catch (e) {
        console.error("🔥 Error al agregar favoritos:", e);
    }

    spinner.style.display = 'none';
};





    // Acción múltiple: quitar favoritos
$scope.removeFavoriteButton = async function () {
    console.log("🧹 Ejecutando removeFavoriteButton (full cleanup)");

    spinner.style.display = 'block';
    let favorites = [];

    $scope.articulos.forEach(articulo => {
        articulo.favorito = false;
        actualizarIconoFavorito(articulo);

        if (articulo.subArticulos?.length) {
            articulo.subArticulos.forEach(sa => {
                sa.favorito = false;
                favorites.push({
                    idArticulo: sa.idArticulo,
                    idColorPorArticulo: sa.colorPorArticulo?.id
                });
                actualizarIconoFavorito(sa);
            });
        } else {
            favorites.push({
                idArticulo: articulo.idArticulo,
                idColorPorArticulo: articulo.colorPorArticulo?.id
            });
        }
    });

    try {
        await ServiceCliente.removeFavoritoBatch(favorites);
        console.log("✅ Todos desmarcados.");
        if (!$scope.$$phase) $scope.$apply();
    } catch (e) {
        console.error("🔥 Error quitando favoritos:", e);
    }

    spinner.style.display = 'none';
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
    $scope.getIdStarArticulo = articulo => "star-" + articulo.idArticulo + '-' + articulo.idColorPorArticulo;

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
      data-idarticulo="{{articulo.idArticulo}}"
      data-idcolorporarticulo="{{articulo.colorPorArticulo.id}}"
      ng-click="toggleFavorito(articulo)">
  <i class="fa fa-2x"
     ng-class="articulo.favorito ? 'fa-star star-on' : 'fa-star-o star-off'"
     id="star-{{articulo.idArticulo}}-{{articulo.colorPorArticulo.id}}">
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
