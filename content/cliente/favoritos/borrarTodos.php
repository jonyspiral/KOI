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

	$favoritos = Base::getListObject('FavoritoCliente', 'cod_cliente = ' . Datos::objectToDB(Usuario::logueado()->cliente->id));

	$response = array();
 	foreach ($favoritos as $fav) {
		try {
		    $fav->borrar();
		} catch (Exception $ex) {
			Html::jsonError($ex->getMessage());
		}		
	}
	echo json_encode(array('status' => 200, 'message' => "success", 'data' => $response));die;

} else {
	 Html::jsonError('Bad Request');
}

} ?>