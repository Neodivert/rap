<?php
	/*** 
	 usuario.php
	 Clase singlenton para gestionar al usuario actual.
	Fuente: 
	http://www.cristalab.com/tutoriales/crear-e-implementar-el-patron-de-diseno-singleton-en-php-c256l/
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

	require_once DIR_LIB . 'utilidades.php';

	class Usuario {
		/*** Atributos ***/

		// Instancia privada (singlenton).
   	private static $instancia;

		protected $id;
		protected $nombre;
		protected $email;
		protected $cod_validacion_email;


		/*** Getters y setters ***/
		public function ObtenerId(){ return $this->id; }
		public function EstablecerId( $id ){ $this->id = $id; }

		public function ObtenerNombre(){ return $this->nombre; }
		public function EstablecerNombre( $nombre ){ $this->nombre = $nombre; }

		public function ObtenerEmail(){ return $this->email; }
		public function EstablecerEmail( $email ){ $this->email = $email; }

		function ObtenerCodValidacionEmail(){ return $this->cod_validacion_email; }
		function EstablecerCodValidacionEmail( $cod_validacion_email ){ $this->cod_validacion_email = $cod_validacion_email; }

		/*** Metodos ***/

		// Constructor privado (singlenton). Carga desde la BD la informacion del 
		// usuario con id $id.
   	private function Usuario( $id )
		{
			$this->CargarDesdeBD( BD::ObtenerInstancia(), $id );
		}


		// Obtiene la instancia unica (singlenton).
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
			// Obtiene desde la BD la informacion del usuario con nombre $nombre.
			$bd = BD::ObtenerInstancia();
			$res = $bd->Consultar( "SELECT * from usuarios WHERE nombre='$nombre'" );
			$usuario = $res->fetch_object();

			// Si no se obtuvo ningun usuario con ese nombre muestra un mensaje
			// de error.
			if( !$usuario ){
				die( "ERROR: No se encontro ningun usuario [$nombre]" );
			}

			// Si la contrasenna no coincide con la guardada en la BD muestra un
			// mensaje de error.
			if( $usuario->contrasenna != $contrasenna ){
				die( "ERROR: Contrasenna incorrecta" );
			}

			// Usuario logueado correctamente. Guarda su id en una variable de
			// sesion y devuelve 0.
			$_SESSION['id'] = $usuario->id;
			return 0;
		}


		// Actualiza en la BD la contraseña del usuario actual con la nueva 
		// contraseña $contrasenna.
		function CambiarContrasennaBD( $bd, $contrasenna )
		{
			// Actualiza la contrasenna en la BD.
			$bd->Consultar( "UPDATE usuarios SET contrasenna='$contrasenna' WHERE id='{$this->ObtenerId()}' " );

			// Actualiza la contrasenna en el propio objeto.
			$this->contrasenna = $contrasenna;
		}


		// Carga desde la BD $bd la informacion del usuario con id $id_usuario.
		function CargarDesdeBD( $bd, $id_usuario )
		{
			// Carga la id del usuario.
			$this->id = $id_usuario;

			// Carga la informacion del usuario desde la BD.
			$res = $bd->Consultar( "SELECT * from usuarios WHERE id='$id_usuario'" );
			$registro = $res->fetch_assoc();
			$this->CargarDesdeRegistro( $registro );
		}


		// Carga la informacion del usuario desde el registro asociativo $reg.
		function CargarDesdeRegistro( $reg )
		{
			$this->EstablecerNombre( $reg['nombre'] );
			$this->EstablecerEmail( $reg['email'] );

			if( isset( $reg['cod_validacion_email'] ) ){
				$this->EstablecerCodValidacionEmail( $reg['cod_validacion_email'] );
			}else{
				$this->EstablecerCodValidacionEmail( null );
			}
		}

		
		// Carga en disco la imagen $imagen como avatar del usuario actual.
		function InsertarAvatarBD( $imagen )
		{
			// No se ha subido imagen, devuelve error.
			if( $imagen['error'] == UPLOAD_ERR_NO_FILE ){
				return -1;
			}

			try{
				// Comprobaciones varias (formato, tamanno de imagen, etc).
				ComprobarImagen( $imagen );

				// Mueve la imagen a media/avatares.
				if( !move_uploaded_file($imagen["tmp_name"], "../../media/avatares/" . $this->id ) ) return -2;
			}catch( Exception $e ){
				die( $e->getMessage() );
			}

			return 0;
		}

	
		// Borra de disco el avatar del usuario actual.
		function BorrarAvatarBD()
		{
			unlink( "../../media/avatares/" . $this->id );
		}


		// Establece el email $email para el usuario actual. Se genera un codigo
		// de validacion que se enviara al correo del usuario.
		function EstablecerEmailBD( $bd, $email )
		{
			$this->EstablecerEmail( $email );

			// Actualiza el email del usuario en la BD.
			$res = $bd->Consultar( "UPDATE usuarios SET email='{$this->email}' WHERE id='{$this->id}'" );
	
			// Genera un codigo de validacion aleatorio y lo guarda en la BD.
			$this->EstablecerCodValidacionEmail( md5(uniqid(rand(), true)) );
			$res = $bd->Consultar( "UPDATE usuarios SET cod_validacion_email='{$this->cod_validacion_email}' WHERE id='{$this->id}'" );

			// Genera el email de confirmacion
			// TODO: ¿Implementar esto en la clase Notificador?.
			$titulo = 'RAP - Validar email';
			$cuerpo = "Este es un mensaje para validar el email indicado en la RAP \r\n";
			$cuerpo .= "Entra en tu perfil e introduce el siguiente \r\n";
			$cuerpo .= "codigo de validacion en la seccion de email: \r\n";
			$cuerpo .= "{$this->cod_validacion_email}";
			$cuerpo .= "\r\n";
			$cuerpo = wordwrap($cuerpo, 70, "\r\n");

			// Envia el email de confirmacion.
			ini_set('sendmail_from', 'neodivert@gmail.com' );
			if( !mail( $this->email, $titulo, $cuerpo ) ){
				die( 'Error enviando el email' );
			}
		}


		// Valida el email del usuario con el codigo $cod_validacion_email.
		function ValidarEmailBD( $bd, $cod_validacion_email )
		{
			if( $this->cod_validacion_email == $cod_validacion_email ){
				// Codigo de validacion correcto. 
				// Cambia el codigo de validacion a NULL en la BD. Esta es la forma
				// de expresar que el email esta validado.
				$res = $bd->Consultar( "UPDATE usuarios SET cod_validacion_email=NULL WHERE id={$this->id}" );
				// Al validar un email se inserta un registro en la BD con unas
				// preferencias por defecto relativas a las notificaciones por 
				// email que desea recibir el usuario.
				$bd->Consultar( "INSERT IGNORE INTO notificaciones_email ( usuario ) VALUES( {$this->id} )" );
				return 0;
			}else{
				// El codigo de validacion no es correcto. Devuelve eror.
				return -1;
			}
		}

	} // Fin de la clase Usuario.
?>
