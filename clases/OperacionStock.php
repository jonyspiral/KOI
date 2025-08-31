<?php

/**
 * Es necesario que todas las clases que implementan esta interfaz no usen el modo UPDATE, ya que los movimientos de stocks solo se permiten POSITIVOS o NEGATIVOS.
 * Por ejemplo, si se crea un remito, se crea un movimiento negativo. Si se elimina ese remito, se crea un movimiento negativo. Por eso los remitos no deben poder EDITARSE.
 */
interface OperacionStock {
	public function stock();
	public function stockTipoMovimiento();
	public function stockTipoOperacion();
	public function stockKeyObjeto();
	public function stockObservacion();
	public function stockDetalle();
}

?>