<?php

class Config {
	// Configuración de Conexión al servidor
	const conexion_sql_ip = 'localhost';
	//const conexion_sql_user = 'Koi'; Usuario del SQL de CAMACHUI
	//const conexion_sql_pass = 'Koisys.123'; Password del SQL de CAMACHUI
	const conexion_sql_user = 'Koi';
	const conexion_sql_pass = 'koisys';
	//const conexion_sql_db = 'readytogo';
	const conexion_sql_db = 'readytogo';
	const siteRoot = '/';
	//const pageTitle = 'Ready to go!';
	const pageTitle = 'Ready to go!';
	//const pathBase = '/xampp/htdocs/rtg/';
	const pathBase = '/xampp/htdocs/rtg/';
	//const urlBase = 'http://rtg/';
	const urlBase = 'http://rtg/';

	// Cache
    const cache_host = 'localhost';
    const cache_port = 11211;

	const CUIT_SPIRAL = '123';
	const RAZON_SPIRAL = 'READY TO GO S.A.';

	public static function desarrollo() {
		return self::conexion_sql_db == 'desarrollo';
	}
}

?>
