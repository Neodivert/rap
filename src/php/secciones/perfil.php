<!-- TÍTULO CON NOMBRE DEL USUARIO -->
<h1><?php echo $usuario->ObtenerNombre(); ?></h1>

<!-- FORMULARIO PARA CAMBIAR LA CONTRASEÑA -->
<form id="form_perfil" action="php/controladores/usuarios.php" method="post">
	<h2 class="titulo_seccion">Cambiar contrase&ntilde;a </h2>

	<!-- Introducir contraseña -->
	<p>
	<label for="contrasenna">Escribe la nueva cotrase&ntilde;a: </label>
	<input type="password" name="contrasenna" id="contrasenna" />
	</p>

	<!-- Repetir contraseña -->
	<p>
	<label for="repetir_contrasenna">Repite la nueva contrase&ntilde;a: </label>
	<input type="password" name="repetir_contrasenna" id="repetir_contrasenna" />
	</p>

	<input type="hidden" name="accion" value="Cambiar contrase&ntilde;a" />

	<!-- Botón de submit -->
	<input type="button" value="Cambiar contrase&ntilde;a" onClick="CambiarContrasenna()" />
</form>

<h2 class="titulo_seccion">Cambiar avatar </h2>
<div class="galeria">	
<?php $rap->MostrarAvatar( $usuario->ObtenerId() ); ?>
</div>
<form class="enmarcado" id="form_avatar" action="php/controladores/usuarios.php" method="post" enctype="multipart/form-data">
	<input type="submit" name="accion" value="Borrar avatar" />
	<p>
	<label for="avatar">Subir avatar (Imagen png o jpg. Dimensiones maximas permitidas: 100x100): </label>
	<input type="file" name="avatar" id="avatar" /><br/>
	<input type="submit" name="accion" value="Cambiar avatar" />
	</p>
</form>

<? 
	$email = $usuario->ObtenerEmail();
	$cod_validacion_email = $usuario->ObtenerCodValidacionEmail();
 ?>
<h2 class="titulo_seccion">Email </h2>

<? if( ($email != null) && ($email != '') ){ ?>
	<p>Email actual: <? echo $email; ?></p>
	<? if( $cod_validacion_email != null ){ ?>
		<strong>Email no validado. Introduce aqu&iacute; el c&oacute;digo de validaci&oacute;n enviado a tu email.</strong>
		<form action="php/controladores/usuarios.php" method="post">
			<input type="text" name="cod_validacion_email" /><br/>
			<input type="submit" name="accion" value="Validar email" />
		</form>
	<? }else{ ?>
		<strong>Email validado</strong>
<? 
		}
	}
?>


<form action="php/controladores/usuarios.php" method="post">
	<label for="email">Introducir email: </label>
	<input type="email" name="email" /><br/>
	<input type="submit" name="accion" value="Establecer email" />
</form>

<?php /*
<h2 class="titulo_seccion">Notificaciones por email </h2>
<? 
if( $email && ($email != '') && ( !$cod_validacion_email ) ){ 
	$notificaciones = ObtenerNotificacionesEmail( $_SESSION['id'] );
?>
	<form id="form_notificaciones" action="controlador.php" method="post" >
		<fieldset>
			<legend>Notificar cuando se suban nuevas perlas</legend>
			<input type="radio" name="nueva_perla" value="siempre" <? if( $notificaciones['nueva_perla'] == 'siempre' ) echo 'checked'; ?> >Siempre<br>
			<input type="radio" name="nueva_perla" value="participante" <? if( $notificaciones['nueva_perla'] == 'participante' ) echo 'checked'; ?> >Sólo cuando soy participante<br>
			<input type="radio" name="nueva_perla" value="nunca" <? if( $notificaciones['nueva_perla'] == 'nunca' ) echo 'checked'; ?> >Nunca<br>
		</fieldset>
		<fieldset>
			<legend>Notificar cuando hayan nuevos comentarios</legend>
			<input type="radio" name="nuevo_comentario" value="siempre" <? if( $notificaciones['nuevo_comentario'] == 'siempre' ) echo 'checked'; ?> >Siempre<br>
			<input type="radio" name="nuevo_comentario" value="participante" <? if( $notificaciones['nuevo_comentario'] == 'participante' ) echo 'checked'; ?> >Sólo en perlas que he comentado<br>
			<input type="radio" name="nuevo_comentario" value="nunca" <? if( $notificaciones['nuevo_comentario'] == 'nunca' ) echo 'checked'; ?> >Nunca<br>
		</fieldset>
		<fieldset>
			<legend>Notificar cuando cambie la nota de una perla</legend>
			<input type="radio" name="cambio_nota" value="siempre" <? if( $notificaciones['cambio_nota'] == 'siempre' ) echo 'checked'; ?> >Siempre<br>
			<input type="radio" name="cambio_nota" value="participante" <? if( $notificaciones['cambio_nota'] == 'participante' ) echo 'checked'; ?> >Sólo en perlas que he votado<br>
			<input type="radio" name="cambio_nota" value="nunca" <? if( $notificaciones['cambio_nota'] == 'nunca' ) echo 'checked'; ?> >Nunca<br>
		</fieldset>
		<fieldset>
			<legend>Notificar cuando haya un nuevo raper@</legend>
			<input type="radio" name="nuevo_usuario" value="siempre" <? if( $notificaciones['nuevo_usuario'] == 'siempre' ) echo 'checked'; ?> >Siempre<br>
			<input type="radio" name="nuevo_usuario" value="nunca" <? if( $notificaciones['nuevo_usuario'] == 'nunca' ) echo 'checked'; ?> >Nunca<br>
		</fieldset>
		<input type="submit" name="accion" value="Establecer notificaciones" />
	</form>
<? }else{ ?>
	<p>Especifica y valida un email en la secci&oacute;n anterior para activar este formulario</p>
<? } ?> */ ?>
