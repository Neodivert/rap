<?php
	/***
	 mostrar_perla.php
	 Seccion que muestra la perla con id $_GET['perla'] y sus comentarios 
	 asociados.
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

	// Comprueba que se ha elegido una perla para mostrar (variable GET).
	// TODO: Rediriguir a lista_perlas y mostrar una notificacion "mala".
	if( !isset( $_GET['perla'] ) ){
		die( 'No se ha seleccionado una perla' );
	}

	// Se hace uso de las clases "Perla" y "Comentario".
	require_once 'php/config/rutas.php';
	require_once DIR_CLASES . 'perla.php';
	require_once DIR_CLASES . 'comentario.php';
?>


<!-- Perla -->
<?php
	// Carga los datos de la perla desde la BD.
	$perla = new Perla;
	$perla->CargarDesdeBD( BD::ObtenerInstancia(), $_GET['perla'], $_SESSION['id'] );

	// Muestra la perla.
	require DIR_PLANTILLAS . 'perla.php';
?>

<!-- Comentarios -->
<h1>Comentarios</h1>
<?php
	$comentarios = $perla->ObtenerComentariosBD( BD::ObtenerInstancia() );

	// La variable "$par" se usa para saber si el comentario actual es par o
	// impar. El interés radica en dar a los comentarios pares e impares
	// estilos distintos.
	$par = true;
	foreach( $comentarios as $comentario ){
		if( $par ){
			echo "<div id=\"c_{$comentario->ObtenerId()}\" class=\"comentario_par\">";
		}else{
			echo "<div id=\"c_{$comentario->ObtenerId()}\" class=\"comentario_impar\">";
		}
		//$rap->MostrarAvatar( $comentario->ObtenerUsuario() );
		if( isset( $_GET['comentario'] ) && ( $comentario->ObtenerId() == $_GET['comentario'] ) ){
			CrearCabeceraFormulario( 'php/controladores/comentarios.php', 'post' );
			echo "<input type=\"hidden\" name=\"id\" value=\"{$comentario->ObtenerId()}\" />";
			echo "<input type=\"hidden\" name=\"perla\" value=\"{$comentario->ObtenerPerla()}\" />";
			echo "<textarea name=\"texto\">{$comentario->ObtenerTexto()}</textarea>";
			echo "<input type=\"submit\" name=\"accion\" value=\"Modificar comentario\" />";
			echo '</form>';
		}else{
			echo "<p>{$comentario->ObtenerTexto()}</p>";
			echo "<span class=\"fecha\">";
			echo $rap->ObtenerNombreUsuario( $comentario->ObtenerUsuario() );
			echo "<br />Subido: {$comentario->ObtenerFechaSubida()}<br/>";
			echo "&Uacute;ltima modificaci&oacute;n: {$comentario->ObtenerFechaModificacion()}";
			echo '</span>';

			if( $comentario->ObtenerUsuario() == $_SESSION['id'] ){
				// Formulario para borrar el comentario actual (solo si el usuario
				// actual es quien subio el comentario).
				CrearCabeceraFormulario( 'php/controladores/comentarios.php', 'post', 'Esta seguro/a de querer borrar este comentario?' );
				echo "<input type=\"hidden\" name=\"comentario\" value=\"{$comentario->ObtenerId()}\" />";
				echo "<input type=\"hidden\" name=\"perla\" value=\"{$comentario->ObtenerPerla()}\" />";
				echo '<input type="submit" name="accion" value="Borrar comentario" />';
				echo '</form>';

				// Formulario para modificar el comentario actual (solo si el usuario
				// actual es quien subio el comentario).
				echo "<form action=\"general.php\" >";
				echo '<input type="hidden" name="seccion" value="mostrar_perla" />';
				echo "<input type=\"hidden\" name=\"perla\" value=\"{$comentario->ObtenerPerla()}\" />";
				echo "<input type=\"hidden\" name=\"comentario\" value=\"{$comentario->ObtenerId()}\" />";
				echo '<input type="submit" value="Modificar comentario" />';
				echo '</form>';

			}
		}
		echo '</div>';
		
		// Alterna de un comentario par a uno impar o viceversa.
		$par = !$par;
	}
?>

<!-- FORMULARIO PARA SUBIR UN NUEVO COMENTARIO -->
<h2>Nuevo comentario</h2>
<?php CrearCabeceraFormulario( 'php/controladores/comentarios.php', 'post' ); ?>
	<label for="texto">Texto: </label>
	<textarea name="texto" id="texto"></textarea>
	<br />
	<?php
		echo "<input type=\"hidden\" name=\"perla\" value=\"{$_GET['perla']}\" />";
	?>
	
	<input type="submit" name="accion" value="Subir comentario" />
</form>

