<?php

class Config {
	// Configuraci�n de Conexi�n al servidor
	const conexion_sql_ip = 'localhost';
	//const conexion_sql_user = 'Koi'; Usuario del SQL de CAMACHUI
	//const conexion_sql_pass = 'Koisys.123'; Password del SQL de CAMACHUI
	const conexion_sql_user = 'Koi';
	const conexion_sql_pass = 'koisys';
	//const conexion_sql_db = 'desarrollo';
    const conexion_sql_db = 'encinitas';
    //const conexion_sql_db = 'spiral';
    // Compatibilidad con el motor MySQL nuevo sin romper referencias legacy.
    const mysql_host = self::conexion_sql_ip;
    const mysql_port = 3306;
    const mysql_db = self::conexion_sql_db;
    const mysql_user = self::conexion_sql_user;
    const mysql_pass = self::conexion_sql_pass;
    const mysql_charset = 'utf8mb4';
	const siteRoot = '/';
    //const pageTitle = 'Desarrollo';
    const pageTitle = 'SPIRAL SHOES';
    //const pageTitle = 'Koi';
	//const pathBase = '/xampp/htdocs/desarrollo/';
    const pathBase = '/xampp/htdocs/encinitas/';
    //const pathBase = '/xampp/htdocs/koi/';
    // const urlBase = 'http://desarrollo/';
    const urlBase = 'http://koi.spiralshoes.com/';

    // Cache
    const cache_host = 'localhost';
    const cache_port = 11211;

    /*
	const CUIT_SPIRAL = '33710051459';
	const RAZON_SPIRAL = 'SPIRAL SHOES S.A.';
	*/
    const CUIT_SPIRAL = '30716182815';
    const RAZON_SPIRAL = 'ENCINITAS S.A.S.';

    const CUIT_NCNTS = '30716182815';
    const RAZON_NCNTS = 'ENCINITAS S.A.S.';

    const PUNTO_VENTA_NCNTS = 2;

    public static function desarrollo() {
        return self::conexion_sql_db == 'desarrollo';
    }

    public static function encinitas() {
        return self::conexion_sql_db == 'encinitas';
    }
}

?>
