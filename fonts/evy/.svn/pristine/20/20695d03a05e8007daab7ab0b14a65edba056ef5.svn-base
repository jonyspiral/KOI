<?php

require_once('../../premaster.php');

class TestCatalogoSeccionFamiliaArticulos extends TestCrud {

    private $original = array(
        array('idLineaProducto' => 1, 'orden' => 2, 'familias' => array(
            array('id' => 1, 'orden' => 2, 'descripcion' => 'Descripcion de la familia 1', 'imagenLateral' => 'http://google.com/img1', 'articulos' => array(
                array('id' => '515', 'idColor' => 'NARA', 'orden' => 3),
                array('id' => '516', 'idColor' => 'ENE', 'orden' => 2),
                array('id' => '517', 'idColor' => 'GA', 'orden' => 1),
            )),
            array('id' => 3, 'orden' => 1, 'descripcion' => 'Descripcion de la familia 3', 'imagenLateral' => 'http://google.com/img3', 'articulos' => array(
                array('id' => '518', 'idColor' => 'N', 'orden' => 1)
            ))
        )),
        array('idLineaProducto' => 2, 'orden' => 1, 'familias' => array(
            array('id' => 6, 'orden' => 1, 'descripcion' => 'Descripcion de la otra linea', 'imagenLateral' => 'http://google.com/imgX')
        ))
    );

    protected function _create() {

        $catalogo = Catalogo::find();
        $catalogo->nombre = 'Summer 2018';
        $catalogo->descripcion = 'Descripción del catálogo 2018';

        $catalogo->guardar();

        foreach ($this->original as $seccion) {
            $aux = CatalogoSeccion::find();
            $aux->catalogo = $catalogo;
            $aux->lineaProducto = Factory::getInstance()->getLineaProducto($seccion['idLineaProducto']);
            $aux->orden = $seccion['orden'];

            $aux->guardar();

            foreach ($seccion['familias'] as $familia) {
                $aux2 = CatalogoSeccionFamilia::find();
                $aux2->catalogo = $aux->catalogo;
                $aux2->lineaProducto = $aux->lineaProducto;
                $aux2->familiaProducto = FamiliaProducto::find($familia['id']);
                $aux2->orden = $familia['orden'];
                $aux2->descripcion = $familia['descripcion'];
                $aux2->imagenLateral = $familia['imagenLateral'];

                $aux2->guardar();

                foreach ($familia['articulos'] as $articulo) {
                    $aux3 = CatalogoSeccionFamiliaArticulo::find();
                    $aux3->catalogo = $aux2->catalogo;
                    $aux3->lineaProducto = $aux2->lineaProducto;
                    $aux3->familiaProducto = $aux2->familiaProducto;
                    $aux3->articulo = Factory::getInstance()->getArticulo($articulo['id']);
                    $aux3->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($articulo['id'], $articulo['idColor']);
                    $aux3->orden = $articulo['orden'];

                    $aux3->guardar();
                }
            }
        }

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);

        return $catalogo2;
    }

    private function _validateFamilias($catalogo, $configSecciones) {

        foreach ($catalogo->secciones as $seccion) {
            foreach ($configSecciones as $configSeccion) {
                if ($seccion->idLineaProducto == $configSeccion['idLineaProducto']) {
                    foreach ($seccion->familias as $familia) {
                        foreach ($configSeccion['familias'] as $configFamilia) {
                            if ($familia->idFamiliaProducto == $configFamilia['id']) {

                                // Test cantidad
                                $this->_assert(count($familia->articulos) == count($configFamilia['articulos']), 'Cantidad de articulos');

                                // Test datos
                                $datosIncorrectos = array();
                                foreach ($familia->articulos as $articulo) {
                                    $found = false;
                                    foreach ($configFamilia['articulos'] as $config) {
                                        if ($articulo->idArticulo == $config['id']
                                            && $articulo->idColorPorArticulo == $config['idColor']
                                            && $articulo->orden == $config['orden']) {
                                            $found = true;
                                        }
                                    }
                                    if (!$found) {
                                        $datosIncorrectos[] = $articulo;
                                    }
                                }
                                $this->_assert(!count($datosIncorrectos), 'Datos', json_encode($datosIncorrectos));

                                // Test orden
                                $last = 0;
                                $ordenCorrecto = true;
                                foreach ($familia->articulos as $articulo) {
                                    $articulo->orden <= $last && $ordenCorrecto = false;
                                }
                                $this->_assert($ordenCorrecto, 'Orden');
                            }
                        }
                    }
                }
            }
        }
    }

    protected function createNewCatalogoConSecciones() {

        $catalogo = $this->_create();

        $this->_validateFamilias($catalogo, $this->original);
    }

    protected function updateCatalogoConSecciones() {

        $catalogo = $this->_create();
        $catalogo->nombre = 'Winter 2018';

        $catalogo->guardar();

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);

        $this->_assert($catalogo2->nombre == 'Winter 2018', 'Nombre');

        $this->_validateFamilias($catalogo2, $this->original);
    }

    protected function updateArticulosDeUnaFamiliaDeUnaSeccionDeUnCatalogo() {

        $catalogo = $this->_create();

        $nuevasFamilias = array(
            array('idLineaProducto' => 1, 'orden' => 2, 'familias' => array(
                array('id' => 1, 'orden' => 2, 'descripcion' => 'Descripcion de la familia 1', 'imagenLateral' => 'http://google.com/img1', 'articulos' => array(
                    array('id' => '515', 'idColor' => 'NARA', 'orden' => 1),
                    array('id' => '517', 'idColor' => 'PEN', 'orden' => 2),
                )),
                array('id' => 3, 'orden' => 1, 'descripcion' => 'Descripcion de la familia 3', 'imagenLateral' => 'http://google.com/img3', 'articulos' => array(
                    array('id' => '518', 'idColor' => 'N', 'orden' => 1)
                ))
            )),
            array('idLineaProducto' => 2, 'orden' => 1, 'familias' => array(
                array('id' => 6, 'orden' => 1, 'descripcion' => 'Descripcion de la otra linea', 'imagenLateral' => 'http://google.com/imgX')
            ))
        );

        foreach ($catalogo->secciones as $seccion) {
            if ($seccion->idLineaProducto == 1) {
                foreach ($seccion->familias as $familia) {
                    if ($familia->idFamiliaProducto == 1) {
                        foreach ($familia->articulos as $articulo) {
                            if ($articulo->idArticulo == '516') {
                                $articulo->borrar();
                            }
                            if ($articulo->idArticulo == '517') {
                                $articulo->borrar();
                            }
                            if ($articulo->idArticulo == '515') {
                                $articulo->orden = 1;
                                $articulo->guardar();
                            }
                        }
                        $aux = CatalogoSeccionFamiliaArticulo::find();
                        $aux->catalogo = $familia->catalogo;
                        $aux->lineaProducto = $familia->lineaProducto;
                        $aux->familiaProducto = $familia->familiaProducto;
                        $aux->articulo = Factory::getInstance()->getArticulo('517');
                        $aux->colorPorArticulo = Factory::getInstance()->getColorPorArticulo('517', 'PEN');
                        $aux->orden = 2;
                        $aux->guardar();
                    }
                }
            }
        }

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);

        $this->_validateFamilias($catalogo2, $nuevasFamilias);
    }

    protected function deleteArticulosDeUnaSeccionDeUnCatalogoConFamilias() {

        $catalogo = $this->_create();

        $nuevasFamilias = array(
            array('idLineaProducto' => 1, 'orden' => 2, 'familias' => array(
                array('id' => 1, 'orden' => 2, 'descripcion' => 'Descripcion de la familia 1', 'imagenLateral' => 'http://google.com/img1', 'articulos' => array(
                    array('id' => '515', 'idColor' => 'NARA', 'orden' => 3),
                    array('id' => '516', 'idColor' => 'ENE', 'orden' => 2),
                    array('id' => '517', 'idColor' => 'GA', 'orden' => 1),
                ))
            ))
        );

        foreach ($catalogo->secciones as $seccion) {
            if ($seccion->idLineaProducto == 1) {
                foreach ($seccion->familias as $familia) {
                    if ($familia->idFamiliaProducto == 3) {
                        $familia->borrar();
                    }
                }
            }
            if ($seccion->idLineaProducto == 2) {
                $seccion->borrar();
            }
        }

        // Acá valido que se haya eliminado bien el artículo de la familia 3 de la linea 1 (que borré entera). Con esto testeo el cascade-delete
        $items = Base::getListObject('CatalogoSeccionFamiliaArticulo', 'cod_catalogo = ' . Datos::objectToDB($catalogo->id) . ' AND cod_linea_producto = 1 AND cod_familia_producto = 3');
        $this->_assert(!count($items), 'Delete en cascada de Familia => Articulos');


        // Acá valido que se hayan eliminado bien los artículos de las familias de la linea 2 (que borré entera). Con esto testeo el cascade-delete
        $items = Base::getListObject('CatalogoSeccionFamiliaArticulo', 'cod_catalogo = ' . Datos::objectToDB($catalogo->id) . ' AND cod_linea_producto = 2');
        $this->_assert(!count($items), 'Delete en cascada de Seccion => Familias => Articulos');

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);

        $this->_validateFamilias($catalogo2, $nuevasFamilias);

        $this->_assert(count($catalogo2->secciones) == 1, 'Cantidad de secciones');

        $this->_assert(count($catalogo2->secciones[0]->familias) == 1, 'Cantidad de familias');
    }
}

$test = new TestCatalogoSeccionFamiliaArticulos();
$test->run();
