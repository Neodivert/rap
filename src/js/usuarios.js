/*** 
 usuarios.js
 Funciones javascript relacionadas con los usuarios.
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

// Valida el formulario de login codificando el campo "contrasenna".
function ValidarLogin ()
{
	// Codifica la contraseña.
	var contrasenna = document.forms["form_login"]["contrasenna"].value;
	document.forms["form_login"]["contrasenna"].value = hex_md5( contrasenna );

	// Formulario validado.
	return true;
}


// Valida el formulario para cambiar la contraseña de usuario actual.
function ValidarCambioContrasenna()
{
	// Accede a los campos "contrasenna" y "repetir_contrasenna".
	c1 = document.forms["form_cambio_contrasenna"]["contrasenna"];
	c2 = document.forms["form_cambio_contrasenna"]["repetir_contrasenna"];

	// Comprueba que los valores de los campos "contrasenna" y 
	// "repetir_contrasenna" coincidan.
	if( c1.value != c2.value ){
		alert( 'Las contrasennas no coinciden, torpe!' );
		return false;
	}

	// Codifica la contraseña.
	c1.value = hex_md5( c1.value );
	
	// Formulario validado.
	return true;
}
