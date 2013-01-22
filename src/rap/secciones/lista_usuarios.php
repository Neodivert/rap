<?php
	require_once DIR_LIB . 'usuarios.php';
?>

<h1>Lista de Usuarios</h1>
<div class="galeria">

<?php
	$usuarios = ObtenerUsuarios();

	while( $usuario = $usuarios->fetch_assoc() ){
		MostrarAvatar( $usuario['nombre'] );
	}
	/*	
	echo '<ul>';
	while( $usuario = $usuarios->fetch_assoc() ){
		$fecha_registro = FormatearFecha( $usuario['fecha_registro'] );
		echo "<li><strong>";
		MostrarAvatar( $usuario['nombre'] );
		echo "</strong> - Fecha registro: $fecha_registro</li>";
	}
	echo '</ul>';
	*/
?>

</div>
