// Funciones javascript relacionadas con las perlas.

/*          Funciones relacionadas con los participantes de una perla        */
/*****************************************************************************/

// Las funciones siguientes trabajan con dos elementos principales del 
// formulario para subir/actualizar una perla: la lista de participantes ya
// añadidos (elemento ul) y el select con todos los usuarios para añadir 
// nuevos participantes a la lista anterior.

// Añade un participante (id, nombre) a la lista de participantes.
function AnnadirParticipante( id, nombre )
{
	// Evita que el usuario introduzca dos veces el mismo participante.
	if( document.getElementById( 'p_' + id ) ){
		alert( 'El participante ya se ha annadido' );
		return;
	}

	// Accede a la lista de participantes ya añadidos (elemento ul).
	var lista_participantes = document.getElementById( 'lista_participantes' );

	// Crea un elemento de la lista (li) con el nuevo participante y el enlace
	// para eliminarlo.
	var participante = document.createElement( 'li' );
	participante.setAttribute( 'id', 'p_' + id );
	participante.innerHTML = nombre + " <a href=\"javascript:void(0)\" onclick=\"EliminarParticipante('" + id + "', '" + nombre + "')\">[x]</a>";

	// Añade el nuevo elemento a la lista de participantes.
	lista_participantes.appendChild( participante );
}


// Elimina el participante cuya id de usuario es 'id' de la lista.
function EliminarParticipante( id )
{
	// Acede a la lista de participantes actuales.
	var lista_participantes = document.getElementById( 'lista_participantes' );

	// Accede al elemento de la lista con el participante a eliminar.
	var x = document.getElementById( 'p_' + id );

	// Elimina el elemento.
	lista_participantes.removeChild( x );
}


// Busca el siguiente hermano del nodo DOM 'nodo' que sea de tipo 1.
function SiguienteHermano( nodo )
{
	do{
		nodo = nodo.nextSibling;
	}while( nodo && (nodo.nodeType != 1) );

	return nodo;
}


// Busca el primer hijo del nodo DOM 'nodo' que sea de tipo 1.
function PrimerHijo( nodo ){
	nodo = nodo.firstChild;

	while( nodo && (nodo.nodeType != 1) ){
		nodo = nodo.nextSibling;
	}

	return nodo;
}


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

	// Obtiene el elemento 'ul' con la lista de participantes.
	var lista_participantes = document.getElementById( 'lista_participantes' );

	// Fuerza a que al menos se haya elegido un participante.
	var aux = PrimerHijo( lista_participantes );
	if( !aux ){
		alert( 'Debes especificar al menos un participante' );
		return;
	}

	// Si el usuario que sube la perla no se ha elegido como participante,
	// muestra un aviso.
	if( !document.getElementById( 'p_' + id_usuario ) ){
		if( !confirm( "No estas en la lista de participantes, por lo que no podras modificar la perla en el futuro. Continuar?" ) ) return;
	}


	// El formulario tiene un campo oculto nombrado "participantes". Se
	// rellena con un array de id's de participantes de la forma 
	// p1,p2,p3, ...
	var strParticipantes = document.getElementById( 'participantes' );
	
	var participante = PrimerHijo( lista_participantes );

	// string de la forma p1,p2,p3 que se guardará en el campo oculto.
	var str = '';
	
	while( participante ){
		str += (participante.getAttribute( 'id' )).substring( 2 ) + ',';
		participante = SiguienteHermano( participante );
	}

	// Ya se tiene la string de la forma p1,p2,p3. Se guarda en el campo
	// oculto.
	var participantes = document.getElementById( 'participantes' );
	participantes.setAttribute( 'value',  str );

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


/*                               Otras funciones                             */
/*****************************************************************************/
/*
function ResetearCampoFichero( nombre )
{
	var elemento = document.getElementById( nombre );
	elemento.parent.innerHTML
	alert( elemento.value );
	elemento.reset();
	alert( elemento.value );
}*/
