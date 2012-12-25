<?php
	require_once DIR_LIB . 'usuarios.php';
?>

<h1>Lista de Usuarios</h1>

<?php
	$usuarios = ObtenerUsuarios();

	echo '<ul>';
	while( $usuario = $usuarios->fetch_assoc() ){
		$fecha_registro = FormatearFecha( $usuario['fecha_registro'] );
		echo "<li><strong>{$usuario['nombre']}</strong> - Fecha registro: $fecha_registro</li>";
	}
	echo '</ul>';
?>
