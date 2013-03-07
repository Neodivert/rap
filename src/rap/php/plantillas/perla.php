
<!-- Perla -->
<div class="perla">
	<!-- Titulo -->
	<div class="div_cabecera_perla">
		<h1 class="izquierda"><?php echo $perla->ObtenerTitulo(); ?></h1>
		<p class="derecha">0/10 (0 votos)</p>
	</div>

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
				MostrarAvatar( $rap->ObtenerNombreUsuario( $participante ) );
			}
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
