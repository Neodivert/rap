RAP - Real Academia de las Perlas
===
La RAP es una web privada donde un grupo de amigos puede subir sus "perlas" (textos o imágenes): frases célebres, conversaciones legendarias, situaciones surrealistas... .
Este repositorio contiene todo el código necesario para montar una RAP privada y extenderla si se desea. Actualmente esto está hecho "a las prisas"; en breve actualizaré este readme con instrucciones, añadiré documentos explicativos, etc.

Importante
===
- La guía de instalación siguiente puede contener errores (al principio xampp no estaba por la labor de funcionar y fui tocando por aquí y por allá, por lo que pude haber olvidado algo). Si encuentras algún problema, no dudes en consultarme.

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
- Si se trabaja en local, al intentar acceder a "localhost/phpmyadmin" puede surgir un mensaje de error del tipo "Acceso prohibido! XAMPP nuevo concepto de seguridad: ...". Este "error" lo solucioné en mi caso siguiendo las indicaciones del siguiente enlace: http://stackoverflow.com/questions/11630412/phpmyadmin-xampp-error
