<?php
if (!ob_get_level()) {
    ob_start();
}

function pedidosAgregarFatalHandler() {
    $error = error_get_last();
    if (!$error) {
        return;
    }

    $fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR);
    if (!in_array($error['type'], $fatalTypes, true)) {
        return;
    }

    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode(array(
        'status' => 500,
        'message' => 'Fatal error',
        'data' => array(
            'type' => $error['type'],
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        )
    ));
}

register_shutdown_function('pedidosAgregarFatalHandler');

require_once('../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('cliente/pedidos/agregar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
}

$idSucursal = Funciones::post('idSucursal');
$idAlmacen = '01';
$idTemporada = 9;

try {
    $pedidoCliente = PedidoCliente::find();
    $pedidoCliente->cliente = $usuario->cliente;
    $pedidoCliente->sucursal = Factory::getInstance()->getSucursal($usuario->cliente->id, $idSucursal);
    $pedidoCliente->estado = PedidoCliente::ESTADO_PENDIENTE;

    $favoritos = Base::getListObject('FavoritoCliente', 'cod_cliente = ' . Datos::objectToDB($usuario->cliente->id));

    $nroItem = 1;
    foreach ($favoritos as $favorito) {
        $arrCantidades = array();

        if (count($favorito->curvas)) {
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
            $i = 1;
            foreach ($favorito->cantidades as $cantidad) {
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
        throw new FactoryExceptionCustomException('No se puede cargar un pedido vacio');
    }

    $notaDePedido = Factory::getInstance()->getPedido();
    $notaDePedido->empresa = 1;
    $notaDePedido->cliente = $pedidoCliente->cliente;
    $notaDePedido->sucursal = $pedidoCliente->sucursal;
    $notaDePedido->idAlmacen = $idAlmacen;
    $notaDePedido->vendedor = Factory::getInstance()->getVendedor($usuario->contacto->cliente->vendedor->id);
    $notaDePedido->temporada = Factory::getInstance()->getTemporada($idTemporada);
    $notaDePedido->usuario = $usuario;
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

    Factory::getInstance()->beginTransaction();

    $notaDePedido->guardar()->notificar('comercial/pedidos/nota_de_pedido/agregar/');
    $pedidoCliente->pedido = $notaDePedido;
    $pedidoCliente->guardar()->notificar('cliente/pedidos/agregar/');

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
    Html::jsonError('Ocurrio un error al intentar guardar el pedido. ' . $ex->getMessage());
}