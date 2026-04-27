<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('cliente/favoritos/editar/')) { ?>
<?php

$idArticulo = $_POST['idArticulo'];
$idColor = $_POST['idColor'];
$cantidades = $_POST['cantidades'];
$idCliente = Usuario::logueado()->cliente->id;

try {
    try {
        $favorito = FavoritoCliente::find($idCliente, $idArticulo, $idColor);
    } catch (FactoryExceptionRegistroNoExistente $ex) {
        $favorito = FavoritoCliente::find();
        $favorito->cliente = Usuario::logueado()->cliente;
        $favorito->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColor);
        $favorito->articulo = $favorito->colorPorArticulo->articulo;
    }

    for ($i = 1; $i <= 10; $i++) {
        $aux = Funciones::toInt($cantidades[$i - 1]);
        $favorito->cantidades[$i] = $aux >= 0 && $aux < 999 ? $aux : 0;
    }

    $favorito->guardar();
    // Compatibilidad MySQL KOI1:
    // Garantiza persistencia de cantidades libres en favoritos_cliente.cant_1 ... cant_10
    // antes de confirmar el pedido.
    $set = array();
    for ($i = 1; $i <= 10; $i++) {
        $set[] = 'cant_' . $i . ' = ' . Datos::objectToDB(Funciones::toInt(Funciones::keyIsSet($favorito->cantidades, $i, 0)));
    }
    $set[] = 'fecha_ultima_mod = NOW()';

    Datos::EjecutarSQLsinQuery(
        'UPDATE favoritos_cliente SET ' . implode(', ', $set) .
        ' WHERE cod_cliente = ' . Datos::objectToDB($idCliente) .
        ' AND cod_articulo = ' . Datos::objectToDB($idArticulo) .
        ' AND cod_color_articulo = ' . Datos::objectToDB($idColor)
    );


    Html::jsonSuccess('El favorito fue modificado correctamente');
} catch (Exception $ex) {
    Html::jsonError('Ocurri� un error al intentar modificar el favorito. ' . $ex->getMessage());
}

?>
<?php } ?>

