<?php

class Config {
	// Configuración de Conexión al servidor
	const conexion_sql_ip = 'localhost';
	//const conexion_sql_user = 'Koi'; Usuario del SQL de CAMACHUI
	//const conexion_sql_pass = 'Koisys.123'; Password del SQL de CAMACHUI
	const conexion_sql_user = 'Koi';
	const conexion_sql_pass = 'koisys';
	const conexion_sql_db = 'desarrollo';
    //const conexion_sql_db = 'encinitas';
    //const conexion_sql_db = 'spiral';
	const siteRoot = '/';
    const pageTitle = 'Desarrollo';
    //const pageTitle = 'Encinitas';
    //const pageTitle = 'Koi';
	const pathBase = '/xampp/htdocs/desarrollo/';
    //const pathBase = '/xampp/htdocs/ncnts/';
    //const pathBase = '/xampp/htdocs/koi/';
    const urlBase = 'http://desarrollo/';
    //const urlBase = 'http://koi.spiralshoes.com/';

    // Cache
    const cache_host = 'localhost';
    const cache_port = 11211;

    /*
	const CUIT_SPIRAL = '33710051459';
	const RAZON_SPIRAL = 'SPIRAL SHOES S.A.';
	*/
    const CUIT_SPIRAL = '30715928678';
    const RAZON_SPIRAL = 'ENCINITAS S.A.S.';

    const CUIT_NCNTS = '30715928678';
    const RAZON_NCNTS = 'ENCINITAS S.A.S.';

    const PUNTO_VENTA_NCNTS = 4;

    public static function desarrollo() {
        return self::conexion_sql_db == 'desarrollo';
    }

    public static function encinitas() {
        return self::conexion_sql_db == 'encinitas';
    }
}

?>