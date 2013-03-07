<?php
	// Sección para mostrar una perla y sus comentarios

	// Comprueba que se ha elegido una perla para mostrar (variable GET).
	if( !isset( $_GET['perla'] ) ){
		die( 'No se ha seleccionado una perla' );
	}

	// El usuario quiere puntuar una perla (DUPLICADO).
	if( isset( $_POST['nota'] ) ){
		PuntuarPerla( $_POST['id_perla'], $_POST['nota'] );
	}
?>

<!-- MUESTRA LA PERLA -->
<?php
	$perla = new Perla;
	$perla->CargarDesdeBD( $_GET['perla'], BD::ObtenerInstancia() );

	require DIR_PLANTILLAS . 'perla.php';
?>

<?php /*
<!-- MUESTRA LOS COMENTARIOS -->
<h1>Comentarios</h1>
<?php
	$comentarios = ObtenerComentarios( $_GET['perla'] );

	// La variable "$par" se usa para saber si el comentario actual es par o
	// impar. El interés radica en dar a los comentarios pares e impares
	// estilos distintos.
	$par = true;
	while( $comentario = $comentarios->fetch_object() ){
		if( $par )
			echo "<div id=\"c_{$comentario->id}\" class=\"comentario_par\">";
		else
			echo "<div id=\"c_{$comentario->id}\" class=\"comentario_impar\">";

		echo "<p>{$comentario->texto}</p>";
		echo "<span class=\"fecha\">";
		echo $usuarios[$comentario->usuario];
		echo "<br /> subido: {$comentario->fecha_subida} - modificado: {$comentario->fecha_modificacion}";
		echo '</span>';

		if( $comentario->usuario == $_SESSION['id'] ){
			CrearCabeceraFormulario( 'php/controladores/comentarios.php', 'post', 1 );
			//echo "<form action=\"general.php?seccion=mostrar_perla&perla={$perla->id}\" method=\"post\" onsubmit=\"return confirm('Esta seguro/a de querer borrar este comentario?');\" >";
			echo "<input type=\"hidden\" name=\"comentario\" value=\"{$comentario->id}\" />";
			echo '<input type="submit" name="accion" value="Borrar comentario" />';

			CrearCabeceraFormulario( 'php/controladores/comentarios.php', 'post' );
			//echo "<form action=\"general.php?seccion=mostrar_perla&perla={$perla->id}\" method=\"post\" onsubmit=\"return confirm('Esta seguro/a de querer borrar este comentario?');\" >";
			echo "<input type=\"hidden\" name=\"comentario\" value=\"{$comentario->id}\" />";
			echo '<input type="submit" name="accion" value="Modificar comentario" />';

			//echo "<input type=\"button\" value=\"Modificar comentario\" onclick=\"ModificarComentario('{$comentario->id}', '{$comentario->texto}')\" />";
			echo '</form>';
		}

		echo '</div>';
		
		// Alterna de un comentario par a uno impar o viceversa.
		$par = !$par;
	}
?>

<!-- FORMULARIO PARA UN NUEVO COMENTARIO -->
<h2>Nuevo comentario</h2>
<?php CrearCabeceraFormulario( 'php/controladores/comentarios.php', 'post' ); ?>
	<label for="texto_comentario">Texto: </label>
	<textarea name="texto_comentario" id="texto_comentario"></textarea>
	<br />
	<?php
		echo "<input type=\"hidden\" name=\"perla\" value=\"{$_GET['perla']}\" />";
	?>
	
	<input type="submit" name="accion" value="Subir comentario" />
</form>
*/ ?>
