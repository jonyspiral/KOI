<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('cliente/favoritos/agregar/')) { ?>
<?php

$idArticulo = $_POST['idArticulo'];
$idColor = $_POST['idColor'];

try {
    $favorito = FavoritoCliente::find();
    $favorito->cliente = Usuario::logueado()->cliente;
    $favorito->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColor);
    $favorito->articulo = $favorito->colorPorArticulo->articulo;

    $favorito->guardar();

    Html::jsonSuccess('El artículo fue añadido a favoritos');
} catch (FactoryExceptionRegistroExistente $ex) {
    Html::jsonSuccess('El artículo ya estaba marcado como favorito');
} catch (Exception $ex) {
    Html::jsonError($ex->getMessage());
}

?>
<?php } ?>

