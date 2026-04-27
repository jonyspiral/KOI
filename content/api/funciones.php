<?php

function sqlEscape($sql) {

    /* De MagicQuotes */
    $fix_str        = stripslashes($sql);
    $fix_str    = str_replace("'","''",$sql);
    $fix_str     = str_replace("\0","[NULL]",$fix_str);

    return $fix_str;

}	 
	
function getStockEnProduccion($articulo, $color){
	$host="192.168.2.100";
	$bd="spiral";
	$user="juancarlos";
	$pass="juancarlos,spiral2020";

	$codigoArticulo = sqlEscape($articulo);
	$codigoColor = sqlEscape($color);

	try {
	
		$link1 = @mssql_connect($host, $user, $pass);
		@mssql_select_db($bd);
		$sql = 'select Top 1 * FROM stock_produccion_incumplida_40_v where cod_articulo = \'' . $codigoArticulo . '\' AND cod_color_articulo = \'' . $codigoColor . '\'' ;
		$result = @mssql_query($sql, $link1);

		if (mssql_num_rows($result)==0) {
			return array('error' => 'no hay filas');
		}

		$row = @mssql_fetch_assoc($result);

		//echo json_encode(array('sql' => $sql));die;
		mssql_close($link1);

		return array('data' => $row);
	} catch (Exception $e) {
		//mssql_close($link1);
		return array('error' => $e->getMessage());
	}
}