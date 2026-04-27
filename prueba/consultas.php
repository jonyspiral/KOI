<?php 

require_once 'connection.php';

$sql= "INSERT INTO $table_name (dni,nombre,apellido,fecha_nacimiento,telefono)values(38268550,'alejandro','falafa','13/02/1950',54555516)";

$result = query($sql);








?>