<?php
	// Conjunto de funciones relacionadas con las notificaciones.
	include_once 'utilidades.php';


	function ObtenerUltimasNotificaciones( $offset = 0, $n = 0 )
	{
		$c1 = "SELECT 'perla' AS tipo, id AS id_perla, id, titulo, fecha_subida, fecha_modificacion FROM perlas";
		$c2 = "SELECT 'comentario' AS tipo, perlas.id AS id_perla, comentarios.id AS id, perlas.titulo, comentarios.fecha_subida, comentarios.fecha_modificacion FROM perlas, comentarios WHERE perlas.id = comentarios.perla";
		$c3 = "SELECT 'voto' AS tipo, perlas.id AS id_perla, 0 AS id, perlas.titulo, '00-00-00' AS fecha_subida, votos.fecha AS fecha_modificacion FROM votos, perlas WHERE perlas.id = votos.perla";
		$c4 = "SELECT 'usuario' AS tipo, 0 AS id_perla, usuarios.id AS id, nombre AS titulo, '00-00-00' AS fecha_subida, fecha_registro AS fecha_modificacion FROM usuarios";

		$consulta = $c1 . " UNION " . $c2 . " UNION " . $c3 . " UNION " . $c4 . " ORDER BY fecha_modificacion DESC";
		if( $n != 0 )
			$consulta .= " LIMIT $offset, $n";

		// Obtiene en un vector las ultimas $n notificaciones.
		return ConsultarBD( $consulta, true );
	}

	function MostrarNotificacion( $notificacion )
	{
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
		}
		echo '<br /><br />';
	}

	// ALTER TABLE `votos` CHANGE `fecha_subida` `fecha` DATETIME NULL DEFAULT NULL 
?>
