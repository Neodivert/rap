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

		// Constructor privado.
   	private function __construct(){
			$this->bd = BD::ObtenerInstancia();
			$this->bd->Configurar( $GLOBALS['datos_bd']['host'], 
						 		  		  $GLOBALS['datos_bd']['usuario'],
						 		  		  $GLOBALS['datos_bd']['contrasenna'],
						 	 	  		  $GLOBALS['datos_bd']['bd'] );
		}

		// Obtiene la instancia unica.
   	public static function ObtenerInstancia()
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


		// Recupera de la BD el id y el nombre de los usuarios ordenados 
		// alfabéticamente por el nombre.
		function ObtenerUsuarios()
		{
			return $this->bd->Consultar( "SELECT * from usuarios ORDER BY nombre ASC" );
		}


		// Obtiene de la BD las perlas que cumplen una serie de caracteristicas 
		// segun los parametros $categoria, $participante, $contenido_informatico,
		// $humor_negro y $palabras.
		// Los argumentos $offset y $n indican, respectivamente, el nº de registro
		// a partir del cual se recuperaran las perlas, y el nº de perlas que se
		// recuperaran (se usa cuando se paginan los resultados).
		function ObtenerPerlas( $categoria = 0, $participante = 0, $contenido_informatico = 1, $humor_negro = 1, $palabras = null, $offset = 0, $n = 0 )
		{
			// Comienza a construir la consulta a la BD segun el valor de los 
			// distintos argumentos suministrados.
			$consulta = 'SELECT SQL_CALC_FOUND_ROWS * from perlas ';
			$and = false;

			if( $participante ){
				$consulta .= "INNER JOIN participantes ON perlas.id=participantes.perla AND participantes.usuario='$participante' ";
			}

			$consulta .= "LEFT JOIN (SELECT perla, COUNT(*) AS num_denuncias FROM denuncias_perlas GROUP BY perla) t2 ON id = t2.perla ";

			$consulta .= "LEFT JOIN (SELECT perla AS denunciada FROM denuncias_perlas WHERE usuario = {$_SESSION['id']}) denuncias ON id = denunciada ";

			if( $categoria || !$contenido_informatico || !$humor_negro ){
				$consulta .= 'WHERE ';
				if( $categoria ){
					$consulta .= "categoria='$categoria' ";
					$and = true;
				}
	
				if( !$contenido_informatico ){
					if( $and ) $consulta .= 'AND ';
					$consulta .= 'contenido_informatico=0 ';
					$and = true;
				}
			
				if( !$humor_negro ){
					if( $and ) $consulta .= 'AND ';
					$consulta .= 'humor_negro=0 ';
					$and = true;
				}			
			}

			$consulta .= 'ORDER BY id DESC ';

			if( $n != 0 ){
				$consulta .= "LIMIT $offset, $n";
			}

			$res = $this->bd->Consultar( $consulta );

			if( !$res ) return null;
			return $res;
		}


	} // Final de la definicion de la clase RAP.
?>
