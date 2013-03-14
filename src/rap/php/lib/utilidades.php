<?php
	require_once DIR_CONFIG . 'bd.php';

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
?>
