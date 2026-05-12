<?php	
		$razonSocial = $_POST['form_razonSocial']; 
		$clienteNro = $_POST['form_clienteNro'];
		$direccionEntregaCalle = $_POST['form_direccionEntregaCalle']; 
		$direccionEntregaNumero = $_POST['form_direccionEntregaNumero'];
		$direccionEntregaProvincia = $_POST['form_direccionEntregaProvincia'];
		$direccionEntregaPiso = $_POST['form_direccionEntregaPiso'];
		$direccionEntregaDpto = $_POST['form_direccionEntregaDpto'];
		$direccionEntregaLocalidad = $_POST['form_direccionEntregaLocalidad'];
		$direccionEntregaCP = $_POST['form_direccionEntregaCP'];
		$transportistaNombre = $_POST['form_transportistaNombre'];
		$transportistaDomicilio = $_POST['form_transportistaDomicilio'];
		$transportistaCUIT = $_POST['form_transportistaCUIT'];
		$transportistaDNI = $_POST['form_transportistaDNI'];
		$horarioEntrega1 = $_POST['form_horarioEntrega1'];
		$horarioEntrega2 = $_POST['form_horarioEntrega1'];
?>
<head>
<link rel="stylesheet" type="text/css" href="../../../../css/styles.css" media="screen"/>
<style>
#datos{
	margin-top: 130px;
	font-size: 3.5em;
}


</style>
</head>
<body>
<?php for ($i = 0 ; $i<=1; $i++){?>
	<?php echo '<div id="datos" class="aCenter">';
	$hayHorariosEntrega = $horarioEntrega1 || $horarioEntrega2;

	 echo $razonSocial . '<br/>';
	 echo ($direccionEntregaCalle ? Funciones::toUpper($direccionEntregaCalle) . ' ' . $direccionEntregaNumero . '<br/>' : '');
	 echo ($direccionEntregaProvincia ? Funciones::toUpper($direccionEntregaProvincia) . '<br/>' : '');
	 echo ($direccionEntregaLocalidad ? Funciones::toUpper($direccionEntregaLocalidad) . '<br/>' : '');
	 echo ($direccionEntregaCP ? Funciones::toUpper($direccionEntregaCP) . '<br/>' : '');
	 echo ($transportistaNombre ? Funciones::toUpper($transportistaNombre) . '<br/>' : '');
	 echo Funciones::toUpper($transportistaDomicilio) . ($hayHorariosEntrega ? '<br/>' : '');
	 echo ($hayHorariosEntrega ? 'Hor. entrega: ' . ($horarioEntrega1 ? $horarioEntrega1 . ' y ' : '') . ($horarioEntrega2 ? $horarioEntrega2 : '') . ($hayHorariosEntrega ? '<br/>' : '') : '');
	 echo 'REMITO Nº: __________ BULTOS: ______';
	 echo '</div>';
	 }?>
</body>