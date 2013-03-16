<!-- TODO: Referenciar fuentes en algun lugar -->
<!-- http://commons.wikimedia.org/wiki/File:Gnome-edit-delete.svg -->

<!-- Perla -->
<div class="perla">
	<!-- Titulo -->
	<div class="div_cabecera_perla">
		<div class="izquierda">
			<h1><?php echo $perla->ObtenerTitulo(); ?></h1>
			<?php 
				$modificable = $perla->EsParticipante( $_SESSION['id'] );
				$hoy = date("Y-m-d H:i:s");
				$t2 = strtotime( $hoy );
				$t1 = strtotime( $perla->ObtenerFechaSubida() );
				$minutos = ($t2 - $t1)/60;

				if( $modificable ){
					CrearCabeceraFormulario( 'php/controladores/perlas.php', 'post' );
					echo "<input type=\"hidden\" name=\"perla\" value=\"{$perla->ObtenerId()}\" />";
					echo '<input type="submit" name="accion" value="Modificar perla" />';
					echo '</form>';

					CrearCabeceraFormulario( 'php/controladores/perlas.php', 'post', "Estas segur@ de querer BORRAR la perla [{$perla->ObtenerTitulo()}]?" );
					echo "<input type=\"hidden\" name=\"perla\" value=\"{$perla->ObtenerId()}\" />";
					echo '<input type="submit" name="accion" value="Borrar perla" />';
					echo '</form>';
				}
			?>
		</div>

		<div class="derecha">
			<h1 class="derecha"><?php echo "{$perla->ObtenerNota()}/10 ({$perla->ObtenerNumVotosPositivos()} votos)"; ?></h1>
				<?php 
				CrearCabeceraFormulario( 'php/controladores/perlas.php', 'post' );
				echo "<input type=\"hidden\" name=\"perla\" value=\"{$perla->ObtenerId()}\" />";
			?>
			<select name="nota">
				<option value="0">0 (cancelar voto)</option>
				<option value="1" >1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5" selected>5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
			</select>
			<input type="submit" name="accion" value="Puntuar Perla"> 
			</form>
		</div>
	</div>

	<!-- Nota media y nº de votos (si existen) -->
	<?php if( $perla->ObtenerNumVotos() != 0 ){ ?>
		<span class="subtexto">Nota media: <? echo "{$perla->ObtenerNota()}" ?> - N&uacute;mero de votos: <?php echo $perla->ObtenerNumVotos(); ?></span>
	<?php } ?>

	<!-- Cuerpo -->
	<div class="cuerpo_perla">
		<?php // TODO: Meter los avisos por etiquetas especiales (humor negro, spoiler, etc) ?>

		<!-- Texto -->
		<p><?php echo $perla->ObtenerTexto(); ?></p>

		<!-- Muestra la imagen (solo perlas visuales) -->

		<?php if( file_exists( "media/perlas/{$perla->ObtenerId()}" ) ){
			echo "<img src=\"media/perlas/{$perla->ObtenerId()}\" width=\"100%\" alt=\"perla visual - {$perla->ObtenerTitulo()} ({$perla->ObtenerId()})\" >";
		} ?>

		<!-- Subidor y fecha de subida. Ultimo modificador y fecha de modificacion. -->
		<span class="subtexto">
			Subida: <?php echo $perla->ObtenerFechaSubida(); ?> por <?php echo $rap->ObtenerNombreUsuario( $perla->ObtenerSubidor() ); ?><br />
			&Uacute;ltima modificaci&oacute;n: <?php echo $perla->ObtenerFechaModificacion(); ?> por <?php echo $rap->ObtenerNombreUsuario( $perla->ObtenerModificador() ); ?><br />
		</span>

		<!-- Participantes -->
		Participantes: 
		<div class="galeria">
		<?php
			$participantes = $perla->ObtenerParticipantes();
			foreach( $participantes as $participante ){
				if( $participante == $_SESSION['id'] ){
					// ¿El usuario actual tiene permisos para modificar la perla 
					// (es participante de la misma)?
					$modificable = true;
				}
				$rap->MostrarAvatar( $participante );
			}
		?>
		</div>

		<!-- Etiquetas -->
		<br/>Etiquetas: 
		<?php 
			$etiquetas = $perla->ObtenerEtiquetas();
			foreach( $etiquetas as $etiqueta ){
				echo "<a href=\"general.php?seccion=lista_perlas&etiquetas=$etiqueta\">$etiqueta</a>, ";
			}
		?><br />

		<!-- Si el usuario actual puede modificar/borrar la perla actual se
		le muestran los botones para hacerlo -->
		<?php
			/*
			$hoy = date("Y-m-d H:i:s");
			$t2 = strtotime( $hoy );
			$t1 = strtotime( $perla->ObtenerFechaSubida() );
			$minutos = ($t2 - $t1)/60;

			CrearCabeceraFormulario( 'php/controladores/perlas.php', 'post', 1 );
			echo "<input type=\"hidden\" name=\"perla\" value=\"{$perla->ObtenerId()}\" />";
			if( $modificable && ($minutos < 30) ){
				echo "<input type=\"submit\" name=\"accion\" value=\"Borrar perla\" onsubmit=\"return confirm('Estas segur@ de querer BORRAR la perla [{$perla->ObtenerTitulo()}]?')\" />";
			}else{
				$n = 3 - $perla->ObtenerNumVotosNegativos();
				if( $perla->ObtenerNumVotosNegativos() ){
					echo "({$perla->ObtenerNumVotosNegativos()} persona(s) ha(n) votado para eliminar esta perla - $n votos restantes)<br/>";
				}else{
					echo "(0 persona(s) ha(n) votado para eliminar esta perla - $n votos restantes)<br/>";
				}
				if( $perla->DenunciadaPorUsuario() ){
					echo 'Has votado para borrar esta perla: ';
					echo '<input type="submit" name="accion" value="Cancelar voto borrado" />';
				}else{
					$denuncias = $perla->ObtenerNumVotosNegativos();
					echo "<input type=\"hidden\" name=\"num_denuncias\" value=\"$denuncias\" />";
					echo '<input type="submit" name="accion" value="Denunciar perla" />';
					//echo '<input type="submit" name="accion" value="Denunciar perla 2" />';
				}
			}
			echo '</form>'; */
			echo "<br /><a href=\"Javascript:void(0)\" onclick=\"MostrarPerla('{$perla->ObtenerId()}')\">Comentar Perla (comentarios: {$perla->ObtenerNumComentarios()})</a>";
			echo '<br />';
			// Formulario (select + botón) para votar la perla.
			//GenerarFormularioVoto( $perla->ObtenerId() );
		?>

		


	</div> <!-- Fin del cuerpo de la perla -->
</div> <!-- Fin de la perla -->
