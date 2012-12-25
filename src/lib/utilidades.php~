<?php
	// Necesario para el SELECT FOUND_ROWS().
	ini_set('mysql.trace_mode', '0');

	// Conecta a la base de datos de la página web. 
	// Devuelve el objeto 'mysqli' de la conexión en caso de éxito, o 
	// finaliza la ejecución en caso de error.
	function GL_ConectarBD ( $datos_bd )
	{
		$bd = new mysqli( $datos_bd['host'], $datos_bd['usuario'], $datos_bd['contrasenna'], $datos_bd['bd'] );

		if( $bd->connect_errno ){
			die( "Error conectando a BD (".$bd->connect_errno.") - ".$bd->connect_error );
		}

		$bd->query("SET NAMES 'utf8'");

		return $bd;
	}


	// Lanza la consulta $consulta a la base de datos. 
	// Devueve el resultado en caso de éxito o finaliza la ejecución si
	// hubo algún error.
	function GL_ConsultarBD ( $datos_bd, $consulta, $guardar_total = false )
	{
		$bd = ConectarBD( $datos_bd );

		$res = $bd->query( $consulta ) or die( "Error con consulta [$consulta]: {$bd->error}" );

		if( $guardar_total ){
			// http://quenerapu.com/mysql/me-encanta-select-found_rows/.
			$aux = $bd->query( 'SELECT FOUND_ROWS()' );
			$_SESSION['num_registros'] = $aux->fetch_row();
			$_SESSION['num_registros'] = $_SESSION['num_registros'][0];
		}

		$bd->close();

		return $res;
	}


	// Función auxiliar para mostrar unos elementos dados por páginas. 
	// Los parámetros requeridos son los siguientes:
	// $pagina_actual: página del libro que se desea mostrar.
	// $fObtenerDatos: ARRAY de strings. El primer elemento es el nombre
	// de la función para recuperar de la BD los datos a mostrar. El resto
	// de elementos son los argumentos que requiera la función anterior.
	// -> Esta función tiene que guardar una variable 
	// $_SESSION['num_registros'] con el nº total de registros que se 
	// encontrarían en la BD sin LIMIT.
	// $fMostrarDato: ARRAY de strings. El primer elemento es el nombre
	// de la función para mostrar un único elemento de los datos. El resto 
	// de elementos del array son los argumentos que requiera la función 
	// anterior.
	// $datos_por_pagina: especifica cuántos datos se muestran por página.
	function GenerarLibro( $pagina_actual, $fObtenerDatos, $fMostrarDato, $datos_por_pagina = 5 )
	{
		// Obtiene los datos y los muestra.
		$datos = call_user_func_array( $fObtenerDatos[0], array_merge( array_slice( $fObtenerDatos, 1 ), array( $pagina_actual*$datos_por_pagina, $datos_por_pagina ) ) );

		//die( print_r( $datos ) );
		while( $dato = $datos->fetch_assoc() ){
			call_user_func_array( $fMostrarDato[0], array_merge( array( $dato ), array_slice( $fMostrarDato, 1 )  ) );
		}

		$datos->close();

		$nPaginas = ceil( $_SESSION['num_registros']/$datos_por_pagina );

		if( $nPaginas <= 1 ) return null;

		// Crea los enlaces a las otras páginas.
		$get = $_GET;
		echo '<div id="seleccion_paginas">';
		for( $pagina=0; $pagina<$nPaginas; $pagina++ ){
			$get['pagina'] = $pagina;
			$getArray = http_build_query( $get );
			if( $pagina != $pagina_actual ){
				echo "<a href=\"" . $_SERVER["PHP_SELF"] . '?' . $getArray . "\" >";
				echo "$pagina</a> ";
			}else{
				echo $pagina . ' ';
			}
		}		
		echo '</div>';
	}	

	// Muestra al usuario un 'alert' de javascript con el mensaje $texto.
	function MostrarAviso( $texto )
	{
		echo '<script language=\'JavaScript\'>';
        	echo "alert('$texto');";
       		echo '</script>';
	}

	// Toma un datetime como argumento y formatea la parte de la fecha
	// para que pase de estar en formato YYYY-MM-DD HH:MM:SS a estar
	// en formato DD-MM-YYYY HH:MM:SS.
	function FormatearFecha( $fecha_ ){
		list( $fecha, $hora ) = explode( ' ', $fecha_ );
		list( $anno, $mes, $dia ) = explode( '-',  $fecha );

		return $dia . '-' . $mes . '-' . $anno . ' ' . $hora;
	}

?>
