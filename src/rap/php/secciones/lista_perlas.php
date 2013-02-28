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

	// Las perlas se muestran por categorías. Categoria por defecto: 0 
	// (cualquier categoría).
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

	<?php /*
	<!-- <form id="form_busqueda" method="get">

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
	// Obtiene los nombres de los usuarios y los mete en un array.
	$rUsuarios = $rap->ObtenerUsuarios();
	$usuarios = array();
	while( $rUsuario = $rUsuarios->fetch_object() ){
		$usuarios[$rUsuario->id] = $rUsuario->nombre;
	}

	$bd = BD::ObtenerInstancia();

	// Obtiene las perlas de la pagina actual.
	$perlas = $rap->ObtenerPerlas( $_SESSION['id'], $etiquetas, $participante, $pagina_actual*5, 5 );

	// Obtiene el numero de perlas.
	$nPerlas = $bd->ObtenerNumFilasEncontradas();

	echo "Filas encontradas: " . $bd->ObtenerNumFilasEncontradas();

	foreach( $perlas as $perla ){
		//$perla = new Perla;
		//$perla->CargarDesdeRegistro( $regPerla );

		$modificable = false;
?>
		<!-- Perla -->
		<div class="perla">
			<!-- Titulo -->
			<h1><?php echo $perla->ObtenerTitulo(); ?></h1>
	
			<!-- Nota media y nº de votos (si existen) -->
			<?php if( $perla->ObtenerNumVotos() != 0 ){ ?>
				<span class="subtexto">Nota media: <? echo $perla->ObtenerNotaMedia(); ?> - N&uacute;mero de votos: <?php echo $perla->ObtenerNumVotos(); ?></span>
			<?php } ?>

			<!-- Cuerpo -->
			<div class="cuerpo_perla">
				<!-- Aviso de contenido informatico (si procede) -->
				<?php if( $perla->ObtenerContenidoInformatico() ){ ?>
					<span class="subtexto"><strong>Nota: La perla tiene contenido inform&aacute;tico</strong></span>
				<?php } ?>

				<!-- Aviso de humor negro (si procede) -->
				<?php if( $perla->ObtenerHumorNegro() ){ ?>
					<span class="subtexto"><strong>Nota: La perla tiene humor negro y/o salvajadas</strong></span>
				<?php } ?>

				<!-- Texto -->
				<p><?php echo $perla->ObtenerTexto(); ?></p>

				<!-- Muestra la imagen (solo perlas visuales) -->
				<?php if( $perla->ObtenerPerlaVisual() ){
					echo "<img src=\"media/perlas/{$perla->ObtenerId()}\" width=\"100%\" alt=\"perla visual - {$perla->ObtenerTitulo()}\" >";
				} ?>

				<!-- Subidor y fecha de subida. Ultimo modificador y fecha de modificacion. -->
				<span class="subtexto">
					Subida: <?php echo $perla->ObtenerFechaSubida(); ?> por <?php echo $usuarios[$perla->ObtenerSubidor()]; ?><br />
					&Uacute;ltima modificaci&oacute;n: <?php echo $perla->ObtenerFechaModificacion(); ?> por <?php echo $usuarios[$perla->ObtenerModificador()]; ?><br />
				</span>

				<!-- Participantes -->
				Participantes: 
				<div class="galeria">
				<?php
					$participantes = $perla->ObtenerParticipantes( BD::ObtenerInstancia() );
					while( $participante = $participantes->fetch_object() ){
						if( $participante->usuario == $_SESSION['id'] ){
							// ¿El usuario actual tiene permisos para modificar la perla 
							// (es participante de la misma)?
							$modificable = true;
						}
						MostrarAvatar( $usuarios[$participante->usuario] );
						//echo "{$usuarios[$participante->usuario]}, ";
					}
					$participantes->close();
				?>
				</div>


				<!-- Etiquetas -->
				Etiquetas: 
				<?php 
					$etiquetas = $perla->ObtenerEtiquetas();
					foreach( $etiquetas as $etiqueta ){
						echo $etiqueta . ', ';
					}
				?><br />

					

				<!-- Si el usuario actual puede modificar/borrar la perla actual se
				le muestran los botones para hacerlo -->
				<?php
					$hoy = date("Y-m-d H:i:s");
					$t2 = strtotime( $hoy );
					$t1 = strtotime( $perla->ObtenerFechaSubida() );
					$minutos = ($t2 - $t1)/60;

					// Modificar la perla.
					if( $modificable ){
						CrearCabeceraFormulario( 'php/controladores/perlas.php', 'post' );
						echo "<input type=\"hidden\" name=\"perla\" value=\"{$perla->ObtenerId()}\" />";
						echo '<input type="submit" name="accion" value="Modificar perla" />';
						echo '</form>';
					}
					CrearCabeceraFormulario( 'php/controladores/perlas.php', 'post', 1 );
					echo "<input type=\"hidden\" name=\"perla\" value=\"{$perla->ObtenerId()}\" />";
					if( $modificable && ($minutos < 30) ){
						echo '<input type="submit" name="accion" value="Borrar perla" />';
					}else{
						$n = 3 - $perla->ObtenerNumDenuncias();
						if( $perla->ObtenerNumDenuncias() ){
							echo "({$perla->ObtenerNumDenuncias()} persona(s) ha(n) votado para eliminar esta perla - $n votos restantes)<br/>";
						}else{
							echo "(0 persona(s) ha(n) votado para eliminar esta perla - $n votos restantes)<br/>";
						}
						if( $perla->ObtenerDenunciada() ){
							echo 'Has votado para borrar esta perla: ';
							echo '<input type="submit" name="accion" value="Cancelar voto borrado" />';
						}else{
							$denuncias = $perla->ObtenerNumDenuncias();
							echo "<input type=\"hidden\" name=\"num_denuncias\" value=\"$denuncias\" />";
							echo '<input type="submit" name="accion" value="Denunciar perla" />';
							//echo '<input type="submit" name="accion" value="Denunciar perla 2" />';
						}
					}
					echo '</form>';
					echo "<br /><a href=\"Javascript:void(0)\" onclick=\"MostrarPerla('{$perla->ObtenerId()}')\">Comentar Perla (comentarios: {$perla->ObtenerNumComentarios()})</a>";
					echo '<br />';
					// Formulario (select + botón) para votar la perla.
					GenerarFormularioVoto( $perla->ObtenerId() );
				?>
			</div> <!-- Fin del cuerpo de la perla -->
		</div> <!-- Fin de la perla -->
<?php
	} // Fin del while que recorre las perlas.
	// Libera los recursos.
	$rUsuarios->close();
	
	// Crea los enlaces a las otras páginas
	$get = $_GET; ?>
	<div id="seleccion_paginas">
		<?php
			$nPaginas = $nPerlas / 5;
		for( $pagina=0; $pagina<$nPaginas; $pagina++ ){
			$get['pagina'] = $pagina;
			$getArray = http_build_query( $get );
			if( $pagina != $pagina_actual ){
				echo "<a href=\"" . $_SERVER["PHP_SELF"] . '?' . $getArray . "\" >";
				echo "$pagina</a> ";
			}else{
				echo $pagina . ' ';
			}
		}
		?>
	</div>

