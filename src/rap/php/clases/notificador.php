<?php
	/*** Info: ***
	Conjunto de funciones relacionadas con las notificaciones.

	/*** Licencia ***
    This file is part of RAP.

    RAP is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    RAP is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with RAP.  If not, see <http://www.gnu.org/licenses/>.
	*/
	// TODO: CAMBIAR NOMBRE Y VER SI JUNTO ESTAS "NOTIFICACIONES" CON LAS
	// NOTIFICACIONES POR EMAIL.
	class Notificador {

		function ObtenerUltimasNotificaciones( $bd, $offset = 0, $n = 0 )
		{
			$c1 = "SELECT SQL_CALC_FOUND_ROWS 'perla' AS tipo, id AS id_perla, id, titulo, fecha_subida, fecha_modificacion FROM perlas";
			$c2 = "SELECT 'comentario' AS tipo, perlas.id AS id_perla, comentarios.id AS id, perlas.titulo, comentarios.fecha_subida, comentarios.fecha_modificacion FROM perlas, comentarios WHERE perlas.id = comentarios.perla";
			$c3 = "SELECT 'voto' AS tipo, perlas.id AS id_perla, 0 AS id, perlas.titulo, '00-00-00' AS fecha_subida, votos.fecha AS fecha_modificacion FROM votos, perlas WHERE perlas.id = votos.perla";
			$c4 = "SELECT 'usuario' AS tipo, 0 AS id_perla, usuarios.id AS id, nombre AS titulo, '00-00-00' AS fecha_subida, fecha_registro AS fecha_modificacion FROM usuarios";

			$consulta = $c1 . " UNION " . $c2 . " UNION " . $c3 . " UNION " . $c4 . " ORDER BY fecha_modificacion DESC";
			if( $n != 0 )
				$consulta .= " LIMIT $offset, $n";

			// Obtiene en un vector las ultimas $n notificaciones.
			return $bd->Consultar( $consulta );
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

	};

	// ALTER TABLE `votos` CHANGE `fecha_subida` `fecha` DATETIME NULL DEFAULT NULL 
?>
