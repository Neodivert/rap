<?php
	/*** 
	 notificacion.php
	 Plantilla que muestra al usuario una caja (div) con un mensaje de exito o 
	 error segun el valor de $_GET['notificacion']. Los posibles valores para
	 esta variable se definen en php/config/parametros.php.
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

	// "Requires" necesarios.
	require_once 'php/config/rutas.php';
	require_once DIR_CONFIG . 'parametros.php';

	// Se muestra una notificacion "buena" si $_GET['notificacion'] contiene
	// la substring "OK". En caso contrario se muestra una notificacion "mala".
?>
<?php if( substr( $_GET['notificacion'], 0, 2) == 'OK' ){ ?>
	<div class="div_notificacion_buena">
	<p><?php echo $notificaciones_buenas[$_GET['notificacion']]; ?></p>
	</div>
<?php	}else{ ?>
	<div class="div_notificacion_mala">
	<p><?php echo $notificaciones_malas[$_GET['notificacion']]; ?></p>
	</div>
<?php } ?>
