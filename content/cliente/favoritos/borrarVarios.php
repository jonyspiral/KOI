<?php 
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: POST');

require_once('../../../premaster.php'); if (Usuario::logueado()->puede('cliente/favoritos/borrar/')) { ?>
<?php

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($contentType === "application/json") {
 	//Receive the RAW post data.
 	$content = trim(file_get_contents("php://input"));

 	$decoded = json_decode($content, true);

	//echo json_encode($decoded);die;

	$response = array();
 	foreach ($decoded['favorites'] as $fav) {
		try {
			$idCliente = Usuario::logueado()->cliente->id;
		    $favorito = FavoritoCliente::find($idCliente, $fav['idArticulo'], $fav['idColorPorArticulo']);
		    $favorito->borrar();

		    $response[] = array('idArticulo' => $fav['idArticulo'],
		   				'idColorPorArticulo' => $fav['idColorPorArticulo'], 
		   				'saved' => true,
		   				'message' => 'Guardado');
		    continue;
		    //Html::jsonSuccess('El artículo fue eliminado de favoritos');
		} catch (FactoryExceptionRegistroNoExistente $ex) {
			$response[] = array('idArticulo' => $fav['idArticulo'],
		   				'idColorPorArticulo' => $fav['idColorPorArticulo'], 
		   				'saved' => true,
		   				'message' => 'Ya estaba Guardado');
		    //Html::jsonSuccess('El artículo no estaba marcado como favorito');
		} catch (Exception $ex) {
			$response[] = array('idArticulo' => $fav['idArticulo'],
		   				'idColorPorArticulo' => $fav['idColorPorArticulo'], 
		   				'saved' => false,
		   				'message' => $ex->getMessage());
		    //Html::jsonError($ex->getMessage());
		}
		
	}
	echo json_encode(array('status' => 200, 'message' => "success", 'data' => $response));die;

} else {
	 Html::jsonError('Bad Request');
}

} ?>