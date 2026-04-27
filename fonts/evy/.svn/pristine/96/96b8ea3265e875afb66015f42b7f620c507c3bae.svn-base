<?php

require_once('premaster.php');

function generarPedidos() {
    $idAlmacen = '01';
    $idTemporada = 9;

    $pedidosCliente = Base::getListObject('PedidoCliente', 'anulado = ' . Datos::objectToDB('N') . ' AND cod_pedido IS NULL');
    Logger::addError('Encontrados ' . count($pedidosCliente) . ' pedidos');
    foreach ($pedidosCliente as $pedidoCliente) {
        /** @var PedidoCliente $pedidoCliente */
        try {
            // Genero el Pedido
            Logger::addError('Generando pedido para: ' . $pedidoCliente->id);

            $notaDePedido = Factory::getInstance()->getPedido();
            $notaDePedido->empresa = 1;
            $notaDePedido->cliente = $pedidoCliente->cliente;
            $notaDePedido->sucursal = $pedidoCliente->sucursal;
            $notaDePedido->idAlmacen = $idAlmacen;
            $notaDePedido->vendedor = Factory::getInstance()->getVendedor($pedidoCliente->usuario->contacto->cliente->vendedor->id);
            $notaDePedido->temporada = Factory::getInstance()->getTemporada($idTemporada);
            $notaDePedido->usuario = $pedidoCliente->usuario;
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

            $notaDePedido->guardar()->notificar('comercial/pedidos/nota_de_pedido/agregar/'); // 1. Guardamos la nota de pedido
            $pedidoCliente->pedido = $notaDePedido;
            $pedidoCliente->guardar()->notificar('cliente/pedidos/agregar/'); // 2. Guardamos el PedidoCliente con el número del pedido asociado

            Factory::getInstance()->commitTransaction();

            Logger::addError('Pedido generado para: ' . $pedidoCliente->id);
            echo 'OK ';
        } catch (Exception $ex) {
            Factory::getInstance()->rollbackTransaction();
            Logger::addError('Fallo una: ' . $pedidoCliente->id);
        }
    }
}

generarPedidos();

?>
