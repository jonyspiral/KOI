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

    Html::jsonSuccess('El favorito fue modificado correctamente');
} catch (Exception $ex) {
    Html::jsonError('Ocurrió un error al intentar modificar el favorito. ' . $ex->getMessage());
}

?>
<?php } ?>

