<?php
	// Conjunto de funciones relacionadas con los comentarios.

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
?>
