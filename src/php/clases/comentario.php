<?php
	/*** 
	 comentario.php
	 Clase para el manejo de los comentarios subidos por los usuarios.
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

	class Comentario {
		/*** Atributos ***/
		protected $id;
		protected $perla;
		protected $usuario;
		protected $texto;
		protected $fecha_subida;
		protected $fecha_modificacion;


		/*** Getters y Setters ***/
		function ObtenerId(){ return $this->id; }
		function EstablecerId( $id ){ $this->id = $id; }

		function ObtenerPerla(){ return $this->perla; }
		function ObtenerUsuario(){ return $this->usuario; }
		function ObtenerTexto(){ return $this->texto; }
		
		function ObtenerFechaSubida(){ return $this->fecha_subida; }
		function ObtenerFechaModificacion(){ return $this->fecha_modificacion; }


		/*** Resto de metodos ***/

		// Carga los atributos a partir del registro asociativo $reg.
		function CargarDesdeRegistro( $reg )
		{
			// Salvo el campo 'texto', el resto de campos pueden estar o no
			// en el registro.
			$this->id = $reg['id'] = isset( $reg['id'] ) ? $reg['id'] : null;
			$this->perla = isset( $reg['perla'] ) ? $reg['perla'] : null;
			$this->usuario = isset( $reg['usuario'] ) ? $reg['usuario'] : null;
			$this->fecha_subida = isset( $reg['fecha_subida'] ) ? $reg['fecha_subida'] : null;
			$this->fecha_modificacion = isset( $reg['fecha_modificacion'] ) ? $reg['fecha_modificacion'] : null;

			$this->texto = $reg['texto'];
		}


		// Inserta en la BD $bd el comentario actual por parte del usuario
		// $usuario.
		function InsertarBD( $bd, $usuario )
		{
			if( $this->id == null ){ 
				// El comentario no tiene id definida y por tanto es nuevo.
				// Insertalo en la BD.
				$bd->Consultar( "INSERT INTO comentarios (perla, usuario, texto, fecha_subida, fecha_modificacion) VALUES ('{$this->perla}', '$usuario', '{$this->texto}', NOW(), NOW() )" );
			}else{
				// El comentario tiene id definida y por tanto ya existe en la BD.
				// Actualizalo.
				$bd->Consultar( "UPDATE comentarios SET texto='{$this->texto}', fecha_modificacion=NOW() WHERE id={$this->id}" );
			}
		}


		// Borra el comentario de la BD $bd.
		function BorrarBD( $bd ){
			$bd->Consultar( "DELETE FROM comentarios WHERE id={$this->id}" );
		}

	} // Fin de la clase Comentario.
?>
