// Funciones javascript relacionadas con las perlas.

/*                     Funciones relacionadas con las perlas                 */
/*****************************************************************************/

// Valida el formulario para subir/actualizar una perla. Si todo está correcto
// envía el formulario.
function SubirPerla( id_usuario )
{
	var formulario = document.forms['form_subir_perla'];
	var string;
	var camposTexto=['titulo_perla', 'texto_perla'];

	// Comprueba que ciertos campos de texto no estén vacios
	for( var i=0; i<camposTexto.length; i++ ){
		string = formulario[camposTexto[i]].value;

		if( string == '' || string == null ){
			alert( 'ERROR: No has especificado - ' + camposTexto[i] );
			return;
		}
	}


	// Sube el formulario.
	formulario.submit();
}

/*                   Funciones relacionadas con los comentarios              */
/*****************************************************************************/

function ModificarComentario( id, texto )
{	
	// Encuentra el div del comentario y límpialo.
	var div_comentario = document.getElementById( 'c_' + id );
	div_comentario.innerHTML = '';

	// Crea un formulario.
	var formulario = document.createElement( 'form' );
	formulario.setAttribute( 'method', 'post' );

	// Crea un textarea con el texto del comentario.
	var campo_texto = document.createElement( 'textarea' );
	campo_texto.setAttribute( 'name', 'texto' );
	campo_texto.innerHTML = texto;

	// Crea un campo oculto con el id del comentario.
	var campo_oculto = document.createElement( 'input' );
	campo_oculto.setAttribute( 'type', 'hidden' );
	campo_oculto.setAttribute( 'name', 'comentario' );
	campo_oculto.setAttribute( 'value', id );

	// Crea un botón de submit.
	var submit = document.createElement( 'input' );
	submit.setAttribute( 'type', 'submit' );
	submit.setAttribute( 'name', 'modificar_comentario' );
	submit.setAttribute( 'value', 'Modificar comentario' );
	
	// Añade los campos al formulario.
	formulario.appendChild( campo_texto );
	formulario.appendChild( campo_oculto );
	formulario.appendChild( submit );
	
	// Añade el formulario al div del comentario.
	div_comentario.appendChild( formulario );
}

/*                Funciones para cambiar la página/sección actual            */
/*****************************************************************************/

function CambiarCategoria( categoria )
{
	window.location.href = 'general.php?seccion=lista_perlas&categoria=' + categoria;
}

function ModificarPerla( id_perla ){
	window.location.href = 'general.php?seccion=subir_perla&modificar=' + id_perla;
}

function MostrarPerla( id_perla )
{
	window.location.href = 'general.php?seccion=mostrar_perla&perla=' + id_perla;
}


