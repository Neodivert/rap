<?php
	// Conjunto de funciones relacionadas con los comentarios.
	
	class Comentario {
		protected $id;
		protected $perla;
		protected $usuario;
		protected $texto;
		protected $fecha_subida;
		protected $fecha_modificacion;

		function ObtenerId(){ return $this->id; }
		function EstablecerId( $id ){ $this->id = $id; }

		function ObtenerPerla(){ return $this->perla; }
		function ObtenerUsuario(){ return $this->usuario; }
		function ObtenerTexto(){ return $this->texto; }
		
		function ObtenerFechaSubida(){ return $this->fecha_subida; }
		function ObtenerFechaModificacion(){ return $this->fecha_modificacion; }

		function CargarDesdeRegistro( $registro )
		{
			if( isset( $registro['id'] ) ){
				$this->id = $registro['id'];
			}
			if( isset( $registro['perla'] ) ){
				$this->perla = $registro['perla'];
			}
			$this->texto = $registro['texto'];

			if( isset( $registro['usuario'] ) ){
				$this->usuario = $registro['usuario'];
			}
			if( isset( $registro['fecha_subida'] ) ){
				$this->fecha_subida = $registro['fecha_subida'];
			}
			if( isset( $registro['fecha_modificacion'] ) ){
				$this->fecha_modificacion = $registro['fecha_modificacion'];
			}
		}

		function InsertarBD( $bd, $usuario )
		{
			if( !isset( $this->id ) ){ 
				$bd->Consultar( "INSERT INTO comentarios (perla, usuario, texto, fecha_subida, fecha_modificacion) VALUES ('{$this->perla}', '$usuario', '{$this->texto}', NOW(), NOW() )" );
			}else{
				$bd->Consultar( "UPDATE comentarios SET texto='{$this->texto}', fecha_modificacion=NOW() WHERE id={$this->id}" );
			}
		}

		function BorrarBD( $bd ){
			$bd->Consultar( "DELETE FROM comentarios WHERE id={$this->id}" );
		}

	}

	/* TODO: Completar.
	function CargarDesdeBD( $bd, $id )
	{
		ConsultarBD( "SELECT * from comentarios WHERE perla=$id_perla ORDER BY fecha_subida ASC" );
	}

	// Recupera de la BD los comentarios de la perla cuya $id es $id_perla.
	function ObtenerComentarios( $id_perla )
	{
		return ConsultarBD( "SELECT * from comentarios WHERE perla=$id_perla ORDER BY fecha_subida ASC" );
	}


	// Inserta un nuevo comentario con el texto $texto en la perla cuya id es 
	// $id_perla.
	function InsertarComentario( $id_perla, $texto )
	{
		return ConsultarBD( "INSERT INTO comentarios (perla, usuario, texto, fecha_subida, fecha_modificacion) VALUES ('$id_perla', '{$_SESSION['id']}', '$texto', NOW(), NOW() )" );
	}


	// Modifica en la BD el comentario cuya id es $id_comentario con el nuevo 
	// texto $texto.
	function ModificarComentario( $id_comentario, $texto )
	{
		return ConsultarBD( "UPDATE comentarios SET texto='$texto', fecha_modificacion=NOW() WHERE id=$id_comentario" );
	}


	// Borra de la BD el comentario cuya id es $id_comentario.
	function BorrarComentario( $id_comentario )
	{
		return ConsultarBD( "DELETE FROM comentarios WHERE id=$id_comentario" );
	}
	*/
?>
