<?php
	/*** 
	 rap.php
	 Clase singlenton para gestionar la RAP.
	Fuente: 
	http://www.cristalab.com/tutoriales/crear-e-implementar-el-patron-de-diseno-singleton-en-php-c256l/
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

	// Configuracion de la conexion a la BD.
	require_once DIR_CONFIG . 'bd.php';

	// Clase para acceder a la BD.
	require_once 'bd.php';


	class RAP {
		/*** Atributos ***/

		// Instancia privada (singlenton).
   	private static $instancia;

		// Base de datos.
		private $bd;

		// Array de la forma [id_usuario] => nombre_usuario.
		protected $usuarios;


		/*** Metodos ***/

		// Constructor privado (singlenton).
   	private function __construct(){
			$this->bd = BD::ObtenerInstancia();
			$this->bd->Configurar( $GLOBALS['datos_bd']['host'], 
						 		  		  $GLOBALS['datos_bd']['usuario'],
						 		  		  $GLOBALS['datos_bd']['contrasenna'],
						 	 	  		  $GLOBALS['datos_bd']['bd'] );
			$this->CargarUsuarios();
		}

		// Obtiene la instancia unica (singlenton).
   	static function ObtenerInstancia()
		{
      	if( !self::$instancia instanceof self ){
				self::$instancia = new self;
			}
			return self::$instancia;
   	}


		// Recupera de la BD el id y el nombre de los usuarios y los guarda en
		// un array interno de la forma [id_usuario] => nombre_usuario.
		function CargarUsuarios()
		{
			$rUsuarios = $this->bd->Consultar( "SELECT * from usuarios ORDER BY nombre ASC" );
			$this->usuarios = array();
			while( $rUsuario = $rUsuarios->fetch_object() ){
				$this->usuarios[$rUsuario->id] = $rUsuario->nombre;
			}
		}


		// Devuelve el array de usuarios.
		function ObtenerUsuarios(){
			return $this->usuarios;
		}


		// Devuelve el nombre del usuario con id $id_usuario.
		function ObtenerNombreUsuario( $id_usuario )
		{
			return $this->usuarios[$id_usuario];
		}


		// Busca en la BD las perlas que tengan la etiqueta $etiquetas y a 
		// $participante como participante. De entre todos los resultados,
		// devuelve $n perlas a partir de la posicion $offset.
		function ObtenerPerlas( $id_usuario, $etiquetas = '', $participante = 0, $offset = 0, $n = 0 )
		{
			// Comienza a construir la consulta a la BD segun el valor de los 
			// distintos argumentos suministrados.
			$consulta = 'SELECT SQL_CALC_FOUND_ROWS perlas.*, etiquetas.nombre FROM perlas ';
			$consulta .= 'LEFT JOIN perlas_etiquetas ON perlas.id = perlas_etiquetas.perla ';
			$consulta .= 'LEFT JOIN etiquetas ON perlas_etiquetas.etiqueta = etiquetas.id ';

			// TODO: ¿Ampliar a busqueda por multiples etiquetas y ordenar por relevancia?.
			if( $etiquetas != '' ){
				$consulta .= "WHERE etiquetas.nombre = '$etiquetas' ";
			}

			$consulta .= ' GROUP BY perlas.id ';

			$consulta .= 'ORDER BY id DESC ';

			// Si se especifica devuelve como maximo $n perlas a partir de la 
			// posicion $offset.
			if( $n != 0 ){
				$consulta .= "LIMIT $offset, $n";
			}

			// Lanza la consulta a la BD.
			$regPerlas = $this->bd->Consultar( $consulta );
				
			// Carga los resultados en un array de objetos Perla.
			$perlas = array();
			$i = 0;
			while( $regPerla = $regPerlas->fetch_assoc() ){
				$perlas[$i] = new Perla;
				$perlas[$i]->CargarDesdeRegistro( $regPerla );
				$perlas[$i]->CargarInfoExtraBD( BD::ObtenerInstancia(), $id_usuario );
				$i++;
			}

			// Devuelve el array resultado.
			return $perlas;
		}

	
		// Obtiene las etiquetas mas populares (las que tienen un mayor numero de
		// perlas que las referencian).
		function ObtenerEtiquetasMasPopulares()
		{
			// TODO: Completar.
			// SELECT nombre, COUNT(*) AS n FROM etiquetas JOIN perlas_etiquetas ON etiquetas.id = perlas_etiquetas.etiqueta GROUP BY id ORDER BY n DESC, nombre ASC;

			/*
			SELECT nombre, SUBSTRING(nombre, 1, 1) as inicial, COUNT(*) AS n FROM etiquetas JOIN perlas_etiquetas ON etiquetas.id = perlas_etiquetas.etiqueta GROUP BY id ORDER BY n DESC, inicial ASC
			*/

			/* SELECT nombre, SUBSTRING( nombre, 1, 1 ) AS inicial, COUNT( * )
FROM etiquetas
GROUP BY SUBSTRING( nombre, 1, 1 )
LIMIT 0 , 30*/

			/* SELECT nombre, COUNT(*) AS n FROM etiquetas JOIN perlas_etiquetas ON etiquetas.id = perlas_etiquetas.etiqueta GROUP BY SUBSTRING(nombre,1,1) ORDER BY n DESC, nombre ASC; */
			//$consulta = 
			//$this->bd->Consultar();
		}


		// Obtiene las 10 perlas con mejor nota en orden descendente.
		function ObtenerTop10Perlas()
		{
			// Prepara la consulta a la BD.
			$consulta = 'SELECT *, SUM(nota) as nota_acumulada, COUNT(*) FROM perlas JOIN votos ON perlas.id = votos.perla GROUP BY perla ORDER BY nota_acumulada DESC, id ASC LIMIT 10';	
/* ConsultarBD( "SELECT * FROM perlas LEFT JOIN (SELECT perla, COUNT(*) AS num_denuncias FROM denuncias_perlas GROUP BY perla) t2 ON id = t2.perla LEFT JOIN (SELECT perla AS denunciada FROM denuncias_perlas WHERE usuario = {$_SESSION['id']}) denuncias ON id = denunciada ORDER BY nota_acumulada DESC LIMIT 10" ); */

			// Lanza la consulta a la BD.
			$regPerlas = $this->bd->Consultar( $consulta );
			
			// Carga los resultados en un array de objetos Perla.
			$perlas = array();
			$i = 0;
			while( $regPerla = $regPerlas->fetch_assoc() ){
				$perlas[$i] = new Perla;
				$perlas[$i]->CargarDesdeRegistro( $regPerla, $_SESSION['id'] );
				$perlas[$i]->CargarInfoExtraBD( BD::ObtenerInstancia(), $_SESSION['id'] );
				$i++;
			}

			// Devuelve el array resultado.
			return $perlas;
		}

		
		// Muestra el avatar del usuario con id $id_usuario. Si $num es diferente
		// de -1 muestra tambien dicho valor entre parentesis.
		function MostrarAvatar( $id_usuario, $num = -1 )
		{
			 if( file_exists( 'media/avatares/' . $id_usuario ) ){
				$ruta = 'media/avatares/' . $id_usuario;
			 }else{
				$ruta = 'media/avatares/_default_.png';
			 }

			 if( $num == -1 ){
				$num = '';
			 }else{
				$num = ' (' . $num . ')';
			 }
			 echo "<div class=\"div_avatar\"><img class=\"avatar\" width=\"100\" height=\"100\" src=\"$ruta\" alt=\"Avatar del usuario [{$this->usuarios[$id_usuario]}]\" /><br />{$this->usuarios[$id_usuario]}$num</div>";
		}


		// Obtiene los $n mayores subidores de perlas para el mes $mes y el anno
		// $anno. Si $mes == 0, devuelve los mayores subidores globales.
		function ObtenerTopSubidores( $n = 3, $mes=0, $anno=0 )
		{
			if( $mes == 0 ){
				return $this->bd->Consultar( "SELECT usuarios.nombre, usuarios.id, SUM(logros.num_perlas) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.num_perlas <> 0 GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
			}else{
				return $this->bd->Consultar( "SELECT usuarios.nombre, usuarios.id, logros.num_perlas AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno AND logros.num_perlas <> 0 ORDER BY n DESC LIMIT $n" );
			}
		}


		// Obtiene los $n usuarios que mas han comentado durante el mes $mes y el 
		// anno  $anno. Si $mes == 0, devuelve los mayores comentaristas 
		// globales.
		function ObtenerTopComentaristas( $n = 3, $mes=0, $anno=0 )
		{
			if( $mes == 0 ){
				return $this->bd->Consultar( "SELECT usuarios.nombre, usuarios.id, SUM(logros.num_comentarios) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.num_comentarios <> 0 GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
			}else{
				return $this->bd->Consultar( "SELECT usuarios.nombre, usuarios.id, logros.num_comentarios AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno AND logros.num_comentarios <> 0 ORDER BY n DESC LIMIT $n" );
			}
		}

		// Obtiene los $n usuarios que mas perlas han puntuado durante el mes 
		// $mes y el anno  $anno. Si $mes == 0, devuelve los mayores 
		// puntuadores globales.
		// TODO: ¿Cambiar "Calificadores" por "Puntuadores" aqui y en la BD
		// (cambiarian tambien los triggers)?.
		function ObtenerTopCalificadores( $n = 3, $mes=0, $anno=0 )
		{
			if( $mes == 0 ){
				return $this->bd->Consultar( "SELECT usuarios.nombre, usuarios.id, SUM(logros.num_perlas_calificadas) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.num_perlas_calificadas <> 0 GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
			}else{
				return $this->bd->Consultar( "SELECT usuarios.nombre, usuarios.id, logros.num_perlas_calificadas AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno AND logros.num_perlas_calificadas <> 0 ORDER BY n DESC LIMIT $n" );
			}
		}

		// Obtiene los $n mejores usuarios para el mes $mes y el anno $anno. Si
		// Si $mes == 0, devuelve los mejores usuarios globales.
		// NOTA: la puntuacion se calcula de la siguiente forma:
		// 3*(perlas subidas) + 2*(comentarios realizados) + 1*(perlas 
		// puntuadas).
		function ObtenerTopUsuarios( $n = 3, $mes=0, $anno=0 )
		{
			if( $mes == 0 ){
				return $this->bd->Consultar( "SELECT usuarios.nombre, usuarios.id, 3*SUM(logros.num_perlas)+2*SUM(logros.num_comentarios)+SUM(logros.num_perlas_calificadas) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
			}else{
				return $this->bd->Consultar( "SELECT usuarios.nombre, usuarios.id, 3*logros.num_perlas+2*logros.num_comentarios+logros.num_perlas_calificadas AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno ORDER BY n DESC LIMIT $n" );
			}
		}

	} // Final de la definicion de la clase RAP.
?>
