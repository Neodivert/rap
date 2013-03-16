<?php
	// Comprueba que se ha elegido una perla para mostrar (variable GET).
	if( !isset( $_GET['perla'] ) ){
		die( 'No se ha seleccionado una perla' );
	}
?>

<!-- MUESTRA LA PERLA -->
<br /><br /><br /><br /><br /><br /><br /> <!-- TODO: QUITAR ESTO Y QUE EL TITULO DE LA PERLA NO SE META ENTRE EL MENU SUPERIOR Y EL BORDE DEL CONTENEDOR -->
<?php
	$perla = new Perla;
	$perla->CargarDesdeBD( BD::ObtenerInstancia(), $_GET['perla'], $_SESSION['id'] );

	require DIR_PLANTILLAS . 'perla.php';
?>

<!-- MUESTRA LOS COMENTARIOS -->
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
			echo "<br /> subido: {$comentario->ObtenerFechaSubida()} - modificado: {$comentario->ObtenerFechaModificacion()}";
			echo '</span>';

			if( $comentario->ObtenerUsuario() == $_SESSION['id'] ){
				CrearCabeceraFormulario( 'php/controladores/comentarios.php', 'post', 'Esta seguro/a de querer borrar este comentario?' );
				//echo "<form action=\"general.php?seccion=mostrar_perla&perla={$perla->id}\" method=\"post\" onsubmit=\"return confirm('Esta seguro/a de querer borrar este comentario?');\" >";
				echo "<input type=\"hidden\" name=\"comentario\" value=\"{$comentario->ObtenerId()}\" />";
				echo "<input type=\"hidden\" name=\"perla\" value=\"{$comentario->ObtenerPerla()}\" />";
				echo '<input type="submit" name="accion" value="Borrar comentario" />';
				echo '</form>';
				/*
				CrearCabeceraFormulario( 'php/controladores/comentarios.php', 'post' );
				echo "<input type=\"hidden\" name=\"comentario\" value=\"{$comentario->ObtenerId()}\" />";
				echo '<input type="submit" name="accion" value="Modificar comentario" />';
				echo '</form>';*/
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

<!-- FORMULARIO PARA UN NUEVO COMENTARIO -->
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

