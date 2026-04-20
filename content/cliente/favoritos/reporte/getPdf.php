<?php require_once('../../../../premaster.php');
if (Usuario::logueado()->puede('cliente/favoritos/reporte/')) { 

	//try {
	    $favoritos = new FavoritoCliente();
	    $favoritos->descargarReporte(Usuario::logueado()->cliente->id);
	//} catch (Exception $ex) {
		//Html::jsonError($ex->getMessage());
	//}

}