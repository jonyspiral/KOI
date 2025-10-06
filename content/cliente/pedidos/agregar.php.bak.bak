<<?php require_once('../../../premaster.php');
print_r($_POST);
error_log('[cliente/pedidos/agregar.php] IN ' . (isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'no-ct'));
if (!empty($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    error_log('[cliente/pedidos/agregar.php] RAW: ' . substr($raw, 0, 512));
    if ($raw !== false && $raw !== '') {
        $json = json_decode($raw, true);
        if (is_array($json)) {
            foreach ($json as $k => $v) {
                if (!isset($_POST[$k])) {
                    $_POST[$k] = $v;
                }
            }
        }
    }
}

if (Usuario::logueado()->puede('cliente/pedidos/agregar/')) { ?>
    <?php
    $idSucursal = Funciones::post('idSucursal');
    error_log('[cliente/pedidos/agregar.php] idSucursal=' . var_export($idSucursal, true));
    if ($idSucursal === null || $idSucursal === '') {
        header('HTTP/1.1 400 Bad Request');
        Html::jsonError('Falta idSucursal');
        exit;
    }



    $idAlmacen = '01';
$idTemporada = 9;
// abrir transacción antes de operaciones (si aún no lo hiciste)
    Factory::getInstance()->beginTransaction();
    error_log("[agregar.php] beginTransaction OK");
// antes de armar PedidoCliente
    error_log("[agregar.php] armar PedidoCliente para cliente=" . (isset(Usuario::logueado()->cliente->id) ? Usuario::logueado()->cliente->id : 'null') . " sucursal=" . var_export($idSucursal, true));
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
// después de calcular totales de pedidoCliente
    error_log("[agregar.php] pedidoCliente total=" . var_export($pedidoCliente->importeTotal, true));

// antes de armar nota de pedido
    error_log("[agregar.php] armar NotaDePedido (idAlmacen=" . var_export(isset($idAlmacen) ? $idAlmacen : null, true) . ", idTemporada=" . var_export(isset($idTemporada) ? $idTemporada : null, true) . ")");

    // Genero el Pedido

    $notaDePedido = Factory::getInstance()->getPedido();
    $notaDePedido->empresa = 1;
    $notaDePedido->cliente = $pedidoCliente->cliente;
    $notaDePedido->sucursal = $pedidoCliente->sucursal;
    $notaDePedido->idAlmacen = $idAlmacen;

    // Resolver vendedor de forma segura (evita fatal si no hay contacto asociado)
    $vendedorId = null;
    try {
        if (isset(Usuario::logueado()->contacto) && isset(Usuario::logueado()->contacto->cliente) && isset(Usuario::logueado()->contacto->cliente->vendedor)) {
            $vendedorId = Usuario::logueado()->contacto->cliente->vendedor->id;
        }
    } catch (Exception $e) { /* ignore */ }
    if (!$vendedorId && isset($pedidoCliente->cliente) && isset($pedidoCliente->cliente->vendedor)) {
        $vendedorId = $pedidoCliente->cliente->vendedor->id;
    }
    if (!$vendedorId && isset(Usuario::logueado()->cliente) && isset(Usuario::logueado()->cliente->vendedor)) {
        $vendedorId = Usuario::logueado()->cliente->vendedor->id;
    }
    if (!$vendedorId) {
        throw new FactoryExceptionCustomException('No se pudo determinar el vendedor para el pedido.');
    }
    $notaDePedido->vendedor = Factory::getInstance()->getVendedor($vendedorId);

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


    // después del commit
    Factory::getInstance()->commitTransaction();
    error_log("[agregar.php] commit OK");

	Html::jsonSuccess('El pedido fue guardado correctamente');
    // en los catch, loguear el mensaje y proteger rollback
} catch (FactoryExceptionCustomException $ex) {
    error_log("[agregar.php] CATCH FactoryExceptionCustomException: " . $ex->getMessage());
    try { Factory::getInstance()->rollbackTransaction(); error_log("[agregar.php] rollback OK"); } catch (Exception $e2) { error_log("[agregar.php] rollback FAIL: " . $e2->getMessage()); }
    Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
    error_log("[agregar.php] CATCH Exception: " . $ex->getMessage());
    try { Factory::getInstance()->rollbackTransaction(); error_log("[agregar.php] rollback OK"); } catch (Exception $e2) { error_log("[agregar.php] rollback FAIL: " . $e2->getMessage()); }
    Html::jsonError('Ocurrió un error al intentar guardar el pedido. ' . $ex->getMessage());
}

?>
<?php } ?>
