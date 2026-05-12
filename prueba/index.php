<?php
	/***
	 * Se pide:
	 * 		1) Conectar a la base de datos. La información de conexión está en el archivo 'connection.php'
	 * 		2) Insertar 4 registros en la tabla 'personas' (ya está creada), que tiene la siguiente estructura:
	 * 			CAMPO				TIPO
	 * 			dni					int	(Primary Key)
	 * 			nombre				varchar(20)
	 * 			apellido			varchar(20)
	 * 			fecha_nacimiento	datetime
	 * 			telefono			varchar(20)
	 * 		3) Pedir los registros usando ajax de jQuery. La información debe viajar en formato JSON.
	 * 		4) Mostrar los resultados en forma de tabla adentro del div 'content'
	 * 		5) Poner color de fondo #EEEFFF a las filas pares (even) de la tabla y #DDDDDD a las filas impares (odd)
	 * 
	 * Restricciones:
	 * 		-No crear archivos nuevos. Utilizar (cómo máximo) los provistos ('index.php', 'ajax.php' y 'connection.php')
	 * 		-No modificar jquery.js. Se pueden editar y usar los archivos 'index.php', 'ajax.php' y 'connection.php'
	 * 		-Crear todas las funciones auxiliares que considere necesarias, tanto en PHP como en JS
	 * 		-Respetar la terminología y la estructura HTML brindada. No cambiarle los nombres a las variables ni editar el código HTML ya provisto
	 * 		-Dado que utilizamos SQL Server 2000, utilizar las funciones mssql_connect y mssql_select_db para conectarse a la Base de Datos
	 * 
	 * Se evaluará:
	 * 		-Correcto funcionamiento de la aplicación
	 * 		-Idoneidad en la resolución
	 * 		-Utilización de jQuery y CSS3
	 * 		-Manejo de errores
	 */
?>

<?php 
?>

<html>
<head>
<script type='text/javascript' src='jquery.js'></script>
<script type='text/javascript'>
	
	function enviarDatos(){
		$.ajax({
                url:'consultas.php',
                success:  function (response) {
				alert("ingreso");
                },
				error : function(error){
				alert("no ingreso");
				}
        });
	
	};
	
	function getRow(){
		$.ajax({
                url:'ajax.php',
				type: 'POST',
				dataType: 'json',
						
                success:  function (response) {
				alert(response);
				var trHTML = '';
						trHTML += 
						'<tr><td>' + response.dni + 
						'</td><tr>' + response.nombre + 
						'<tr><td>'  + response.apellido + 
						'</td></tr>'+ response.fecha_nacimiento +
						'<tr><td>'  + response.telefono +'</td></tr>';
					$('#records_table').append(trHTML);
                },
				error : function(error){
				alert(error);
				}
        });
	
	
	};
	
	


	// Código JS
</script>
<style>
	/* Código CSS */
</style>
</head>
<body>
	<div id='content'> 
	<input type="submit" value="Enviar Datos" onclick="enviarDatos()">
	<input type="submit" value="Buscar Datos" onclick="getRow()">
	
	</div>
	
	<table id="records_table" border='1'>
    <tr>
        <th>dni</th>
        <th>nombre</th>
        <th>apellido</th>
		<th>fechaNacimiento</th>
		<th>telefono</th>
    </tr>
</table>
</body>
</html>
