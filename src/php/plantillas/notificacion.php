<?php if( substr( $_GET['notificacion'], 0, 2) == 'OK' ){ ?>
	<div class="div_notificacion_buena">
	<p><?php echo $notificaciones_buenas[$_GET['notificacion']]; ?></p>
	</div>
<?php	}else{ ?>
	<div class="div_notificacion_mala">
	<p><?php echo $notificaciones_malas[$_GET['notificacion']]; ?></p>
	</div>
<?php } ?>
