<?php
	/* Info:
	Clase singlenton para gestionar la RAP.
	Fuente: 
	http://www.cristalab.com/tutoriales/crear-e-implementar-el-patron-de-diseno-singleton-en-php-c256l/
	// TODO: Seguir el enlace para convertir la BD en una verdadera singlenton.
	/*
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
	*/

	require_once DIR_CONFIG . 'bd.php';
	require_once 'bd.php';

	class RAP {
   	private static $instancia;

		private $bd;

		protected $usuarios;

		// Constructor privado.
   	private function __construct(){
			$this->bd = BD::ObtenerInstancia();
			$this->bd->Configurar( $GLOBALS['datos_bd']['host'], 
						 		  		  $GLOBALS['datos_bd']['usuario'],
						 		  		  $GLOBALS['datos_bd']['contrasenna'],
						 	 	  		  $GLOBALS['datos_bd']['bd'] );
		}

		// Obtiene la instancia unica.
   	static function ObtenerInstancia()
		{
      	if( !self::$instancia instanceof self ){
				self::$instancia = new self;
			}
			return self::$instancia;
   	}

		// Obtiene la lista de categorías de la base de datos (por orden 
		// ascendente de nombres).
		function ObtenerCategorias()
		{
			return $this->bd->Consultar( "SELECT * from categorias ORDER BY nombre ASC" );
		}


		// Recupera de la BD el id y el nombre de los usuarios.
		function CargarUsuarios()
		{
			$rUsuarios = $this->bd->Consultar( "SELECT * from usuarios ORDER BY nombre ASC" );
			$this->usuarios = array();
			while( $rUsuario = $rUsuarios->fetch_object() ){
				$this->usuarios[$rUsuario->id] = $rUsuario->nombre;
			}
		}

		function ObtenerUsuarios(){
			return $this->usuarios;
		}


		function ObtenerNombreUsuario( $id )
		{
			return $this->usuarios[$id];
		}


		// Obtiene de la BD las perlas que cumplen una serie de caracteristicas 
		// segun los parametros $categoria, $participante, $contenido_informatico,
		// $humor_negro y $palabras.
		// Los argumentos $offset y $n indican, respectivamente, el nº de registro
		// a partir del cual se recuperaran las perlas, y el nº de perlas que se
		// recuperaran (se usa cuando se paginan los resultados).
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

			if( $n != 0 ){
				$consulta .= "LIMIT $offset, $n";
			}

			$regPerlas = $this->bd->Consultar( $consulta );
				
			$perlas = array();
			$i = 0;
			while( $regPerla = $regPerlas->fetch_assoc() ){
				$perlas[$i] = new Perla;
				$perlas[$i]->CargarDesdeRegistro( $regPerla, $_SESSION['id'] );
				$perlas[$i]->CargarInfoExtraBD( BD::ObtenerInstancia(), $_SESSION['id'] );
				$i++;
			}

			return $perlas;
		}

		function ObtenerEtiquetasMasPopulares()
		{
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
			$this->bd->Consultar();
		}

		// Obtiene las 10 perlas con mejor nota en orden descendente.
		function ObtenerTop10Perlas()
		{
			$consulta = 'SELECT *, SUM(nota) as nota_acumulada, COUNT(*) FROM perlas JOIN votos ON perlas.id = votos.perla GROUP BY perla ORDER BY nota_acumulada DESC, id ASC LIMIT 10';	
/* ConsultarBD( "SELECT * FROM perlas LEFT JOIN (SELECT perla, COUNT(*) AS num_denuncias FROM denuncias_perlas GROUP BY perla) t2 ON id = t2.perla LEFT JOIN (SELECT perla AS denunciada FROM denuncias_perlas WHERE usuario = {$_SESSION['id']}) denuncias ON id = denunciada ORDER BY nota_acumulada DESC LIMIT 10" ); */

			$regPerlas = $this->bd->Consultar( $consulta );
				
			$perlas = array();
			$i = 0;
			while( $regPerla = $regPerlas->fetch_assoc() ){
				$perlas[$i] = new Perla;
				$perlas[$i]->CargarDesdeRegistro( $regPerla, $_SESSION['id'] );
				$perlas[$i]->CargarInfoExtraBD( BD::ObtenerInstancia(), $_SESSION['id'] );
				$i++;
			}

			return $perlas;
		}

		function MostrarAvatar( $usuario, $num = -1 )
		{
			 if( file_exists( 'media/avatares/' . $usuario ) ){
				$ruta = 'media/avatares/' . $usuario;
			 }else{
				$ruta = 'media/avatares/_default_.png';
			 }

			 if( $num == -1 ){
				$num = '';
			 }else{
				$num = ' (' . $num . ')';
			 }
			 echo "<div class=\"div_avatar\"><img class=\"avatar\" width=\"100\" height=\"100\" src=\"$ruta\" alt=\"Avatar del usuario [$usuario]\" /><br />$usuario$num</div>";
		}


		function ObtenerTopSubidores( $n = 3, $mes=0, $anno=0 )
		{
			if( $mes == 0 ){
				return $this->bd->Consultar( "SELECT usuarios.nombre, SUM(logros.num_perlas) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.num_perlas <> 0 GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
			}else{
				return $this->bd->Consultar( "SELECT usuarios.nombre, logros.num_perlas AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno AND logros.num_perlas <> 0 ORDER BY n DESC LIMIT $n" );
			}
		}

		function ObtenerTopComentaristas( $n = 3, $mes=0, $anno=0 )
		{
			if( $mes == 0 ){
				return $this->bd->Consultar( "SELECT usuarios.nombre, SUM(logros.num_comentarios) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.num_comentarios <> 0 GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
			}else{
				return $this->bd->Consultar( "SELECT usuarios.nombre, logros.num_comentarios AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno AND logros.num_comentarios <> 0 ORDER BY n DESC LIMIT $n" );
			}
		}

		function ObtenerTopCalificadores( $n = 3, $mes=0, $anno=0 )
		{
			if( $mes == 0 ){
				return $this->bd->Consultar( "SELECT usuarios.nombre, SUM(logros.num_perlas_calificadas) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.num_perlas_calificadas <> 0 GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
			}else{
				return $this->bd->Consultar( "SELECT usuarios.nombre, logros.num_perlas_calificadas AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno AND logros.num_perlas_calificadas <> 0 ORDER BY n DESC LIMIT $n" );
			}
		}

		function ObtenerTopUsuarios( $n = 3, $mes=0, $anno=0 )
		{
			if( $mes == 0 ){
				return $this->bd->Consultar( "SELECT usuarios.nombre, 3*SUM(logros.num_perlas)+2*SUM(logros.num_comentarios)+SUM(logros.num_perlas_calificadas) AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario GROUP BY usuarios.id ORDER BY n DESC LIMIT $n" );
			}else{
				return $this->bd->Consultar( "SELECT usuarios.nombre, 3*logros.num_perlas+2*logros.num_comentarios+logros.num_perlas_calificadas AS n FROM usuarios, logros WHERE usuarios.id = logros.usuario AND logros.mes = $mes AND logros.anno = $anno ORDER BY n DESC LIMIT $n" );
			}
		}

	} // Final de la definicion de la clase RAP.
?>
