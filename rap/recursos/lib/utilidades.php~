<?php
	require DIR_GLOBAL_LIB . 'utilidades.php';	

	function ConectarBD ()
	{
		return GL_ConectarBD ( $GLOBALS['datos_bd'] );
	}


	// Lanza la consulta $consulta a la base de datos. 
	// Devueve el resultado en caso de éxito o finaliza la ejecución si
	// hubo algún error.
	function ConsultarBD( $consulta, $guardar_total = false )
	{
		return GL_ConsultarBD( $GLOBALS['datos_bd'], $consulta, $guardar_total );
	}

?>
