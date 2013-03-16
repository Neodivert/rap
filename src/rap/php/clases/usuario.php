<?php
	// Conjunto de funciones relacionadas con los usuarios.
	require_once DIR_LIB . 'utilidades.php';
	require_once DIR_CLASES . 'objeto_bd.php';


	class Usuario {
   	private static $instancia;

		protected $id;
		protected $nombre;
		protected $email;
		protected $cod_validacion_email;

		// Constructor privado.
   	private function Usuario( $id )
		{
			$this->CargarDesdeBD( BD::ObtenerInstancia(), $id );
		}

		// Obtiene la instancia unica.
   	public static function ObtenerInstancia( $id )
		{
      	if( !self::$instancia instanceof self ){
				self::$instancia = new self( $id );
			}
			return self::$instancia;
   	}

		// Intenta logear al usuario cuyos nombre y contraseña son,
		// respectivamente, $nombre y $contrasenna. Devuelve true en caso de
		// éxito, o finaliza la ejecución con un mensaje en caso de error.
		public static function Loguear( $nombre, $contrasenna )
		{	
			$bd = BD::ObtenerInstancia();

			$res = $bd->Consultar( "SELECT * from usuarios WHERE nombre='$nombre'" );
			$usuario = $res->fetch_object();

			if( !$usuario ){
				die( "ERROR: No se encontro ningun usuario [$nombre]" );
			}

			if( $usuario->contrasenna != $contrasenna ){
				die( "ERROR: Contrasenna incorrecta" );
			}

			/*
			$this->id = $usuario->id;
			$this->nombre = $nombre;	
			$this->email = $usuario->email;		
			*/

			$_SESSION['id'] = $usuario->id;

			return 0;
		}



		public function ObtenerId(){ return $this->id; }
		public function EstablecerId( $id ){ $this->id = $id; }

		public function ObtenerNombre(){ return $this->nombre; }
		public function EstablecerNombre( $nombre ){ $this->nombre = $nombre; }

		public function ObtenerEmail(){ return $this->email; }

		function ObtenerCodValidacionEmail(){ return $this->cod_validacion_email; }


		// Actualiza en la BD la contraseña del usuario actual con la nueva 
		// contraseña $contrasenna.
		function CambiarContrasennaBD( $bd, $contrasenna )
		{
			$bd->Consultar( "UPDATE usuarios SET contrasenna='$contrasenna' WHERE id='{$this->ObtenerId()}' " );

			$this->contrasenna = $contrasenna;
		}

		function CargarDesdeBD( $bd, $id )
		{
			$this->id = $id;

			$res = $bd->Consultar( "SELECT * from usuarios WHERE id='$id'" );

			$registro = $res->fetch_assoc();
		
			$this->nombre = $registro['nombre'];
			$this->email = $registro['email'];
			if( isset( $registro['cod_validacion_email'] ) ){
				$this->cod_validacion_email = $registro['cod_validacion_email'];
			}else{
				$this->cod_validacion_email = null;
			}
		}

		// TODO: Completar.
		//public function InsertarBD( $bd, $id_usuario ){}
		//public function CargarDatos( $info ){}
		function InsertarAvatarBD( $imagen )
		{
			if( $imagen['error'] == UPLOAD_ERR_NO_FILE ){
				return -1;
			}

			try{
				ComprobarImagen( $imagen );
				if( !move_uploaded_file($imagen["tmp_name"], "../../media/avatares/" . $this->id ) ) return -2;
			}catch( Exception $e ){
				die( $e->getMessage() );
			}

			return 0;
		}

		function BorrarAvatarBD()
		{
			unlink( "../../media/avatares/" . $this->id );
		}

		function EstablecerEmailBD( $bd, $email )
		{
			$this->email = $email;

			$res = $bd->Consultar( "UPDATE usuarios SET email='{$this->email}' WHERE id='{$this->id}'" );
			$this->cod_validacion_email = $random_hash = md5(uniqid(rand(), true));
			$res = $bd->Consultar( "UPDATE usuarios SET cod_validacion_email='{$this->cod_validacion_email}' WHERE id='{$this->id}'" );

			// Generar email de confirmacion
			$titulo = 'RAP - Validar email';
			$cuerpo = "Este es un mensaje para validar el email indicado en la RAP \r\n";
			$cuerpo .= "Entra en tu perfil e introduce el siguiente \r\n";
			$cuerpo .= "codigo de validacion en la seccion de email: \r\n";
			$cuerpo .= "{$this->cod_validacion_email}";
			$cuerpo .= "\r\n";
			$cuerpo = wordwrap($cuerpo, 70, "\r\n");

			ini_set('sendmail_from', 'neodivert@gmail.com' );
			if( !mail( $this->email, $titulo, $cuerpo ) ){
				die( 'Error enviando el email' );
			}
		}

		function ValidarEmailBD( $bd, $cod_validacion_email )
		{
			if( $this->cod_validacion_email == $cod_validacion_email ){
				$res = $bd->Consultar( "UPDATE usuarios SET cod_validacion_email=NULL WHERE id={$this->id}" );
				return 0;
			}else{
				return -1;
			}
		}

	} // Fin de la clase Usuario.
	
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
