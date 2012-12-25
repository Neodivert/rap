<?php
	// Página de perfil. La única funcionalidad actual es poder cambiar la contraseña.

	// El usuario pide cambiar su contraseña.
	if( isset( $_POST['contrasenna'] ) ){
		CambiarContrasenna( $_POST['contrasenna'] );

		// Si la contraseña pudo cambiarse, muestra un aviso al usuario.
		// CONFIRMAR QUE SE CAMBIA Y TENER EN CUENTA ERRORES.
		MostrarAviso( 'Contrasenna cambiada!' );
	}
?>

<!-- TÍTULO CON NOMBRE DEL USUARIO -->
<h1><?php echo $_SESSION['nombre'] ?></h1>

<!-- FORMULARIO PARA CAMBIAR LA CONTRASEÑA -->
<form id="form_perfil" action="general.php?seccion=perfil" method="post">
	<h2>Cambiar contrase&ntilde;a: </h2>

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

	<!-- Botón de submit -->
	<input type="button" name="cambiar_contrasenna" value="Cambiar contrase&ntilde;a" onClick="CambiarContrasenna()" />
</form>

