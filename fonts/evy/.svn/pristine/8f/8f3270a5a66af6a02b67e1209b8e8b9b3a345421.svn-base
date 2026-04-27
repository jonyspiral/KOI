<?php

require_once('../../premaster.php');

class TestCatalogoSecciones extends TestCrud {

    private $original = array(
        array('idLineaProducto' => 1, 'orden' => 3),
        array('idLineaProducto' => 2, 'orden' => 1),
        array('idLineaProducto' => 3, 'orden' => 2)
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
        }

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);

        return $catalogo2;
    }

    private function _validateSecciones($catalogo, $configSecciones) {

        // Test cantidad
        $this->_assert(count($catalogo->secciones) == count($configSecciones), 'Cantidad de secciones');

        // Test datos
        $datosIncorrectos = array();
        foreach ($catalogo->secciones as $seccion) {
            $found = false;
            foreach ($configSecciones as $configSeccion) {
                if ($seccion->idLineaProducto == $configSeccion['idLineaProducto'] && $seccion->orden == $configSeccion['orden']) {
                    $found = true;
                }
            }
            if (!$found) {
                $datosIncorrectos[] = $seccion;
            }
        }
        $this->_assert(!count($datosIncorrectos), 'Datos', json_encode($datosIncorrectos));

        // Test orden
        $last = 0;
        $ordenCorrecto = true;
        foreach ($catalogo->secciones as $seccion) {
            $seccion->orden <= $last && $ordenCorrecto = false;
        }
        $this->_assert($ordenCorrecto, 'Orden');
    }

    protected function createNewCatalogoConSecciones() {

        $catalogo = $this->_create();

        $this->_validateSecciones($catalogo, $this->original);
    }

    protected function updateCatalogoConSecciones() {

        $catalogo = $this->_create();
        $catalogo->nombre = 'Winter 2018';

        $catalogo->guardar();

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);

        $this->_assert($catalogo2->nombre == 'Winter 2018', 'Nombre');

        $this->_validateSecciones($catalogo2, $this->original);
    }

    protected function updateSeccionesDeUnCatalogo() {

        $catalogo = $this->_create();

        foreach ($catalogo->secciones as $seccion) {
            $seccion->borrar();
        }

        $nuevasSecciones = array(
            array('idLineaProducto' => 5, 'orden' => 1),
            array('idLineaProducto' => 3, 'orden' => 2)
        );

        foreach ($nuevasSecciones as $seccion) {
            $aux = CatalogoSeccion::find();
            $aux->catalogo = $catalogo;
            $aux->lineaProducto = Factory::getInstance()->getLineaProducto($seccion['idLineaProducto']);
            $aux->orden = $seccion['orden'];

            $aux->guardar();
        }

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);

        $this->_validateSecciones($catalogo2, $nuevasSecciones);
    }

    protected function deleteSeccionesDeUnCatalogo() {

        $catalogo = $this->_create();

        foreach ($catalogo->secciones as $seccion) {
            $seccion->borrar();
        }

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);

        $this->_validateSecciones($catalogo2, array());
    }
}

$test = new TestCatalogoSecciones();
$test->run();
