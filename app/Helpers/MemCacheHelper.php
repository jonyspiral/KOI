<?php
// 📄 Generado por Sofía
// Función: Helper para cache local en memoria durante ejecución
// Fecha: 2025-07-11
// Fuente: app/Helpers/MemCacheHelper.php

namespace App\Helpers;

class MemCacheHelper
{
    /**
     * Devuelve un valor desde cache si existe, o lo calcula y lo almacena.
     *
     * @param array  &$cache      Referencia al array de cache
     * @param string $key         Clave única de cache
     * @param \Closure $callback  Función que devuelve el valor si no está en cache
     * @return mixed
     */
    public static function getOrCompute(array &$cache, string $key, \Closure $callback)
    {
        if (!array_key_exists($key, $cache)) {
            $cache[$key] = $callback();
        }
        return $cache[$key];
    }
}
