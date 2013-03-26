<?php
	/***
	 historico.php
	 Seccion que muestra los eventos ocurridos en la RAP por orden cronologico y
	 organizados por paginas.
	 Copyright (C) Moises J. Bonilla Caraballo 2012 - 2013.
	****
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
	***/

	// Se hace uso de la clase singlenton Historico.
	require_once DIR_CLASES . 'historico.php';

	// El historico se muestra por paginas. Pagina por defecto: 0.
	$pagina_actual = isset( $_GET['pagina'] ) ? $_GET['pagina'] : 0;
	
	// Establece el numero de eventos a mostrar por pagina.
	$nElementosPorPagina = 25;

	// Obtiene los eventos para la pagina actual.
	$historico = Historico::ObtenerInstancia();
	$notificaciones = $historico->ObtenerUltimosEventosBD( BD::ObtenerInstancia(), $pagina_actual*$nElementosPorPagina, $nElementosPorPagina );

	// Accede a la BD y obtiene el numero total de eventos del historico (se usa
	// para generar los enlaces a las otras paginas del historico).
	$bd = BD::ObtenerInstancia();
	$nElementos = $bd->ObtenerNumFilasEncontradas();

	// Recorre los eventos y los muestra segun su tipo.
	while( $notificacion = $notificaciones->fetch_assoc() ){
		echo "[{$notificacion['fecha_modificacion']}]: ";
		switch( $notificacion['tipo'] ){


			// Perla subida/modificada.
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


			// Comentario subido/modificado.
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


			// Voto subido/modificado.
			case 'voto':
				echo "<a href=\"general.php?seccion=mostrar_perla&perla={$notificacion['id_perla']}\">";
				echo 'Ha cambiado la puntuaci&oacute;n de la perla: ';
				echo $notificacion['titulo'];
				echo '</a>';
			break;


			// Nuevo usuario.
			case 'usuario':
				echo "Nuevo usuario: {$notificacion['titulo']}";
			break;


			// Evento desconocido.
			default:
				echo "SIN_TIPO[{$notificacion['tipo']}]";
			break;
		}
		echo '<br /><br />';
	}

	// Genera los enlaces para acceder a las otras paginas del historico.
	require DIR_PLANTILLAS . 'selector_paginas.php';
?>
