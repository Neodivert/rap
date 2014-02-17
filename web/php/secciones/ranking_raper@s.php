<?php
	/***
	 ranking_raper@s.php
	 Seccion que muestra los rankings globales y mensuales de raper@s.
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
?>

<!-- Rankings globales -->
<h1>Rankings globales</h1>
<strong>Nota: </strong> La puntuaci&oacute;n para los tops generales se calcula sumando 3 puntos por cada perla subida, 2 puntos por cada comentario subido y 1 punto por cada perla calificada.
<?php
	// La informacion para cargar cada top global se obtiene del siguiente
	// array. Cada elemento del array es a su vez otro array que contiene:
	// - El titulo del ranking.
	// - Un array con el metodo a invocar desde call_user_func_array.
	$tops = array( 
		array( 'Top raper@s', array( &$rap, 'ObtenerTopUsuarios' ) ),
		array( 'Perlas subidas', array( &$rap,'ObtenerTopSubidores' ) ),
		array( 'Comentarios realizados', array( &$rap,'ObtenerTopComentaristas' ) ),
		array( 'Perlas calificadas', array( &$rap,'ObtenerTopCalificadores' ) )
	);

	// Muestra cada uno de los tops.
	foreach( $tops as $top ){
		// Muestra el titulo del top.
		echo "<h2>{$top[0]}</h2>";

		// Obtiene como maximo 5 usuarios para cada top.
		echo '<div class="galeria">';
		$usuarios = call_user_func_array( $top[1], array( 5 ) );

		// Para cada usuario del top muestra su avatar con su nombre y su 
		// puntuacion.
		while( $usuario = $usuarios->fetch_assoc() ){
			$rap->MostrarAvatar( $usuario['id'], $usuario['n'] );
		}
		echo '</div>';
	}

	// Rankings mensuales.
	echo '<!-- Rankings mensuales -->';


	// La informacion para cargar cada top mensual se obtiene del siguiente
	// array. Cada elemento del array es a su vez otro array que contiene:
	// - El titulo del ranking.
	// - Un array con el metodo a invocar desde call_user_func_array.
	$tops = array( 
		array( 'Top raper@s', array( &$rap, 'ObtenerTopUsuarios' ) ),
		array( 'Perlas subidas', array( &$rap, 'ObtenerTopSubidores' ) ),
		array( 'Comentarios realizados', array( &$rap, 'ObtenerTopComentaristas' ) ),
		array( 'Perlas calificadas', array( &$rap, 'ObtenerTopCalificadores' ) )
	);


	echo '<h1>Rankings mensuales</h1>';
	echo '<table border="1">';

	// Parte desde el mes y anno actuales.
	$mes = date( "m" );
	$anno = date( "Y" );

	// Muestra los rankings mensuales hacia atras hasta llegar al mes 09/2012 
	// (montaje online de la RAP :D).
	while( $mes > 8 || $anno > 2012 ){
		// Muestra una fila con el mes y anno actuales.
		echo "<tr><th colspan=\"4\">$mes / $anno</th></tr>";

		// Para cada mes, muestra una columna para cada top.
		echo "<tr>";
		foreach( $tops as $top ){
			echo "<th>{$top[0]}</th>";
		}
		echo "</tr><tr>";

		// Para cada top mmuestra los usuarios que lo integran (nombre y 
		// puntuacion).
		foreach( $tops as $top ){
			echo "<td>";

			echo '<ul>';
			$usuarios = call_user_func_array( $top[1], array( 5, $mes, $anno ) );
			while( $usuario = $usuarios->fetch_assoc() ){
				echo '<li>';
				echo "<strong>{$usuario['nombre']}</strong> ({$usuario['n']})";
				echo '</li>';
			}
			echo '</ul>';
			echo '</td>';
		}
		echo "</tr>";

		// Itera al mes pasado.
		if( $mes > 1 ){
			$mes--;
		}else{
			$anno--;
			$mes = 12;
		}
	}
	echo '</table>';

?>


