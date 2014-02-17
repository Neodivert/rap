<?php
	/***
	 perfil.php
	 Seccion que muestra el perfil privado del usuario actual.
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

	// Se requiere la clase Notificador.
	require_once 'php/config/rutas.php';
	require_once DIR_CLASES . 'notificador.php';
?>

<!-- TÍTULO CON NOMBRE DEL USUARIO -->
<h1><?php echo $usuario->ObtenerNombre(); ?></h1>

<!-- FORMULARIO PARA CAMBIAR LA CONTRASEÑA -->
<form id="form_cambio_contrasenna" action="php/controladores/usuarios.php" method="post" onSubmit="return ValidarCambioContrasenna();" >
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
	<input type="submit" name="accion" value="Cambiar contrase&ntilde;a" />
</form>

<!-- FORMULARIO PARA CAMBIAR EL AVATAR -->
<h2 class="titulo_seccion">Cambiar avatar </h2>
<!-- Avatar actual -->
<div class="galeria">	
	<?php $rap->MostrarAvatar( $usuario->ObtenerId() ); ?>
</div>
<!-- Cambiar avatar -->
<form class="enmarcado" id="form_avatar" action="php/controladores/usuarios.php" method="post" enctype="multipart/form-data">
	<input type="submit" name="accion" value="Borrar avatar" />
	<p>
	<label for="avatar">Subir avatar (Imagen png o jpg. Dimensiones maximas permitidas: 100x100): </label>
	<input type="file" name="avatar" id="avatar" /><br/>
	<input type="submit" name="accion" value="Cambiar avatar" />
	</p>
</form>

<!-- FORMULARIO PARA ESTABLECER/VALIDAR EMAIL -->
<? 
	// Obtiene el email y el codigo de validacion actuales.
	$email = $usuario->ObtenerEmail();
	$cod_validacion_email = $usuario->ObtenerCodValidacionEmail();
 ?>
<h2 class="titulo_seccion">Email </h2>

<? if( ($email != null) && ($email != '') ){ ?>
	<!-- Email actual -->
	<p>Email actual: <? echo $email; ?></p>
	<? if( $cod_validacion_email != null ){ ?>
		<!-- Formulario para introducir el codigo de validacion -->
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

<!-- Formulario para establecer un nuevo email -->
<form action="php/controladores/usuarios.php" method="post">
	<label for="email">Introducir email: </label>
	<input type="email" name="email" /><br/>
	<input type="submit" name="accion" value="Establecer email" />
</form>

<!-- FORMULARIO PARA ESTABLECER LAS NOTIFICACIONES POR EMAIL -->
<h2 class="titulo_seccion">Notificaciones por email </h2>
<?php 
if( $email && ($email != '') && ( !$cod_validacion_email ) ){ 
	// El usuario dispone de un email validado, carga sus preferencias sobre
	// notificaciones email y muestraselas en un formulario.
	$notificador = Notificador::ObtenerInstancia();
	$notificaciones = $notificador->ObtenerPreferenciasBD( BD::ObtenerInstancia(), $usuario->ObtenerId() );
?>
	<form id="form_notificaciones" action="php/controladores/usuarios.php" method="post" >
		<!-- Notificaciones por nueva perla -->
		<fieldset>
			<legend>Notificar cuando se suban nuevas perlas</legend>
			<input type="radio" name="nueva_perla" value="siempre" <? if( $notificaciones['nueva_perla'] == 'siempre' ) echo 'checked'; ?> >Siempre<br>
			<input type="radio" name="nueva_perla" value="participante" <? if( $notificaciones['nueva_perla'] == 'participante' ) echo 'checked'; ?> >Sólo cuando soy participante<br>
			<input type="radio" name="nueva_perla" value="nunca" <? if( $notificaciones['nueva_perla'] == 'nunca' ) echo 'checked'; ?> >Nunca<br>
		</fieldset>

		<!-- Notificaciones por nuevo comentario -->
		<fieldset>
			<legend>Notificar cuando hayan nuevos comentarios</legend>
			<input type="radio" name="nuevo_comentario" value="siempre" <? if( $notificaciones['nuevo_comentario'] == 'siempre' ) echo 'checked'; ?> >Siempre<br>
			<input type="radio" name="nuevo_comentario" value="participante" <? if( $notificaciones['nuevo_comentario'] == 'participante' ) echo 'checked'; ?> >Sólo en perlas en las que soy participante, he comentado o votado<br>
			<input type="radio" name="nuevo_comentario" value="nunca" <? if( $notificaciones['nuevo_comentario'] == 'nunca' ) echo 'checked'; ?> >Nunca<br>
		</fieldset>

		<!-- Notificaciones por cambio de nota en una perla -->
		<fieldset>
			<legend>Notificar cuando cambie la nota de una perla</legend>
			<input type="radio" name="nueva_nota" value="siempre" <? if( $notificaciones['nueva_nota'] == 'siempre' ) echo 'checked'; ?> >Siempre<br>
			<input type="radio" name="nueva_nota" value="participante" <? if( $notificaciones['nueva_nota'] == 'participante' ) echo 'checked'; ?> >Sólo en perlas en las que soy participante, he comentado o votado<br>
			<input type="radio" name="nueva_nota" value="nunca" <? if( $notificaciones['nueva_nota'] == 'nunca' ) echo 'checked'; ?> >Nunca<br>
		</fieldset>

		<!-- Notificaciones por nuevo raper@ -->
		<fieldset>
			<legend>Notificar cuando haya un nuevo raper@</legend>
			<input type="radio" name="nuevo_usuario" value="siempre" <? if( $notificaciones['nuevo_usuario'] == 'siempre' ) echo 'checked'; ?> >Siempre<br>
			<input type="radio" name="nuevo_usuario" value="nunca" <? if( $notificaciones['nuevo_usuario'] == 'nunca' ) echo 'checked'; ?> >Nunca<br>
		</fieldset>
		<input type="submit" name="accion" value="Establecer notificaciones" />
	</form>
<?php 
	}else{ 
		// El usuario no dispone de email o este no esta validado. Se le muestra
		// un aviso.
?>
	<p>Especifica y valida un email en la secci&oacute;n anterior para activar este formulario</p>
<?php } ?>
