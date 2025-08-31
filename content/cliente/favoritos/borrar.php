<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('cliente/favoritos/borrar/')) { ?>
<?php

$idArticulo = $_POST['idArticulo'];
$idColor = $_POST['idColor'];
$idCliente = Usuario::logueado()->cliente->id;

try {
    $favorito = FavoritoCliente::find($idCliente, $idArticulo, $idColor);
    $favorito->borrar();

    Html::jsonSuccess('El artículo fue eliminado de favoritos');
} catch (FactoryExceptionRegistroNoExistente $ex) {
    Html::jsonSuccess('El artículo no estaba marcado como favorito');
} catch (Exception $ex) {
    Html::jsonError($ex->getMessage());
}

?>
<?php } ?>

