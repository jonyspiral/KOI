<?php
/*************************************************************
 * KOI1 - Clientes / Catálogo
 * Archivo: content/cliente/catalogo/index.php
 * Objetivo: Listar productos y permitir enviar a Favoritos
 * Compatibilidad: PHP 5.2/5.6  (sin short arrays)
 *
 * 📝 Ordenado por Sofía - 2025-09-04 23:15
 * NOTA: Angular/JS del módulo se cargan por premaster/includes.
 *************************************************************/

/* ==========================================================
 * 1) Bootstrap & Dependencias (flujo KOI1)
 * ========================================================== */
@ini_set('display_errors', 1);
@ini_set('display_startup_errors', 1);
@error_reporting(E_ALL);

// KOI1: incluir premaster ANTES de cualquier salida
require_once dirname(__FILE__) . '/../../../premaster.php';

/* ==========================================================
 * 2) Helpers locales (compat PHP 5.x)
 * ========================================================== */
function rq_clean($key, $default) {
    if (!isset($_REQUEST[$key])) return $default;
    // Permitimos letras, números, guion, guion bajo y slash (menús anidados)
    return preg_replace('/[^a-zA-Z0-9_\\-\\/]/', '', $_REQUEST[$key]);
}

/* ==========================================================
 * 3) Parámetros de entrada (sección / familia)
 * ========================================================== */
$menuactual    = rq_clean('c', '');
$submenuactual = rq_clean('f', '');

/* ==========================================================
 * 4) Contexto del Catálogo (respetando clases KOI1)
 * ========================================================== */
$catalogo = null;
try { $catalogo = Catalogo::ultimo(); } catch (Exception $e) { $catalogo = null; }

$tituloCatalogo     = '';
$familiaProducto    = '';
$familiaDescripcion = '';
$imagenLateral      = '';
$itemsBase          = array(); // colección de artículos/colores según sección/familia

if ($catalogo) {
    if ($submenuactual !== '') {
        // Familia específica
        $familia = null;
        try { $familia = CatalogoSeccionFamilia::find($catalogo->id, $menuactual, $submenuactual); } catch (Exception $e) {}
        if ($familia) {
            $itemsBase          = isset($familia->articulos) ? $familia->articulos : array();
            $tituloCatalogo     = isset($familia->lineaProducto->tituloCatalogo) ? $familia->lineaProducto->tituloCatalogo : '';
            $familiaProducto    = isset($familia->familiaProducto->nombre) ? $familia->familiaProducto->nombre : '';
            $familiaDescripcion = isset($familia->descripcion) ? $familia->descripcion : '';
            $imagenLateral      = isset($familia->imagenLateral) ? $familia->imagenLateral : '';
        } else {
            // Fallback: sección completa
            $sec = null;
            try { $sec = CatalogoSeccion::find($catalogo->id, $menuactual); } catch (Exception $e) {}
            if ($sec) {
                $itemsBase      = isset($sec->articulos) ? $sec->articulos : array();
                $tituloCatalogo = isset($sec->lineaProducto->tituloCatalogo) ? $sec->lineaProducto->tituloCatalogo : '';
            }
        }
    } else {
        // Sección completa
        $sec = null;
        try { $sec = CatalogoSeccion::find($catalogo->id, $menuactual); } catch (Exception $e) {}
        if ($sec) {
            $itemsBase      = isset($sec->articulos) ? $sec->articulos : array();
            $tituloCatalogo = isset($sec->lineaProducto->tituloCatalogo) ? $sec->lineaProducto->tituloCatalogo : '';
        }
    }
}

/* ==========================================================
 * 5) Usuario / permisos (no invento clases ni endpoints)
 * ========================================================== */
$usuario = null;
$puedeFavoritos = false;
try {
    $usuario = Usuario::logueado();
    $puedeFavoritos = class_exists('FavoritoCliente'); // autorización real la decide backend
} catch (Exception $e) {
    $usuario = null;
    $puedeFavoritos = false;
}

/* ==========================================================
 * 6) Normalización mínima para la vista (si necesitás pre-procesar)
 *    Si tu Angular usa la colección "plana" desde PHP, la exponemos tal cual.
 * ========================================================== */
$articulos = is_array($itemsBase) ? $itemsBase : array();

/* ==========================================================
 * 7) Render (estructura; Angular & servicios ya vienen por includes)
 * ========================================================== */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($tituloCatalogo ?: 'Catálogo'); ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

  <!-- Contenedor principal del catálogo controlado por Angular del módulo -->
  <div class="container-catalogo" ng-controller="CatalogoCtrl">
    <!-- Encabezado -->
    <div class="encabezado">
      <h1><?php echo htmlspecialchars($tituloCatalogo ?: 'Catálogo'); ?></h1>
      <?php if ($familiaProducto): ?>
        <div class="subtitulo"><?php echo htmlspecialchars($familiaProducto); ?></div>
      <?php endif; ?>
      <?php if ($familiaDescripcion): ?>
        <div class="descripcion"><?php echo $familiaDescripcion; /* puede venir con HTML */ ?></div>
      <?php endif; ?>
    </div>

    <!-- Acciones -->
    <div style="margin:10px 0;">
      <?php if ($puedeFavoritos): ?>
        <button class="btn btn-primary" ng-click="enviarFavoritos && enviarFavoritos()">
          Enviar seleccionados a Favoritos
        </button>
      <?php endif; ?>
    </div>

    <!-- Sidebar de filtros -->
    <aside id="catalogo-filtros">
      <!-- Aquí se engancha tu UI de filtros del módulo -->
    </aside>

    <!-- Grid / Lista de artículos -->
    <section id="catalogo-grid" class="grid-articulos">
      <!-- Tu grilla/plantillas existentes del módulo -->
      <!-- Ejemplo mínimo (si tenés plantilla angular propia, mantenela): -->
      <!--
      <div class="item" ng-repeat="articulo in articulos | filter:{filtros:true}">
        <div class="top">
          <strong>{{ getName(articulo) }}</strong>
          <button type="button" ng-click="toggleFavorito(articulo)">
            <i class="star" ng-class="{'star-on': articulo.favorito, 'star-off': !articulo.favorito}"></i>
          </button>
        </div>
        <img ng-src="{{ getImageUrl(articulo) }}" alt="" />
        <div>{{ getPrecioMayorista(articulo) | currency:'$' }}</div>
      </div>
      -->
    </section>

    <!-- Pie -->
    <div style="margin:14px 0; color:#666; font-size:12px;">
      <span>Sección: <?php echo htmlspecialchars($menuactual ?: '(todas)'); ?></span>
      <?php if ($submenuactual): ?>
        <span> | Familia: <?php echo htmlspecialchars($submenuactual); ?></span>
      <?php endif; ?>
    </div>
  </div>

  <!-- Controller inline del módulo (tu lógica original) -->
  <script type="text/javascript">
  Koi.controller('CatalogoCtrl', function ($scope, $filter, ServiceCliente, ServiceCatalogo) {
      ServiceCatalogo.filtros.show = true; // muestra panel filtros (servicio existente) <?php /* ref ServiceCatalogo */ ?>

      $scope.funciones = funciones;
      $scope.imagesUrl = 'https://www.spiralshoes.com/zapatillas/jpg/';

      var orderBy = $filter('orderBy');

      $scope.articlesSort = function(sortBy, reverse) {
          $scope.articulos = orderBy($scope.articulos, sortBy, reverse);
      };

      $scope.$on('Catalogo:FiltrosAplicados:changed', function (e, filtrosAplicados) {
          angular.forEach($scope.articulos, function (item) {
              if (filtrosAplicados.tipoProductoStock.indexOf('12') >= 0) {
                  if (filtrosAplicados.tipoProductoStock.length == 1) {
                      item.filtros = item.stock > 0;
                  } else {
                      item.filtros = item.stock > 0 && filtrosAplicados.tipoProductoStock.indexOf((item.colorPorArticulo.tipoProductoStock.id + '')) >= 0;
                  }
              } else {
                  item.filtros = filtrosAplicados.tipoProductoStock.indexOf((item.colorPorArticulo.tipoProductoStock.id + '')) >= 0;
              }
          });
      });

      $scope.$on('Catalogo:SortBy:changed', function (e, sortBy) {
          var mapSort = {
              catalogo: { field: 'articulos.nombre', reverse: false },
              stock: { field: 'stock', reverse: false },
              category: { field: 'categoria', reverse: false },
              "mayor-precio-mayorista": { field: 'precioMayorista', reverse: true },
              "menor-precio-mayorista": { field: 'precioMayorista', reverse: false }
          };
          $scope.articlesSort(mapSort[sortBy].field, mapSort[sortBy].reverse);
      });

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
          var result = articulo.imagenesArticulo ? articulo.imagenesArticulo.filter(function (imagen) { return imagen.lado_imagen == 'e'; }) : [];
          if (result.length > 0) {
              return $scope.imagesUrl + result[0].ruta;
          }
          return (articulo.imagenesArticulo && articulo.imagenesArticulo.length > 0) ? $scope.imagesUrl + articulo.imagenesArticulo[0].ruta : $scope.getEmptyImageUrl();
      };

      $scope.getImageUrls = function (articulo) {
          var images = [];
          if (articulo.imagenesArticulo) {
              images = articulo.imagenesArticulo.map(function (imagen) {
                  return {
                      ruta: $scope.imagesUrl + imagen.ruta,
                      orden: imagen.orden || 0
                  };
              });
          }
          return images.length > 0 ? images : [{ruta: $scope.getEmptyImageUrl(), orden: 0}];
      };

      $scope.getEmptyImageUrl = function () { return $scope.imagesUrl + 'empty.jpg'; };
      $scope.getUnavailableImageUrl = function () { return $scope.imagesUrl + 'empty.jpg'; };

      /* Favoritos (usa ServiceCliente ya existente) */
      $scope.toggleFavorito = function (articulo) {
          var cb = function (err) { if (!err) articulo.favorito = !articulo.favorito; };
          articulo.favorito ? ServiceCliente.removeFavorito(articulo, cb) : ServiceCliente.addFavorito(articulo, cb);
      };

      $scope.toggleFavorito2 = async function (articulo) {
          var favorites = [];
          for (var i=0; i<(articulo.subArticulos||[]).length; i++) {
              var item = articulo.subArticulos[i];
              favorites.push({ idArticulo: item.idArticulo, idColorPorArticulo: item.idColorPorArticulo });
          }

          if (articulo.favorito) {
              var res1 = await ServiceCliente.removeFavoritoBatch(favorites);
              if (res1 && res1.status == 200) {
                  $scope.$apply(function(){ articulo.favorito = false; });
              }
          } else {
              var res2 = await ServiceCliente.addFavoritoBatch(favorites);
              if (res2 && res2.status == 200) {
                  $scope.$apply(function(){ articulo.favorito = true; });
              }
          }
      };

      $scope.getIdNameModal3d = function (articulo) { return '#modal' + articulo.idArticulo + articulo.idColorPorArticulo; };
      $scope.getNameModal3d = function (articulo) { return 'modal' + articulo.idArticulo + articulo.idColorPorArticulo; };
      $scope.getArticuloCodigoColor = function (articulo) { return articulo.idArticulo + '-' + articulo.idColorPorArticulo; };
      $scope.getArticuloRutaIframe3D = function (articulo) { return articulo.rutaIframe3D; };
      $scope.getIdStarArticulo = function (articulo) { return "star-" + articulo.idArticulo + '-' + articulo.idColorPorArticulo; };

      // Carga inicial desde PHP (colección plana que ya usa tu módulo)
      $scope.articulos = <?php echo json_encode($articulos); ?>;
  });
  </script>

  <?php include('content/cliente/ordenamientosSelecciones.php'); ?>

</body>
</html>
