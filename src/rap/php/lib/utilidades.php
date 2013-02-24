<?php
	//require_once DIR_GLOBAL_LIB . 'utilidades.php';	
	require_once DIR_CONFIG . 'bd.php';


	//function ConectarBD ()
	//{
	//	return GL_ConectarBD ( $GLOBALS['datos_bd'] );
	//}


	// Lanza la consulta $consulta a la base de datos. 
	// Devueve el resultado en caso de éxito o finaliza la ejecución si
	// hubo algún error.
	//function ConsultarBD( $consulta, $bd_extra_info = BD_EXTRA_INFO::NADA )
	//{
	//	return GL_ConsultarBD( $GLOBALS['datos_bd'], $consulta, $bd_extra_info );
	//}


	function NotificarPorEmail( $tipo_notificacion, $id )
	{
		$bd = ConectarBD();
		switch( $tipo_notificacion ){
			case 'nueva_perla':
				$titulo = 'RAP - Nueva perla subida';
				$cuerpo = "Se ha subido una nueva perla a la RAP. Para verla pulsa en el siguiente enlace: \r\n";
				$cuerpo .= "http://www.neodivert.com/rap/general.php?seccion=mostrar_perla&perla=$id\r\n";

				$emails = $bd->query( "(SELECT DISTINCT email FROM usuarios JOIN notificaciones_email ON usuarios.id = notificaciones_email.usuario AND notificaciones_email.frecuencia='siempre' AND usuarios.cod_validacion_email IS NULL) UNION (SELECT DISTINCT email FROM usuarios JOIN notificaciones_email ON usuarios.id = notificaciones_email.usuario AND notificaciones_email.frecuencia='participante' AND usuarios.cod_validacion_email IS NULL JOIN participantes ON notificaciones_email.usuario = participantes.usuario WHERE participantes.perla=$id)" ) or die( $bd->error );
			break;
		}

		ini_set('sendmail_from', 'neodivert@gmail.com' );
		$bd->close();

		$destinatarios = '';
		while( $email = $emails->fetch_array() ){
			$destinatarios .= "{$email['email']}, ";
		}
		if( !mail( $destinatarios, $titulo, $cuerpo ) ){
			die( 'Error enviando las notificaciones' );
		}
	}

	function CrearCabeceraFormulario( $controlador, $method, $confirmacion = NULL ){
		echo "<form action=\"{$controlador}\" method=\"$method\" ";
		if( $confirmacion != NULL ){
			echo 'onsubmit="return confirm(\'Estas segur@?\')" ';
		}
		echo '>';
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
	function GenerarLibro( $pagina_actual, $fObtenerDatos, $fContarDatos, $claseDato, $datos_por_pagina = 5 )
	{
		// Obtiene los datos y los muestra.
		$datos = call_user_func_array( $fObtenerDatos[0], array_merge( array_slice( $fObtenerDatos, 1 ), array( $pagina_actual*$datos_por_pagina, $datos_por_pagina ) ) );

		//die( print_r( $datos ) );
		while( $dato = $datos->fetch_assoc() ){
			call_user_func_array( array( $claseDato, 'Mostrar'), array_merge( array( $dato ), array_slice( $fMostrarDato, 1 )  ) );
		}

		$datos->close();

		$numDatos = call_user_func_array( $fContarDatos );

		$nPaginas = ceil( $numDatos/$datos_por_pagina );

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
?>
