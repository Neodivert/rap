<?php
	// Conjunto de funciones relacionadas con los usuarios.
	require_once DIR_LIB . 'utilidades.php';
	require_once DIR_CLASES . 'objeto_bd.php';


	class Usuario implements ObjetoBD {
		protected $id;
		protected $nombre;
		
		public function ObtenerId(){ return $this->id; }
		public function EstablecerId( $id ){ $this->id = $id; }

		public function ObtenerNombre(){ return $this->nombre; }
		public function EstablecerNombre( $nombre ){ $this->nombre = $nombre; }

		// TODO: Completar.
		public function InsertarBD( $bd ){}
		//public function CargarDatos( $info ){}

		function Usuario( $id, $nombre )
		{
			$this->id = $id;
			$this->nombre = $nombre;
		}

		// Intenta logear al usuario cuyos nombre y contraseña son,
		// respectivamente, $nombre y $contrasenna. Devuelve true en caso de
		// éxito, o finaliza la ejecución con un mensaje en caso de error.
		public function Logear( $nombre, $contrasenna )	
		{	
			$bd = BD::ObtenerInstancia();

			$res = $bd->Consultar( "SELECT id, contrasenna from usuarios WHERE nombre='$nombre'" );
			$usuario = $res->fetch_object();

			if( !$usuario ){
				die( "ERROR: No se encontro ningun usuario [$nombre]" );
			}

			if( $usuario->contrasenna != $contrasenna ){
				die( "ERROR: Contrasenna incorrecta" );
			}

			$this->id = $usuario->id;
			$this->nombre = $nombre;			
			
			//$_SESSION['id'] = $usuario->id;
			return NULL;
		}


	} // Fin de la clase Usuario.

	


	


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
		 echo "<div class=\"div_avatar\"><img class=\"avatar\" width=\"100\" height=\"100\" src=\"$ruta\" alt=\"Avatar del usuario [$usuario]\" /><br />$usuario$num</div>";
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

	function ObtenerEmail( $usuario )
	{
		$bd = ConectarBD();

		$res = $bd->query( "SELECT email, cod_validacion_email FROM usuarios WHERE id='$usuario'" ) or die( $bd->error );

		$bd->close();

		$res = $res->fetch_array();
		return $res;
	}

	function EstablecerEmail( $usuario, $email )
	{
		$bd = ConectarBD();

		$res = $bd->query( "UPDATE usuarios SET email='$email' WHERE id='$usuario'" ) or die( $bd->error );
		$cod_validacion_email = $random_hash = md5(uniqid(rand(), true));
		$res = $bd->query( "UPDATE usuarios SET cod_validacion_email='$cod_validacion_email' WHERE id='$usuario'" ) or die( $bd->error );

		$bd->close();

		// Generar email de confirmacion
		$titulo = 'RAP - Validar email';
		$cuerpo = "Este es un mensaje para validar el email indicado en la RAP \r\n";
		$cuerpo .= "Entra en tu perfil e introduce el siguiente \r\n";
		$cuerpo .= "codigo de validacion en la seccion de email: \r\n";
		$cuerpo .= "$cod_validacion_email";
		$cuerpo .= "\r\n";
		$cuerpo = wordwrap($cuerpo, 70, "\r\n");

		ini_set('sendmail_from', 'neodivert@gmail.com' );
		if( !mail( $email, $titulo, $cuerpo ) ){
			die( 'Error enviando el email' );
		}
	}

	function ValidarEmail( $usuario, $cod_validacion_email )
	{
		$bd = ConectarBD();

		$res = $bd->query( "SELECT id, cod_validacion_email FROM usuarios WHERE id=$usuario AND cod_validacion_email='$cod_validacion_email'" ) or die( $bd->error );
	
		//die( "SELECT id, cod_validacion_email FROM usuarios WHERE id=$usuario AND cod_validacion_email='$cod_validacion_email'" );

		if( $res->fetch_array() ){
			$res = $bd->query( "UPDATE usuarios SET cod_validacion_email=NULL WHERE id=$usuario" ) or die( $bd->error );
			$bd->close();
			return true;
		}else{ 
			$bd->close();
			return false;
		}
	}

	function ObtenerNotificacionesEmail( $usuario )
	{
		$notificaciones = array(
			'nueva_perla' => 'nunca',
			'nuevo_comentario' => 'nunca',
			'cambio_nota' => 'nunca',
			'nuevo_usuario' => 'nunca'
		);
	
		$bd = ConectarBD();
		$notificaciones_ = $bd->query( "SELECT * FROM notificaciones_email WHERE usuario='$usuario'" ) or die( $bd->error );
		$bd->close();

		while( $notificacion_ = $notificaciones_->fetch_array() ){
			$notificaciones[$notificacion_['tipo']] = $notificacion_['frecuencia'];
		}	

		return $notificaciones;
	}

	function EstablecerNotificacionesEmail( $usuario, $notificaciones )
	{
		$bd = ConectarBD();
		
		foreach( $notificaciones as $tipo => $frecuencia ){
			if( $tipo != 'accion' ){
				if( $frecuencia != 'nunca' ){
					$res = $bd->query( "INSERT INTO notificaciones_email (usuario, tipo, frecuencia) VALUES ($usuario, '$tipo', '$frecuencia') ON DUPLICATE KEY UPDATE frecuencia='$frecuencia'" ) or die( $bd->error );
				}else{
					$res = $bd->query( "DELETE FROM notificaciones_email WHERE usuario='$usuario' AND tipo='$tipo'" ) or die( $bd->error );
				}
			}
		}
		
		$bd->close();
	}
?>
