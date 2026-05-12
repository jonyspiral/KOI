<?php
	$r_empresa = $_POST['form_empresa'];
	$r_numero = $_POST['form_numero'];
	$r_fecha = explode('/', $_POST['form_fecha']);
	$r_nombreCliente = $_POST['form_nombreCliente'];
	$r_idCliente = $_POST['form_idCliente'];
	$r_direccion = $_POST['form_direccion'];
	$r_localidad = $_POST['form_localidad'];
	$r_cuit = $_POST['form_cuit'];
	$r_idCondicionIva = $_POST['form_idCondicionIva'];
	$r_valorDeclarado = $_POST['form_valorDeclarado'];
	$r_cantidadPares = $_POST['form_cantidadPares'];
	$r_cantidadBultos = $_POST['form_cantidadBultos'];
	$r_transportistaNombre = $_POST['form_transportistaNombre'];
	$r_transportistaDomicilio = $_POST['form_transportistaDomicilio'];
	$r_detalle = $_POST['form_detalle'];
	$r_horarioEntrega1 = $_POST['form_horarioEntrega1'];
	$r_horarioEntrega2 = $_POST['form_horarioEntrega2'];
?>
<html>
<head>
<style>
label {
	position: absolute;
	font-family: calibri;
	font-size: 1.2em;
}

#r_fecha_dia {
	/*top: 98px;*/
	top: 143px;
	right: 380px;
}
#r_fecha_mes {
	/*top: 98px;*/
	top: 143px;
	right: 300px;
}
#r_fecha_anio {
	/*top: 98px;*/
	top: 143px;
	right: 205px;
}
#r_nombreCliente {
	/*top: 220px;*/
	top: 265px;
	left: 210px;
}
#r_idCliente {
	/*top: 220px;*/
	top: 225px;
	right: 160px;
}
#r_direccion {
	/*top: 250px;*/
	top: 295px;
	left: 210px;
}
#r_cuit {
	/*top: 285px;*/
	top: 340px;
	left: 205px;
}
#r_localidad {
	/*top: 250px;*/
	top: 295px;
	right: 140px;
}
#r_idCondicionIva_co {
	/*top: 290px;*/
	top: 320px;
	right: 158px;
}
#r_idCondicionIva_ex {
	/*top: 290px;*/
	top: 320px;
	right: 425px;
}
#r_idCondicionIva_ri {
	/*top: 278px;*/
	top: 313px;
	right: 413px;
}
#r_idCondicionIva_mo {
	/*top: 290px;*/
	top: 325px;
	right: 138px;
}
#r_numeroRemito {
	position: absolute;
	top: 365px;
	right: 145px;
}
#r_detalle_ul {
	text-align: left;
	position: absolute;
	list-style: none;
	/*top: 416px;*/
	top: 451px;
	left: 163px;
}
#r_detalle_ul li {
	margin-top: 12.5px;
}
.r_cantidad {
	display: inline-block;
	width: 40px;
}
.r_detalle {
	display: inline-block;
	margin-left: 30px;
}
#r_lineaFinal {
	/*top: 851px;*/
	top: 900px;
	left: 130px;
}
#r_transportistaNombre{
	position: absolute;
	/*top:880px;*/
	top:920px;
	left:180px;
}
#r_transportistaDomicilio{
	position: absolute;
	top:940px;
	left:180px;
}
</style>
</head>
<body>

	<label id='r_fecha_dia'><?php echo $r_fecha[0];?></label>
	<label id='r_fecha_mes'><?php echo $r_fecha[1];?></label>
	<label id='r_fecha_anio'><?php echo $r_fecha[2];?></label>

	<label id='r_nombreCliente'><?php echo $r_nombreCliente;?></label>

	<label id='r_direccion'><?php echo $r_direccion;?></label>

	<label id='r_cuit'><?php echo $r_cuit;?></label>

	<label id='r_idCliente'><?php echo $r_idCliente; ?></label>

	<label id='r_localidad'><?php echo $r_localidad; ?></label>

	<?php if ($r_idCondicionIva == 'CO'){?><label id='r_idCondicionIva_co'>x</label><?php }?>
	<?php if ($r_idCondicionIva == 'EX'){?><label id='r_idCondicionIva_ex'>x</label><?php }?>
	<?php if ($r_idCondicionIva == 'RI'){?><label id='r_idCondicionIva_ri'>x</label><?php }?>
	<?php if ($r_idCondicionIva == 'MO'){?><label id='r_idCondicionIva_mo'>x</label><?php }?>

	<?php if ($r_empresa == '2'){?><label id='r_numeroRemito'><?php echo 'Nro remito KOI: ' . $r_numero ?></label><?php }?>

	<ul id='r_detalle_ul'>
		<?php
		foreach ($r_detalle as $almacen) {
			foreach ($almacen as $colores) {
				foreach ($colores as $item) {
					echo '<li>';
					echo '<div class="r_cantidad">' . $item['cantidad'] . '</div>';
					echo '<div class="r_detalle">' . '<' . $item['codArt'] . ' ' . $item['codColor'] . '> ' . $item['nombreArt'] . ' - ' . $item['nombreColor'] . '</div><br/>';
					echo '</li>';
				}
			}
		}
		?>
	</ul>
	<label id='r_transportistaNombre'><?php echo $r_transportistaNombre; ?></label>
	<label id='r_transportistaDomicilio'><?php echo $r_transportistaDomicilio; ?></label>	
	<label id='r_lineaFinal'>
		Valor declarado: <?php echo $r_valorDeclarado; ?> - 
		Pares: <?php echo $r_cantidadPares; ?> - 
		Bultos: <?php echo $r_cantidadBultos; ?> -
		<?php echo ($r_horarioEntrega1 || $r_horarioEntrega2 ? 'Hor. entrega: ' . ($r_horarioEntrega1 ? $r_horarioEntrega1 . ' y ' : '') . ($r_horarioEntrega2 ? $r_horarioEntrega2 : '') : ''); ?>
	</label>

</body>
</html>