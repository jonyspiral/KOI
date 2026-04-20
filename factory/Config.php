<?php
class Config {
    /* =========================
       BASE DE DATOS (MySQL 8)
       ========================= */
    const mysql_host    = '192.168.2.210';
    const mysql_port    = 3306;
    const mysql_user    = 'koiuser';
    const mysql_pass    = 'Route667?';   // ← reemplaza por tu clave
    const mysql_db      = 'koi1_stage';
    const mysql_charset = 'utf8mb4';    // soporte completo para emojis y caracteres especiales

    /* =========================
       RUTAS Y PATHS
       ========================= */
    const siteRoot   = '/';
    const pageTitle  = 'SPIRAL SHOES';
    const pathBase   = '/var/www/encinitas/';
    const urlBase    = 'http://koi.spiralshoes.com/';

    /* =========================
       CACHE (Memcached)
       ========================= */
    const cache_host = 'localhost';
    const cache_port = 11211;

    /* =========================
       DATOS FISCALES
       ========================= */
    const CUIT_SPIRAL  = '30716182815';
    const RAZON_SPIRAL = 'ENCINITAS S.A.S.';
    const CUIT_NCNTS   = '30716182815';
    const RAZON_NCNTS  = 'ENCINITAS S.A.S.';
    const PUNTO_VENTA_NCNTS = 2;

    /* =========================
       HELPERS
       ========================= */
    public static function desarrollo() {
        return self::mysql_db === 'desarrollo';
    }

    public static function encinitas() {
        return self::mysql_db === 'encinitas';
    }
}
