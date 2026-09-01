# Proyecto
Proyecto hecho por: Dante Laureiro, Inti Peña, Federico González, Bruno Ferreira y Bruno Vilar.

El proyecto SIGSM se basa en el desarrollo de un software administrativo que permita la digitalización de documentos por parte del hospital de clínicas. Como objetivo secundario se debera permitir seguir la trazabilidad de las ambulancias.

Para este proyecto se utilizó: una base de datos, el lenguaje de PHP, J.S, CSS y HTML. Para que este proyecto funcione de manera adecuada se debera disponer de xamppcontrolpanel con apache y mysql instalado. Además es necesario el uso de phpmyadmin para la conexión con la base de datos y la manipulación de la misma.

Para utilizar el proyecto es muy simple, del lado de la instalación, simplemente deberemos utilizar la consola (en caso de linux) o git bash, que interpreta comandos de la consola de linux, para poder descargalo del github a través del comando "git clone "http://unlinkrandom.git/" ", después se utilizara "cd "nombredelarchivocopiado" " para entrar a la carpeta que contiene los archivs para ejecutar el software. 
En el lado del uso, es diferente según el usuario, si es administrador, deberá pasar por un inicio de sesión que en caso de ser correcto lo llevará hasta "SIGSM.html" para poder manipular indicaciónes. En el caso del paciente, este deberá entrar utilizando un código QR.

Administrador: nombre del usuario, contraseña.
Usuario: QR.

Todo lo que tiene que ver con uso o manipulación de datos o base de datos, se resume en cambios directos a la misma BDD o al archivo "consexion.php" donde se cambiaran los datos como serian, el host, el n° de puerto que utiliza el servicio de sql, el nombre de la BDD, el nombre del usuario que manipula la BDD y la contraseña que manipula la BDD.

La estructura del proyecto se resume en Proyecto/Datos, Documentos, Negocio, Presentacion/
Dentro de cada una de las mismas se encuentran archivos dependiendo de su función.