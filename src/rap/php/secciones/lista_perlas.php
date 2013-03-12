<?php
	//Lista de perlas. Las perlas se muestran por categorías y páginas
	/*
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
	*/

	// Las perlas se muestran por etiquetas.
	$etiquetas = isset( $_GET['etiquetas'] ) ? $_GET['etiquetas'] : '';

	// Las perlas se muestran por páginas. Página por defecto: 0.
	$pagina_actual = isset( $_GET['pagina'] ) ? $_GET['pagina'] : 0;
    
	// Las perlas se pueden mostrar por participantes. Participante por 
  	// defecto: 0 (cualquier participante).
	$participante = isset( $_GET['participante'] ) ? $_GET['participante'] : 0;
?>

<!-- TÍTULO -->



<h1>Lista de Perlas</h1>

<!--                           Barra de busqueda                            -->
<h2>Buscar perlas</h2>
<div id="barra_busqueda" class="barra">

	<!-- TODO: Completar 
	<input list="browsers">

	<datalist id="browsers">
	  <option value="Internet Explorer">
	  <option value="Firefox">
	  <option value="Chrome">
	  <option value="Opera">
	  <option value="Safari">
	</datalist> -->
	
	<p>Introduce una palabra o una frase sencilla</p>
	<form action="general.php" method="get">
		<input list="etiquetas" name="etiquetas">
		<input type="submit" value="Buscar perlas" />
	</form>

	<?php
	/*die( '3. Comentar perlas (¿clase comentario?)<br />4. Buscar etiquetas (datalist con etiquetas mas populares)<br/>5. Buscar etiquetas (¿una o mas de una?) / incluir boton para buscar cualquiera<br />5.5 RESTO DE SECCIONES<br/>6. ¿Publicar?<br/>6.5. Denunciar perlas<br/>7. ¿Avisos? (humor negro, informatico, etc) ¿tabla BD (etiqueta, aviso)?' );*/
	
	/*<form id="form_busqueda" method="get">

		<!-- Busqueda. Seleccion de categoria 
		<label for="categoria">Categor&iacute;a: </label>
  		<select name="categoria" id="categoria" >
		<?php
			$categorias = $rap->ObtenerCategorias();
			$total_perlas = 0;
		
			// Bucle que itera a lo largo de las categorías y va creando las
			// opciones del select.
			while( $categoria = $categorias->fetch_object() ){
				echo "<option ";
				if( $categoria->id == $_GET['categoria'] ){
					// Si la categoría coincide con la actual, muestra esta 
					// opción como preseleccionada.
					echo 'selected="selected" ';
				}
            echo "value=\"{$categoria->id}\" >{$categoria->nombre} ({$categoria->num_perlas})</option>";

				// Cada categoría en la BD tiene guardado el número de perlas
				// de la misma para evitar consultar la BD y contar. El total
				// de perlas, sin embargo, se va calculando a medida que se 
				// recuperan las categorías de la BD.
				$total_perlas += $categoria->num_perlas;
			}

			// Muestra una última opción 'Cualquiera' en el select.
			echo "<option ";
			if( $_GET['categoria'] == 0 ){
				echo 'selected="selected" ';
			}
			echo "value=\"0\">Cualquiera ($total_perlas)</option>";
		?>
		</select> -->

		<!-- Busqueda. Seleccion de participante 
		<label for="participante">Participante</label>
		<select name="participante" id="participante">
      	<?php
            $usuarios = $rap->ObtenerUsuarios();
            while( $usuario = $usuarios->fetch_object() ){
              	echo '<option ';
              	if( $usuario->id == $_GET['participante'] ){
						// Si el participante coincide con el actual, muestra esta 
						// opción como preseleccionada.
						echo 'selected="selected" ';
					}
          		echo "value=\"{$usuario->id}\">";
            	echo $usuario->nombre;
            	echo '</option>';
            }

            // Muestra una última opción 'Cualquiera' en el select.
				echo "<option ";
				if( $_GET['participante'] == 0 ){
					echo 'selected="selected" ';
				}
				echo "value=\"0\">Cualquiera</option>";
        		?>
     	</select> -->

		<!-- Busqueda. Seleccion de flags 
		<?php
			echo '<br /><input type="checkbox" name="contenido_informatico" value="0" ';
			if( $_GET['contenido_informatico'] == 0 ){
				echo 'checked';
			}
			echo '/>NO quiero perlas con "humor inform&aacute;tico"';
			echo '<br /><input type="checkbox" name="humor_negro" value="1" ';
			if( $_GET['humor_negro'] == 0 ){
				echo 'checked';
			}
			echo '/>NO quiero perlas con humor negro';
			
		?>
		<br />
      <input type="submit" value="Buscar Perlas" />
	</form> --> */ ?>
</div>


<!--                           Lista de perlas                              -->
<?php 
	$bd = BD::ObtenerInstancia();

	// Obtiene las perlas de la pagina actual.
	$perlas = $rap->ObtenerPerlas( $_SESSION['id'], $etiquetas, $participante, $pagina_actual*5, 5 );

	// Obtiene el numero de perlas.
	$nElementos = $bd->ObtenerNumFilasEncontradas();
	
	$nElementosPorPagina = 5;

	//echo "Filas encontradas: " . $bd->ObtenerNumFilasEncontradas();
	
	foreach( $perlas as $perla ){
		$modificable = false;
		require DIR_PLANTILLAS . 'perla.php';
	} // Fin del while que recorre las perlas.
	
	// Crea los enlaces a las otras páginas
	if( isset( $_GET['notificacion'] ) ){
		unset( $_GET['notificacion'] );
	}
	
	require DIR_PLANTILLAS . 'selector_paginas.php';
?>
