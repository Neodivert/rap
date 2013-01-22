RAP - Real Academia de las Perlas
===
La RAP es una web privada donde un grupo de amigos puede subir sus "perlas" (textos o imágenes): frases célebres, conversaciones legendarias, situaciones surrealistas... .
Este repositorio contiene todo el código necesario para montar una RAP privada y extenderla si se desea. Actualmente esto está hecho "a las prisas"; en breve actualizaré este readme con instrucciones, añadiré documentos explicativos, etc.

Importante
===
- He intentado instalar la RAP siguiendo las instrucciones de abajo, pero xampp (y más concretamente phpmyadmin) no parece por la labor. En cuanto tenga tiempo lo corregiré.

Instalación
===
- Descargar el comprimido y descomprimir.
- Instalación y configuración de la base de datos.
  - Crear una base de datos vacía e importar la estructura desde el fichero bd/bd-rap.sql.
  - Modificar el fichero src/rap/recursos/config.php con la información para conectarse a la base de datos.
  - Introducir usuarios manualmente en la base de datos.
    - (*) El código de la RAP maneja contraseñas codificadas con MD5. Tener esto en cuenta a la hora de introducir las contraseñas manualmente en la base de datos (las contraseñas se pueden codificar en la página siguiente: http://pajhome.org.uk/crypt/md5/).
- Instalación del código.
  - Copiar el CONTENIDO de la carpeta src en la carpeta principal del servidor, quedando la jerarquia de carpetas siguientes:
SERVER_ROOT/lib
SERVER_ROOT/rap
    - Si se ha instalado xampp, la carpeta del servidor será <ruta_instalacion_xampp>/htdocs/.
- Reiniciar el servidor para que los cambios surtan efecto.
