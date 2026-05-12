<?php

require_once('../../premaster.php');

class TestCatalogoCrud extends TestCrud {

    private $original = array(
        'nombre' => 'Summer 2017',
        'descripcion' => 'Descripción del catálogo 2017'
    );

    private function _create() {

        $catalogo = Catalogo::find();
        $catalogo->nombre = $this->original['nombre'];
        $catalogo->descripcion = $this->original['descripcion'];
        $catalogo->guardar();

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);

        return $catalogo2;
    }

    protected function createNewCatalogo() {

        $catalogo = $this->_create();

        $this->_assert($catalogo->nombre == $this->original['nombre'], 'Nombre');
        $this->_assert($catalogo->descripcion == $this->original['descripcion'], 'Descripción');
    }

    protected function updateCatalogo() {

        $catalogo = $this->_create();

        $catalogoInfo = array(
            'nombre' => 'Summer 2018',
            'descripcion' => 'Descripción del catálogo 2018'
        );
        $catalogo->nombre = $catalogoInfo['nombre'];
        $catalogo->descripcion = $catalogoInfo['descripcion'];
        $catalogo->guardar();

        $idCatalogo2 = $catalogo->id;
        $catalogo2 = Catalogo::find($idCatalogo2);
        $this->_assert($catalogo2->nombre == $catalogoInfo['nombre'], 'Nombre');
        $this->_assert($catalogo2->descripcion == $catalogoInfo['descripcion'], 'Descripción');
    }

    protected function deleteCatalogo() {

        $catalogo = $this->_create();
        $idCatalogo = $catalogo->id;
        $catalogo->borrar();

        $catalogo2 = Catalogo::find($idCatalogo);
        $this->_assert($catalogo2->anulado(), 'Delete');
    }
}

$test = new TestCatalogoCrud();
$test->run();
