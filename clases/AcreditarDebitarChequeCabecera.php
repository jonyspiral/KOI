<?php

class AcreditarDebitarChequeCabecera extends TransferenciaDobleCabecera {
	const		_primaryKey = '["numero", "empresa"]';

	public		$fecha;
	public		$tipo;
}

?>