<?php
	/*** 
	 utilidades.php
	 Funciones auxiliares.
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
	require_once DIR_CONFIG . 'bd.php';


	// Imprime la cabecera html de un formulario, estableciendo 
	// action="$controlador" y method="$metodo". Ademas, incluye un mensaje de
	// confirmacion (del tipo "estas seguro?) si se especifica 
	// ($mentaje_confirmacion).
	// TODO: ¿Lo estoy usando?.
	function CrearCabeceraFormulario( $controlador, $metodo, $mensaje_confirmacion = NULL ){
		echo "<form action=\"{$controlador}\" method=\"$metodo\" ";
		if( $mensaje_confirmacion != NULL ){
			echo "onsubmit=\"return confirm('$mensaje_confirmacion')\" ";
		}
		echo '>';
	}


	// Redirige al usuario a su ultima direccion visitada. Si $notificacion != 
	// null se le muestra al usuario la notificacion indexada por $notificacion
	// en el destino.
	function RedirigirUltimaDireccion( $notificacion = null ){
		if( strrpos( $_SESSION['ultima_dir'], '?' ) == false ){
			header( "Location: {$_SESSION['ultima_dir']}?notificacion=$notificacion" );
		}else{
			header( "Location: {$_SESSION['ultima_dir']}&notificacion=$notificacion" );
		}
		exit();
	}


	// Devuelve una string explicativa para el error de fichero con el código 
	// $codigo. El error 4 (No se subió fichero) no se contempla.
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


	// Comprueba que la imagen que se ha subido es válida.
	// En caso de éxito devuelve 0, y si hay un error devuelve un codigo 
	// negativo.
	function ComprobarImagen( $imagen )
	{
		$tipos_soportados = array( 'image/jpeg', 'image/png' );

		// ¿Hubo algún error en la subida?. El error 4 (No se subió fichero) ya
		// se tiene en cuenta antes de intentar subir el fichero.
		if( $imagen['error'] > 0 ){
			return -1;
		}

		// Comprueba que el tipo mime de la imagen es jpeg o png.
		// Contribución de renato en la ayuda de php.
		$finfo = new finfo( FILEINFO_MIME );
		$tipo_imagen = $finfo->file( $imagen['tmp_name'] );
		$tipo_mime = substr( $tipo_imagen, 0, strpos($tipo_imagen, ';') );
		//$tipo_imagen = mime_content_type( $_FILES[$nombre]['tmp_name'] );
		if( !in_array( $tipo_mime, $tipos_soportados ) ){
			return -2;
		}

		return 0;
	}
?>
