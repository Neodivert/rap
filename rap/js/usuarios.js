// Funciones javascript relacionadas con los usuarios.

// Valida el formulario de login.
function ValidarLogin ()
{
	// Comprueba que el campo con el nombre no esté vacío.
	var nombre = document.forms["form_login"]["nombre"].value;
	if( nombre == null | nombre == '' ){
		alert( 'ERROR: Nombre no introducido' );
		return false;
	}

	// Comprueba que el campo con la contraseña no esté vacío.
	var contrasenna = document.forms["form_login"]["contrasenna"].value;
	if( contrasenna == null | contrasenna == '' ){
		alert( 'ERROR: Contrasenna no introducida' );
		return false;
	}

	// Codifica la contraseña.
	document.forms["form_login"]["contrasenna"].value = hex_md5( contrasenna );

	// Formulario validado.
	return true;
}

// Valida el formulario para cambiar la contraseña de usuario y lo envía si es 
// correcto.
function CambiarContrasenna()
{
	// Accede a los campos "contrasenna" y "repetir_contrasenna".
	c1 = document.forms["form_perfil"]["contrasenna"];
	c2 = document.forms["form_perfil"]["repetir_contrasenna"];

	// Comprueba que el campo con la contraseña no esté vacío. 
	if( !c1.value || c1.value == null ){
		alert( 'Introduce una contrasenna!' );
		return;
	}

	// Comprueba que los valores de los campos "contrasenna" y 
	// "repetir_contrasenna" coincidan.
	if( c1.value != c2.value ){
		alert( 'Las contrasennas no coinciden, torpe!' );
		return;
	}

	// Codifica la contraseña.
	c1.value = hex_md5( c1.value );
	
	// Envía el formulario.
	document.forms["form_perfil"].submit();
}
