<?php

function sqlEscape($str) {
    // Arreglo menor (tu versión tenía una línea usando $sql en lugar de $fix_str)
    $fix_str = stripslashes($str);
    $fix_str = str_replace("'", "''", $fix_str);
    $fix_str = str_replace("\0", "[NULL]", $fix_str);
    return $fix_str;
}

function getStockEnProduccion($articulo, $color){
    try {
        $sql = 'SELECT *
                FROM stock_produccion_incumplida_40_v
                WHERE cod_articulo = ' . Datos::objectToDB($articulo) . '
                  AND cod_color_articulo = ' . Datos::objectToDB($color) . '
                LIMIT 1';

        $row = Datos::EjecutarSQLItem($sql);

        if (!$row || !is_array($row) || count($row) == 0) {
            return array('error' => 'no hay filas');
        }

        return array('data' => $row);

    } catch (Exception $e) {
        return array('error' => $e->getMessage());
    }
}
