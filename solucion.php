<?php

require_once('premaster.php');

function crearContacto($cliente) {
    /** @var Cliente $cliente */

    try {
        $contacto = Factory::getInstance()->getContacto();
        $contacto->nombre = $cliente->razonSocial;
        $contacto->apellido = $cliente->nombre;
        $contacto->tipo = 'C';
        $contacto->cliente = $cliente;
        $contacto->sucursal = $cliente->sucursalCentral;
        $contacto->telefono1 = $cliente->telefono1;
        $contacto->interno1 = $cliente->interno1;
        $contacto->email1 = $cliente->email;
        $contacto->direccionCalle = $cliente->direccionCalle;
        $contacto->direccionNumero = $cliente->direccionNumero;
        $contacto->direccionPiso = $cliente->direccionPiso;
        $contacto->direccionDepartamento = $cliente->direccionDepartamento;
        $contacto->direccionCodigoPostal = $cliente->direccionCodigoPostal;
        $contacto->direccionPais = $cliente->direccionPais;
        $contacto->direccionProvincia = $cliente->direccionProvincia;
        $contacto->direccionLocalidad = $cliente->direccionLocalidad;

        $contacto->guardar();

        return $contacto;
    } catch (Exception $ex) {
        Logger::addError('El error es creando contacto');
        throw $ex;
    }
}

function crearUsuario($cliente, $contacto) {
    /** @var Cliente $cliente */
    /** @var Contacto $contacto */

    try {
        $usuario = Factory::getInstance()->getUsuarioLogin();
        $usuario->id = $cliente->cuit;
        $usuario->password = Funciones::toSHA1('spiralshoes');
        $usuario->idContacto = $contacto->id;
        $usuario->tipoPersona = $contacto->tipo;

        $rol = Factory::getInstance()->getRolPorUsuario();
        $rol->id = 14;
        $rol->idUsuario = $cliente->cuit;
        $usuario->roles = array($rol);

        Factory::getInstance()->persistir($usuario);

        return $usuario;
    } catch (Exception $ex) {
        Logger::addError('El error es creando usuario');
        throw $ex;
    }
}

function crearUsuarios() {

    $usuariosCreados = 0;

    $clientes = Factory::getInstance()->getListObject('Cliente', 'anulado = ' . Datos::objectToDB('N'));

    /** @var Cliente $cliente */
    foreach ($clientes as $cliente) {
        try {

            // Verificamos si el cuit es válido
            if (strpos($cliente->cuit, '-') === 0 || strpos($cliente->cuit, ' ') !== false) {
                continue;
            }

            // Verificamos que no tenga un usuario creado
            $usuarios = Factory::getInstance()->getListObject('Usuario', 'anulado = ' . Datos::objectToDB('N') . ' AND cod_usuario = ' . Datos::objectToDB($cliente->cuit));
            if (count($usuarios)) {
                continue;
            }

            // Buscamos si ya tiene un contacto, así lo reutilizamos
            $contacto = null;
            $contactos = Factory::getInstance()->getListObject('Contacto', 'anulado = ' . Datos::objectToDB('N') . ' AND cod_cliente = ' . Datos::objectToDB($cliente->id));
            if (count($contactos)) {
                $contacto = $contactos[0];
            }

            // Si no tenía contacto, lo creamos
            if (!$contacto) {
                $contacto = crearContacto($cliente);
            }

            // Creamos el usuario
            /** @var Usuario $usuario */
            $usuario = crearUsuario($cliente, $contacto);

            Logger::addInfo('Se creo el usuario ' . $cliente->cuit . ' del cliente ' . $cliente->nombre);
            $usuariosCreados++;
        } catch (Exception $ex) {
            Logger::addError('Ocurrio un error guardando el usuario ' . $cliente->cuit . ' del cliente ' . $cliente->nombre . ': ' . $ex->getMessage());
        }
    }
}

//crearUsuarios();


function create() {

    $original = array(
        array('idLineaProducto' => 1, 'orden' => 3, 'familias' => array(
            array('id' => 4, 'orden' => 2, 'descripcion' => 'Las Eighties evocan los años 80 y su descripcion tiene que ser copada y larga', 'imagenLateral' => 'http://google.com/img1', 'articulos' => array(
                array('id' => '586', 'idColor' => 'AZR', 'orden' => 3),
                array('id' => '586', 'idColor' => 'BOC', 'orden' => 2),
                array('id' => '586', 'idColor' => 'MAV', 'orden' => 1),
                array('id' => '586', 'idColor' => 'N', 'orden' => 4),
                array('id' => '586', 'idColor' => 'NG', 'orden' => 5),
                array('id' => '586', 'idColor' => 'NLI', 'orden' => 6),
                array('id' => '586', 'idColor' => 'NPU', 'orden' => 7),
                array('id' => '586', 'idColor' => 'RB', 'orden' => 9),
                array('id' => '586', 'idColor' => 'VMG', 'orden' => 8)
            )),
            array('id' => 3, 'orden' => 1, 'descripcion' => 'Las Classics evocan los años 90 y su descripcion tiene que ser copada y larga', 'imagenLateral' => 'http://google.com/img3', 'articulos' => array(
                array('id' => '589', 'idColor' => 'GN', 'orden' => 1),
                array('id' => '589', 'idColor' => 'NG', 'orden' => 2),
                array('id' => '589', 'idColor' => 'NLI', 'orden' => 3),
                array('id' => '589', 'idColor' => 'R', 'orden' => 4),
                array('id' => '590', 'idColor' => 'AZNA', 'orden' => 5),
                array('id' => '590', 'idColor' => 'BA', 'orden' => 6),
                array('id' => '590', 'idColor' => 'BXG', 'orden' => 7),
                array('id' => '590', 'idColor' => 'GR', 'orden' => 8),
                array('id' => '590', 'idColor' => 'N', 'orden' => 9),
                array('id' => '590', 'idColor' => 'NB', 'orden' => 10),
                array('id' => '590', 'idColor' => 'R', 'orden' => 12),
                array('id' => '591', 'idColor' => 'BV', 'orden' => 11)
            ))
        )),
        array('idLineaProducto' => 2, 'orden' => 2, 'familias' => array(
            array('id' => 1, 'orden' => 2, 'descripcion' => 'Avril para women surgió hace 10 años y la rompe toda. Esta descripción tiene que ser más o menos larga', 'imagenLateral' => 'http://google.com/imgX', 'articulos' => array(
                array('id' => '826', 'idColor' => 'AZ', 'orden' => 1),
                array('id' => '826', 'idColor' => 'B', 'orden' => 3),
                array('id' => '826', 'idColor' => 'COR', 'orden' => 5),
                array('id' => '826', 'idColor' => 'ESM', 'orden' => 2),
                array('id' => '826', 'idColor' => 'N', 'orden' => 4),
                array('id' => '896', 'idColor' => 'ARE', 'orden' => 6),
                array('id' => '896', 'idColor' => 'CER', 'orden' => 7),
                array('id' => '896', 'idColor' => 'N', 'orden' => 8),
                array('id' => '896', 'idColor' => 'VMI', 'orden' => 9),
                array('id' => '897', 'idColor' => 'B', 'orden' => 10),
                array('id' => '897', 'idColor' => 'N', 'orden' => 11),
                array('id' => '897', 'idColor' => 'PET', 'orden' => 12)
            )),
            array('id' => 5, 'orden' => 1, 'descripcion' => 'Koi para women surgió hace 10 años y la rompe toda. Esta descripción tiene que ser más o menos larga', 'imagenLateral' => 'http://google.com/imgX', 'articulos' => array(
                array('id' => '568', 'idColor' => 'CER', 'orden' => 2),
                array('id' => '569', 'idColor' => 'CAM', 'orden' => 4),
                array('id' => '569', 'idColor' => 'N', 'orden' => 1),
                array('id' => '745', 'idColor' => 'ESM', 'orden' => 3),
                array('id' => '745', 'idColor' => 'RS', 'orden' => 5),
                array('id' => '745', 'idColor' => 'VMI', 'orden' => 6),
                array('id' => '781', 'idColor' => 'AZNA', 'orden' => 7)
            )),
            array('id' => 9, 'orden' => 3, 'descripcion' => 'RV para women surgió hace 10 años y la rompe toda. Esta descripción tiene que ser más o menos larga', 'imagenLateral' => 'http://google.com/imgX', 'articulos' => array(
                array('id' => '918', 'idColor' => 'NJ', 'orden' => 1),
                array('id' => '918', 'idColor' => 'SE', 'orden' => 3),
                array('id' => '919', 'idColor' => 'FC', 'orden' => 5),
                array('id' => '919', 'idColor' => 'FER', 'orden' => 2),
                array('id' => '919', 'idColor' => 'FES', 'orden' => 4),
                array('id' => '919', 'idColor' => 'PET', 'orden' => 6),
                array('id' => '943', 'idColor' => 'SE', 'orden' => 7),
                array('id' => '944', 'idColor' => 'B', 'orden' => 8),
                array('id' => '944', 'idColor' => 'N', 'orden' => 9),
                array('id' => '978', 'idColor' => 'AZ', 'orden' => 10)
            ))
        )),
        array('idLineaProducto' => 3, 'orden' => 1, 'familias' => array(
            array('id' => 5, 'orden' => 2, 'descripcion' => 'El proyecto KOI surgió hace 10 años y la rompe toda. Esta descripción tiene que ser más o menos larga', 'imagenLateral' => 'http://google.com/imgX', 'articulos' => array(
                array('id' => '824', 'idColor' => 'B', 'orden' => 1),
                array('id' => '824', 'idColor' => 'N', 'orden' => 2),
                array('id' => '895', 'idColor' => 'ARE', 'orden' => 3),
                array('id' => '568', 'idColor' => 'CER', 'orden' => 9),
                array('id' => '569', 'idColor' => 'CAM', 'orden' => 5),
                array('id' => '569', 'idColor' => 'N', 'orden' => 10),
                array('id' => '745', 'idColor' => 'ESM', 'orden' => 4),
                array('id' => '745', 'idColor' => 'RS', 'orden' => 6),
                array('id' => '745', 'idColor' => 'VMI', 'orden' => 7),
                array('id' => '781', 'idColor' => 'AZNA', 'orden' => 8)
            )),
            array('id' => 6, 'orden' => 1, 'descripcion' => 'El proyecto MAX surgió hace 10 años y la rompe toda. Esta descripción tiene que ser más o menos larga', 'imagenLateral' => 'http://google.com/imgX', 'articulos' => array(
                array('id' => '516', 'idColor' => 'AMA', 'orden' => 1),
                array('id' => '516', 'idColor' => 'AN', 'orden' => 3),
                array('id' => '516', 'idColor' => 'AZNA', 'orden' => 5),
                array('id' => '516', 'idColor' => 'ENE', 'orden' => 2),
                array('id' => '516', 'idColor' => 'G', 'orden' => 4),
                array('id' => '516', 'idColor' => 'GBX', 'orden' => 6),
                array('id' => '516', 'idColor' => 'GCA', 'orden' => 7),
                array('id' => '516', 'idColor' => 'NG', 'orden' => 8),
                array('id' => '517', 'idColor' => 'AZNA', 'orden' => 9),
                array('id' => '517', 'idColor' => 'BOR', 'orden' => 10),
                array('id' => '517', 'idColor' => 'GA', 'orden' => 11),
                array('id' => '517', 'idColor' => 'GAQ', 'orden' => 12),
                array('id' => '521', 'idColor' => 'BOR', 'orden' => 13),
                array('id' => '521', 'idColor' => 'GA', 'orden' => 14),
                array('id' => '521', 'idColor' => 'GES', 'orden' => 15),
                array('id' => '521', 'idColor' => 'MOS', 'orden' => 16),
                array('id' => '522', 'idColor' => 'AZM', 'orden' => 17),
                array('id' => '522', 'idColor' => 'N', 'orden' => 18),
                array('id' => '522', 'idColor' => 'NG', 'orden' => 19),
                array('id' => '540', 'idColor' => 'V', 'orden' => 20)
            )),
            array('id' => 8, 'orden' => 3, 'descripcion' => 'El proyecto POW surgió hace 10 años y la rompe toda. Esta descripción tiene que ser más o menos larga', 'imagenLateral' => 'http://google.com/imgX', 'articulos' => array(
                array('id' => '514', 'idColor' => 'AN', 'orden' => 12),
                array('id' => '514', 'idColor' => 'BN', 'orden' => 11),
                array('id' => '514', 'idColor' => 'BOR', 'orden' => 10),
                array('id' => '514', 'idColor' => 'GA', 'orden' => 9),
                array('id' => '514', 'idColor' => 'GES', 'orden' => 8),
                array('id' => '514', 'idColor' => 'N', 'orden' => 7),
                array('id' => '514', 'idColor' => 'NG', 'orden' => 6),
                array('id' => '514', 'idColor' => 'R', 'orden' => 5),
                array('id' => '514', 'idColor' => 'VIG', 'orden' => 4),
                array('id' => '514', 'idColor' => 'VMI', 'orden' => 3),
                array('id' => '559', 'idColor' => 'NG', 'orden' => 2),
                array('id' => '560', 'idColor' => 'NG', 'orden' => 1)
            ))
        ))
    );

    $catalogo = Catalogo::find();
    $catalogo->nombre = 'Summer 2018';
    $catalogo->descripcion = 'Descripción del catálogo 2018';

    $catalogo->guardar();

    foreach ($original as $seccion) {
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

//$catalogo = create();

