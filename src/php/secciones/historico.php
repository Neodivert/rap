
<?php
	require_once DIR_CLASES . 'log.php';

	$pagina_actual = isset( $_GET['pagina'] ) ? $_GET['pagina'] : 0;
	
	$log = new log;
	$notificaciones = $log->ObtenerUltimasNotificaciones( BD::ObtenerInstancia(), $pagina_actual*25, 25 );

	$bd = BD::ObtenerInstancia();
	
	$nElementos = $bd->ObtenerNumFilasEncontradas();

	while( $notificacion = $notificaciones->fetch_assoc() ){
		echo "[{$notificacion['fecha_modificacion']}]: ";
		switch( $notificacion['tipo'] ){
			case 'perla':
				echo "<a href=\"general.php?seccion=mostrar_perla&perla={$notificacion['id_perla']}\">";
				if( $notificacion['fecha_subida'] != $notificacion['fecha_modificacion'] ){
					echo 'Perla modificada: ';
				}else{
					echo 'Perla subida: ';
				}
				echo $notificacion['titulo'];
				echo '</a>';
			break;
			case 'comentario':
				echo "<a href=\"general.php?seccion=mostrar_perla&perla={$notificacion['id_perla']}\">";
				if( $notificacion['fecha_subida'] != $notificacion['fecha_modificacion'] ){
					echo 'Comentario modificado en: ';
				}else{
					echo 'Comentario subido en: ';
				}
				echo $notificacion['titulo'];
				echo '</a>';
			break;
			case 'voto':
				echo "<a href=\"general.php?seccion=mostrar_perla&perla={$notificacion['id_perla']}\">";
				echo 'Ha cambiado la puntuaci&oacute;n de la perla: ';
				echo $notificacion['titulo'];
				echo '</a>';
			break;
			case 'usuario':
				echo "Nuevo usuario: {$notificacion['titulo']}";
			break;
			default:
				echo "SIN_TIPO[{$notificacion['tipo']}]";
			break;
		}
		echo '<br /><br />';
	}

	$nElementosPorPagina = 25;

	require DIR_PLANTILLAS . 'selector_paginas.php';
?>
