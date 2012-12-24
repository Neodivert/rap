
<?php
	if( !isset( $_GET['pagina'] ) ) $_GET['pagina'] = 0;

	require_once DIR_LIB . 'notificaciones.php';

	GenerarLibro( $_GET['pagina'], array( 'ObtenerUltimasNotificaciones' ), array( 'MostrarNotificacion' ), 25 );
?>
