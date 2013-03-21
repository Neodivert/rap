<?php
	require_once DIR_CONFIG . 'bd.php';

	function CrearCabeceraFormulario( $controlador, $method, $mensaje_confirmacion = NULL ){
		echo "<form action=\"{$controlador}\" method=\"$method\" ";
		if( $mensaje_confirmacion != NULL ){
			echo "onsubmit=\"return confirm('$mensaje_confirmacion')\" ";
		}
		echo '>';
	}

	function RedirigirUltimaDireccion( $notificacion = null ){
		if( strrpos( $_SESSION['ultima_dir'], '?' ) == false ){
			header( "Location: {$_SESSION['ultima_dir']}?notificacion=$notificacion" );
		}else{
			header( "Location: {$_SESSION['ultima_dir']}&notificacion=$notificacion" );
		}
		exit();
	}

	// Devuelve una string explicativa para el error de fichero con el código $codigo.
	// El error 4 (No se subió fichero) no se contempla.
	function MostrarErrorFichero( $codigo )
	{
		$max_tam_imagen = ini_get( 'upload_max_filesize' );
		$mensajes_error = array(
			UPLOAD_ERR_INI_SIZE => "El tama&ntilde;o del fichero sobrepasa el m&aacute;ximo definido ($max_tam_imagen)",
			UPLOAD_ERR_FORM_SIZE => 'El tama&ntilde;o del fichero sobrepasa el m&aacute;ximo definido en el formulario HTML',
			UPLOAD_ERR_PARTIAL => 'S&oacute;lo se carg&oacute; parte del archivo',
			UPLOAD_ERR_NO_TMP_DIR => 'No se encuentra el directorio temporal',
			UPLOAD_ERR_CANT_WRITE => 'No se puede escribir en disco',
			UPLOAD_ERR_EXTENSION => 'Una extensi&oacute;n PHP par&oacute; la subida del fichero'
		);
		return 'ERROR SUBIENDO FICHERO: ' . $mensajes_error[$codigo] . '<br />';
	}

	// Comprueba que el fichero que se ha subido es válido.
	// En caso de éxito no devuelve nada, y si hay un error lanza una excepción.
	function ComprobarImagen( $imagen )
	{
		$tipos_soportados = array( 'image/jpeg', 'image/png' );

		// ¿Hubo algún error en la subida?. El error 4 (No se subió fichero) ya
		// se tiene en cuenta antes de intentar subir el fichero.
		if( $imagen['error'] > 0 ){
			return -1;
			//throw new Exception( 'ERROR: ' . MostrarErrorFichero( $imagen['error'] ) );
		}

		// Comprueba que el tipo mime de la imagen es jpeg o png.
		// Contribución de renato en la ayuda de php.
		$finfo = new finfo( FILEINFO_MIME );
		$tipo_imagen = $finfo->file( $imagen['tmp_name'] );
		$tipo_mime = substr( $tipo_imagen, 0, strpos($tipo_imagen, ';') );
		//$tipo_imagen = mime_content_type( $_FILES[$nombre]['tmp_name'] );
		if( !in_array( $tipo_mime, $tipos_soportados ) ){
			return -2;
			//throw new Exception( 'ERROR: tipo de imagen no soportado. Tipos soportados: jpeg, png' );
		}

		return 0;
	}
?>
