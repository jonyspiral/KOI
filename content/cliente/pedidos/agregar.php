<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('cliente/pedidos/agregar/')) { ?>
<?php

$idSucursal = Funciones::post('idSucursal');
$idAlmacen = '01';
$idTemporada = 9;


try {

    // Primero preparo el PedidoCliente

	$pedidoCliente = PedidoCliente::find();
    $pedidoCliente->cliente = Usuario::logueado()->cliente;
    $pedidoCliente->sucursal = Factory::getInstance()->getSucursal(Usuario::logueado()->cliente->id, $idSucursal);
    $pedidoCliente->estado = PedidoCliente::ESTADO_PENDIENTE;

    $favoritos = Base::getListObject('FavoritoCliente', 'cod_cliente = ' . Datos::objectToDB(Usuario::logueado()->cliente->id));

    $detalle = array();
    $nroItem = 1;
    foreach ($favoritos as $favorito) {
        /** @var FavoritoCliente $favorito */
        $arrCantidades = array();

        if (count($favorito->curvas)) {
			// Seteamos las cantidades del articulo segun las curvas
            foreach ($favorito->curvas as $idCurva => $cantCurvas) {
                $curva = Factory::getInstance()->getCurva($idCurva);
                $i = 1;
                foreach ($curva->cantidad as $cantidad) {
                    if (!array_key_exists($i, $arrCantidades)) {
                        $arrCantidades[$i] = 0;
                    }
                    $arrCantidades[$i] += Funciones::toInt($cantidad) * Funciones::toInt($cantCurvas);
                    $i++;
                }
            }
        } else {
        	// Las cantidades son libres
            $i = 1;
            foreach ($favorito->cantidades as $cantidad){
                if (!array_key_exists($i, $arrCantidades)) {
                    $arrCantidades[$i] = 0;
                }
                $arrCantidades[$i] += Funciones::toInt($cantidad);
                $i++;
            }
        }

        if (Funciones::sumaArray($arrCantidades) <= 0) {
            continue;
        }

        $pedidoClienteItem = PedidoClienteItem::find();
        $pedidoClienteItem->numeroDeItem = $nroItem;
        $pedidoClienteItem->articulo = $favorito->articulo;
        $pedidoClienteItem->colorPorArticulo = $favorito->colorPorArticulo;

        // Calculo descuentos por tipo de producto stock
        $descuentoItem = Funciones::toFloat($favorito->colorPorArticulo->tipoProductoStock->descuentoPorc);
        $importeConDescuentos = $favorito->colorPorArticulo->getPrecioSegunCliente($pedidoCliente->cliente) * ((100 - $descuentoItem) / 100);

        $pedidoClienteItem->precioUnitario = $importeConDescuentos;
        for ($i = 1; $i <= 10; $i++) {
            $pedidoClienteItem->cantidades[$i] = Funciones::toInt(Funciones::keyIsSet($arrCantidades, $i, 0));
        }
        $pedidoCliente->addItem($pedidoClienteItem);
        $nroItem++;
    }

	$pedidoCliente->calcularTotal();
    if ($pedidoCliente->importeTotal <= 0) {
        throw new FactoryExceptionCustomException('No se puede cargar un pedido vac�o');
    }


    // Genero el Pedido

    $notaDePedido = Factory::getInstance()->getPedido();
    $notaDePedido->empresa = 1;
    $notaDePedido->cliente = $pedidoCliente->cliente;
    $notaDePedido->sucursal = $pedidoCliente->sucursal;
    $notaDePedido->idAlmacen = $idAlmacen;
    $notaDePedido->vendedor = Factory::getInstance()->getVendedor(Usuario::logueado()->contacto->cliente->vendedor->id);
    $notaDePedido->temporada = Factory::getInstance()->getTemporada($idTemporada);
    $notaDePedido->usuario = Usuario::logueado();
    $notaDePedido->precioAlFacturar = 'S';
    $notaDePedido->aprobado = 'N';

    foreach ($pedidoCliente->detalle as $item) {
        $notaDePedidoItem = Factory::getInstance()->getPedidoItem();
        $notaDePedidoItem->empresa = $notaDePedido->empresa;
        $notaDePedidoItem->idAlmacen = $notaDePedido->idAlmacen;
        $notaDePedidoItem->idArticulo = $item->idArticulo;
        $notaDePedidoItem->idColorPorArticulo = $item->idColorPorArticulo;
        $notaDePedidoItem->numeroDeItem = $item->numeroDeItem;
        $notaDePedidoItem->precioUnitario = $item->precioUnitario;
        $notaDePedidoItem->cantidad = $item->cantidades;
        $notaDePedido->addItem($notaDePedidoItem);
    }

    $notaDePedido->calcularTotal();


    // Guardamos to_do en transacci�n

    Factory::getInstance()->beginTransaction();

    $notaDePedido->guardar()->notificar('comercial/pedidos/nota_de_pedido/agregar/'); // 1. Guardamos la nota de pedido
    $pedidoCliente->pedido = $notaDePedido;
	$pedidoCliente->guardar()->notificar('cliente/pedidos/agregar/'); // 2. Guardamos el PedidoCliente con el n�mero del pedido asociado

    // 3. Limpiamos las curvas de los favoritos (COMENTADO, ahora se quiere borrar la selecci�n de favoritos incluso)
    /*foreach ($favoritos as $favorito) { // 3. Limpiamos los favoritos
        $favorito->curvas = array();
        for ($i = 1; $i <= 10; $i++) {
            $favorito->cantidades[$i] = 0;
        }
        $favorito->guardar();
    }*/

    // 3. Limpiamos los favoritos
    foreach ($favoritos as $favorito) {
        $favorito->borrar();
    }

    Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('El pedido fue guardado correctamente');
} catch (FactoryExceptionCustomException $ex) {
    Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
    Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurri� un error al intentar guardar el pedido. ' . $ex->getMessage());
}

?>
<?php } ?>
