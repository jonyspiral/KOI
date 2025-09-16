<?php

class Config {
    /* =========================
       DRIVER DE BASE DE DATOS
       ========================= */
    const DB_DRIVER = 'mysql';        // o 'mssql' si querés rollback al legacy

    /* =========================
       PARÁMETROS MySQL (KOI2)
       ========================= */
    const mysql_host    = '192.168.2.210';
    const mysql_port    = 3306;
    const mysql_user    = 'koiuser';
    const mysql_pass    = 'Route667?';
    const mysql_db      = 'koi1_stage';     // usa 'koi2_v1' si probás contra dev
    const mysql_charset = 'utf8';     // clave para PHP 5.6

    /* ======= (dejas tus constantes MSSQL existentes abajo, sin tocar) ======= */
	const conexion_sql_ip = '192.168.2.100';
	//const conexion_sql_user = 'Koi'; Usuario del SQL de CAMACHUI
	//const conexion_sql_pass = 'Koisys.123'; Password del SQL de CAMACHUI
	const conexion_sql_user = 'Koi';
	const conexion_sql_pass = 'koisys';
	//const conexion_sql_db = 'desarrollo';
    const conexion_sql_db = 'encinitas';
    //const conexion_sql_db = 'spiral';
	const siteRoot = '/';
    //const pageTitle = 'Desarrollo';
    const pageTitle = 'SPIRAL SHOES';
    //const pageTitle = 'Koi';
	//const pathBase = '/xampp/htdocs/desarrollo/';


 const pathBase = '/var/www/encinitas/';



    
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


