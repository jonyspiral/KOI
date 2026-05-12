<?php
	require_once 'connection.php';
	
	
	$sql = "SELECT * FROM $table_name";
	$result = query($sql);
	$respuesta = getRow($result);
	
	echo json_encode($respuesta);
?>