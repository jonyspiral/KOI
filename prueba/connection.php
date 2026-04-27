<?php
	$db_host = 'localhost';
	$db_user = 'test';
	$db_pass = 'test';
	$db_name = 'test';
	$table_name = 'personas';
	/***
	 * CAMPO			TIPO
	 * dni				int	(Primary Key)
	 * nombre			varchar(20)
	 * apellido			varchar(20)
	 * fecha_nacimiento	datetime
	 * telefono			varchar(20)
	 */
?>

<?php
	mssql_connect($db_host, $db_user, $db_pass);
	mssql_select_db($db_name);

	function query($query) {
		return mssql_query($query);
	}
	function getRow($result){
		return mssql_fetch_assoc($result);
	}
	function numRows($result){
		return mssql_num_rows($result);
	}
	
	
?>