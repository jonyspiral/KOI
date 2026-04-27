<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('cliente/favoritos/editar/')) { ?>
<?php

$idArticulo = $_POST['idArticulo'];
$idColor = $_POST['idColor'];
$idCurva = $_POST['idCurva'];
$unidades = $_POST['unidades'];
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

    $favorito->curvas[$idCurva] = $unidades >= 0 ? $unidades : 0;

    $favorito->guardar();

    Html::jsonSuccess('El favorito fue modificado correctamente');
} catch (Exception $ex) {
    Html::jsonError('Ocurrió un error al intentar modificar el favorito. ' . $ex->getMessage());
}

?>
<?php } ?>

