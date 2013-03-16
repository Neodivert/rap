<h1>Rankings totales</h1>
<strong>Nota: </strong> La puntuaci&oacute;n para los tops generales se calcula sumando 3 puntos por cada perla subida, 2 puntos por cada comentario subido y 1 punto por cada perla calificada.
<?php
	$tops = array( 
		array( 'Top raper@s', array( &$rap, 'ObtenerTopUsuarios' ), array( 5 ) ),
		array( 'Perlas subidas', array( &$rap,'ObtenerTopSubidores' ), array( 5 ) ),
		array( 'Comentarios realizados', array( &$rap,'ObtenerTopComentaristas' ), array( 5 ) ),
		array( 'Perlas calificadas', array( &$rap,'ObtenerTopCalificadores' ), array( 5 ) )
	);

	foreach( $tops as $top ){
		echo "<h2>{$top[0]}</h2>";
		//echo '<ul>';
		echo '<div class="galeria">';
		$usuarios = call_user_func_array( $top[1], $top[2] );
		while( $usuario = $usuarios->fetch_assoc() ){
			$rap->MostrarAvatar( $usuario['id'], $usuario['n'] );
		}
		echo '</div>';
	}


	$meses = array( 11, 10, 9 );
	$annos = array( 2012 );

	$tops = array( 
		array( 'Top raper@s', array( &$rap, 'ObtenerTopUsuarios' ) ),
		array( 'Perlas subidas', array( &$rap, 'ObtenerTopSubidores' ) ),
		array( 'Comentarios realizados', array( &$rap, 'ObtenerTopComentaristas' ) ),
		array( 'Perlas calificadas', array( &$rap, 'ObtenerTopCalificadores' ) )
	);


	echo '<h1>Rankings mensuales</h1>';
	echo '<table border="1">';
	// Muestra los rankings mensuales. 
	// Parte desde el mes actual y llega hasta el mes 09/2012 (montaje online de RAP :D)
	$mes = date( "m" );
	$anno = date( "Y" );

	while( $mes > 8 || $anno > 2012 ){
		echo "<tr><th colspan=\"4\">$mes / $anno</th></tr>";
		echo "<tr>";
		foreach( $tops as $top ){
			echo "<th>{$top[0]}</th>";
		}
		echo "</tr><tr>";
		foreach( $tops as $top ){
			echo "<td>";

			//echo '<div class="galeria">';
			echo '<ul>';
			$usuarios = call_user_func_array( $top[1], array( 5, $mes, $anno ) );
			while( $usuario = $usuarios->fetch_assoc() ){
				//MostrarAvatar( $usuario['nombre'], $usuario['n'] );
				
				echo '<li>';
				echo "<strong>{$usuario['nombre']}</strong> ({$usuario['n']})";
				echo '</li>';
			}
			echo '</ul>';
			//echo '</div>';
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


