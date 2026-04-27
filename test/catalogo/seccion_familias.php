<?php

require_once('../../premaster.php');

class TestCatalogoSeccionFamilias extends TestCrud {

    private $original = array(
        array('idLineaProducto' => 1, 'orden' => 2, 'familias' => array(
            array('id' => 1, 'orden' => 3, 'descripcion' => 'Descripcion de la familia 1', 'imagenLateral' => 'http://google.com/img1'),
            array('id' => 2, 'orden' => 1, 'descripcion' => 'Descripcion de la familia 2', 'imagenLateral' => 'http://google.com/img2'),
            array('id' => 3, 'orden' => 2, 'descripcion' => 'Descripcion de la familia 3', 'imagenLateral' => 'http://google.com/img3')
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

                    // Test cantidad
                    $this->_assert(count($seccion->familias) == count($configSeccion['familias']), 'Cantidad de familias');

                    // Test datos
                    $datosIncorrectos = array();
                    foreach ($seccion->familias as $familia) {
                        $found = false;
                        foreach ($configSeccion['familias'] as $config) {
                            if ($familia->idFamiliaProducto == $config['id']
                                && $familia->orden == $config['orden']
                                && $familia->descripcion == $config['descripcion']
                                && $familia->imagenLateral == $config['imagenLateral']) {
                                $found = true;
                            }
                        }
                        if (!$found) {
                            $datosIncorrectos[] = $familia;
                        }
                    }
                    $this->_assert(!count($datosIncorrectos), 'Datos', json_encode($datosIncorrectos));

                    // Test orden
                    $last = 0;
                    $ordenCorrecto = true;
                    foreach ($seccion->familias as $familia) {
                        $familia->orden <= $last && $ordenCorrecto = false;
                    }
                    $this->_assert($ordenCorrecto, 'Orden');
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

    protected function updateFamiliasDeUnaSeccionDeUnCatalogo() {

        $catalogo = $this->_create();

        $nuevasFamilias = array(
            array('idLineaProducto' => 1, 'orden' => 2, 'familias' => array(
                array('id' => 1, 'orden' => 1, 'descripcion' => 'Descripcion de la familia 1', 'imagenLateral' => 'http://google.com/img1'),
                array('id' => 3, 'orden' => 2, 'descripcion' => 'Descripcion de la familia 3', 'imagenLateral' => 'http://google.com/img3')
            )),
            array('idLineaProducto' => 2, 'orden' => 1, 'familias' => array(
                array('id' => 6, 'orden' => 1, 'descripcion' => 'Descripcion de la otra linea', 'imagenLateral' => 'http://google.com/imgX')
            ))
        );

        foreach ($catalogo->secciones as $seccion) {
            if ($seccion->idLineaProducto == 1) {
                foreach ($seccion->familias as $familia) {
                    if ($familia->idFamiliaProducto == 2) {
                        $familia->borrar();
                    }
                    if ($familia->idFamiliaProducto == 1) {
                        $familia->orden = 1;
                        $familia->guardar();
                    }
                    if ($familia->idFamiliaProducto == 3) {
                        $familia->orden = 2;
                        $familia->guardar();
                    }
                }
            }
        }

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);

        $this->_validateFamilias($catalogo2, $nuevasFamilias);
    }

    protected function deleteSeccionesDeUnCatalogoConFamilias() {

        $catalogo = $this->_create();

        $nuevasFamilias = array(
            array('idLineaProducto' => 1, 'orden' => 2, 'familias' => array(
                array('id' => 1, 'orden' => 3, 'descripcion' => 'Descripcion de la familia 1', 'imagenLateral' => 'http://google.com/img1'),
                array('id' => 2, 'orden' => 1, 'descripcion' => 'Descripcion de la familia 2', 'imagenLateral' => 'http://google.com/img2'),
                array('id' => 3, 'orden' => 2, 'descripcion' => 'Descripcion de la familia 3', 'imagenLateral' => 'http://google.com/img3')
            ))
        );

        foreach ($catalogo->secciones as $seccion) {
            if ($seccion->idLineaProducto == 2) {
                $seccion->borrar();
            }
        }

        // Acá valido que se hayan eliminado bien las familias de la linea 2 (que borré entera). Con esto testeo el cascade-delete
        $items = Base::getListObject('CatalogoSeccionFamilia', 'cod_catalogo = ' . Datos::objectToDB($catalogo->id) . ' AND cod_linea_producto = 2');
        $this->_assert(!count($items), 'Delete en cascada de LineaProducto => Familia');

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);

        $this->_validateFamilias($catalogo2, $nuevasFamilias);

        $this->_assert(count($catalogo2->secciones) == 1, 'Cantidad de secciones');
    }
}

$test = new TestCatalogoSeccionFamilias();
$test->run();
