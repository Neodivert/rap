<?php
	/***
	 subir_perla.php
	 Seccion para subir una perla o modificar una existente.
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

	// Se hace uso de la clase "Perla".
	require_once DIR_CLASES . 'perla.php';

	// VOY POR AQUI COMENTANDO.

	$perla = new Perla;
	// ¿El usuaruaio quiere subir una perla nueva o modificar una existente?
	if( isset( $_GET['modificar'] ) ){
		// El usuario quiere modificar una perla existente. La variable
		// $_GET['modificar'] contiene La id de la perla en cuestión.
		$perla->CargarDesdeBD( BD::ObtenerInstancia(), $_GET['modificar'], $_SESSION['id'] );
	}else{
		// El usuario va a subir una perla nueva. Rellena sus campos con
		// los valores por defecto.
		$perla->EstablecerTitulo( '' );
		$perla->EstablecerTexto( '' );
		$perla->EstablecerFecha( '' );
	
		$perla->EstablecerEtiquetas( array() );

		$perla->EstablecerParticipantes( "{$_SESSION['id']}" );
	}
?>



<!-- TÍTULO -->
<h1>Subir perla</h1>

<!-- FORMULARIO PARA SUBIR/MODIFICAR UNA PERLA -->
<form action="php/controladores/perlas.php" method="post" enctype="multipart/form-data">

	<!-- Id de la perla (solo cuando se trata de una actualizacion) -->
	<?php
		if( $perla->ObtenerId() ){
			echo "<input type=\"hidden\" name=\"id\" value=\"{$perla->ObtenerId()}\" />";
		}
	?>


	<!-- ¿Título de la perla? (campo de texto) -->
	<h2>T&iacute;tulo:</h2>
	<p>
	<?php
		echo "<input type=\"text\" name=\"titulo\" id=\"titulo\" value=\"{$perla->ObtenerTitulo()}\" required />";
	?>
	</p>

	<!-- Imagen (solo perlas visuales) -->
	<h2>Imagen (s&oacute;lo perlas visuales)</h2>
	<?php
		if( isset( $_GET['modificar'] ) && file_exists( "media/perlas/{$_GET['modificar']}" ) ){
			echo "<img src=\"media/perlas/{$_GET['modificar']}\" width=\"100%\" >";
			echo '<input type="checkbox" name="borrar_imagen" value="" />Borrar imagen<br />';
		}
	?>
	<label for="imagen">Cargar imagen: </label>
	<input type="file" name="imagen" id="imagen" />
	<!-- <a href="Javascript:void(0)" onclick="VaciarElemento('imagen')">Resetear campo de fichero</a> -->

	<!-- ¿Texto de la perla? (textarea) --> 
	<h2>Texto: </h2>
	<textarea name="texto" id="texto"><?php echo $perla->ObtenerTextoPlano(); ?></textarea>

	<!-- Etiquetas de la perla -->
	<h2>Etiquetas: </h2>
	<label for="etiquetas">Introduce las etiquetas separadas por comas. Por favor, usa palabras o frases simples que alguien pueda usar para buscar tu perla. Ejemplo de etiquetas: "pastelillo, g&eacute;minis, sub-woofer, napoleon":</label>
	<?php
		echo "<input type=\"text\" name=\"etiquetas\" id=\"etiquetas\" value=\"{$perla->ObtenerEtiquetasStr()}\" required />";
	?>

	<!-- ¿Fecha de la perla (cuándo ocurrió)? (campo de texto) -->
	<h2>Fecha de la perla (¿cu&aacute;ndo ocurri&oacute;?): </h2>
	<label for="fecha">Fecha de la perla (Si sabes el d&iacute;a concreto, ponlo como dd/mm/aaaa. Si no, pues una frase o lo que sea (p.e. "el año pasado"). Tambi&eacute;n se puede dejar vac&iacute;a: </label>
	<?php
		echo "<input type=\"text\" name=\"fecha\" id=\"fecha\" value=\"{$perla->ObtenerFecha()}\" />";
	?>

	<!-- TODO: Meter lo del contenido informatico y el humor negro (mediante etiquetas) -->
	
	<!-- Conjunto de campos "checkbox" para añadir participantes a la perla -->
	<h2>Participantes en la perla:</h2>
	<fieldset required>
		<?php 
			$usuarios = $rap->ObtenerUsuarios();

			if( !$usuarios ) die( 'Error: no se obtuvieron usuarios de la base de datos' );

			if( isset( $_GET['modificar'] ) ){
				foreach( $usuarios as $id_usuario => $nombre_usuario ){
					if( $id_usuario != $_SESSION['id'] ){
						echo "<input type=\"checkbox\" name=\"participantes[]\" value=\"{$id_usuario}\" ";
						if( $perla->EsParticipante( $id_usuario ) ){
							echo 'checked';
						}
						echo " />{$nombre_usuario} ({$id_usuario})<br />";
					}
				}
			}else{
				foreach( $usuarios as $id_usuario => $nombre_usuario ){
					if( $id_usuario != $_SESSION['id'] ){
						echo "<input type=\"checkbox\" name=\"participantes[]\" value=\"{$id_usuario}\" />{$nombre_usuario}<br />";
					}
				}
			}
		?>
	</fieldset>

	<!--
	<?php
		// Botón de "submit". Se llama a una función javascript que haga 
		// comprobaciones sobre el formulario antes de subir el formulario.
		echo '<input type="button" value="Subir Perla" ';
		echo "onclick=\"SubirPerla('{$_SESSION['id']}')\" ";
		echo '/>';
	?> -->

	<input type="submit" name="accion" value="Subir perla"/>
	
</form>
