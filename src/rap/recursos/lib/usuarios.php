<?php
	// Conjunto de funciones relacionadas con los usuarios.
	require_once DIR_LIB . 'utilidades.php';

	// Intenta logear al usuario cuyos nombre y contraseña son,
	// respectivamente, $nombre y $contrasenna. Devuelve true en caso de
	// éxito, o finaliza la ejecución con un mensaje en caso de error.
	function LogearUsuario( $nombre, $contrasenna )	
	{
		$bd = ConectarBD();

		$res = $bd->query( "SELECT id, contrasenna from usuarios WHERE nombre='$nombre'" ) or die( $bd->error );

		$bd->close();

		$usuario = $res->fetch_object();

		if( !$usuario ){
			die( "ERROR: No se encontro ningun usuario [$nombre]" );
		}

		if( $usuario->contrasenna != $contrasenna ){
			die( "ERROR: Contrasenna incorrecta" );
		}

		$_SESSION['id'] = $usuario->id;
		return true;
	}


	// Recupera de la BD el id y el nombre de los usuarios ordenados 
	// alfabéticamente por el nombre.
	function ObtenerUsuarios()
	{
		return ConsultarBD( "SELECT * from usuarios ORDER BY nombre ASC" );
	}


	// Actualiza en la BD la contraseña del usuario actual con la nueva 
	// contraseña $contrasenna.
	function CambiarContrasenna( $contrasenna )
	{
		$bd = ConectarBD();

		$res = $bd->query( "UPDATE usuarios SET contrasenna='$contrasenna' WHERE id='{$_SESSION['id']}' " ) or die( $bd->error );

		$bd->close();
	}


	function ObtenerTopSubidores( $n = 3, $mes=0, $anno=0 )
	{
		if( $mes == 0 ){
			return ConsultarBD( "SELECT usuarios.nombre, SUM(logros.num_perlas) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.num_perlas <> 0 GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
		}else{
			return ConsultarBD( "SELECT usuarios.nombre, logros.num_perlas AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno AND logros.num_perlas <> 0 ORDER BY n DESC LIMIT $n" );
		}
	}

	function ObtenerTopComentaristas( $n = 3, $mes=0, $anno=0 )
	{
		if( $mes == 0 ){
			return ConsultarBD( "SELECT usuarios.nombre, SUM(logros.num_comentarios) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.num_comentarios <> 0 GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
		}else{
			return ConsultarBD( "SELECT usuarios.nombre, logros.num_comentarios AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno AND logros.num_comentarios <> 0 ORDER BY n DESC LIMIT $n" );
		}
	}

	function ObtenerTopCalificadores( $n = 3, $mes=0, $anno=0 )
	{
		if( $mes == 0 ){
			return ConsultarBD( "SELECT usuarios.nombre, SUM(logros.num_perlas_calificadas) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.num_perlas_calificadas <> 0 GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
		}else{
			return ConsultarBD( "SELECT usuarios.nombre, logros.num_perlas_calificadas AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno AND logros.num_perlas_calificadas <> 0 ORDER BY n DESC LIMIT $n" );
		}
	}

	function ObtenerTopUsuarios( $n = 3, $mes=0, $anno=0 )
	{
		if( $mes == 0 ){
			return ConsultarBD( "SELECT usuarios.nombre, 3*SUM(logros.num_perlas)+2*SUM(logros.num_comentarios)+SUM(logros.num_perlas_calificadas) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
		}else{
			return ConsultarBD( "SELECT usuarios.nombre, 3*logros.num_perlas+2*logros.num_comentarios+logros.num_perlas_calificadas AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno ORDER BY n DESC LIMIT $n" );
		}
	}

	function MostrarAvatar( $usuario, $num = -1 )
	{
		 if( file_exists( 'media/avatares/' . $usuario ) ){
			$ruta = 'media/avatares/' . $usuario;
		 }else{
			$ruta = 'media/avatares/_default_.png';
		 }

		 if( $num == -1 ){
			$num = '';
		 }else{
			$num = ' (' . $num . ')';
		 }
		 echo "<div class=\"div_avatar\"><img class=\"avatar\" id=\"avatar\" width=\"100\" height=\"100\" src=\"$ruta\" /><br />$usuario$num</div>";
	}


	function InsertarAvatar( $imagen, $nombre )
	{
		if( $imagen['error'] == UPLOAD_ERR_NO_FILE ){
			echo 'Sin avatar subido';
			return;
		}

		try{
			ComprobarImagen( "avatar" );

			echo "Imagen: " . $imagen["name"] . "<br />";
			echo "Tipo: " . $imagen["type"] . "<br />";
			echo "Tamanno: " . ($imagen["size"] / 1024) . " Kb<br />";

			if( !move_uploaded_file($imagen["tmp_name"], "media/avatares/" . $nombre ) ) throw new Exception( 'ERROR moviendo fichero' );
			echo "Guardada en: " . $imagen["tmp_name"] . '<br />';
		}catch( Exception $e ){
			die( $e->getMessage() );
		}
	}
?>
