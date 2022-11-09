# Crasy Weather - Neubox Challenge 2

Se diseñó una API capaz que acepta solicitudes RESTful, que recibe como parámetro el nombre de la ciudad, o las coordenadas largas de latitud y longitud, y devuelva una sugerencia de lista de reproducción de acuerdo con la temperatura actual, que tiene el siguiente comportamiento:

    Si la temperatura es superior a los 30°C, el servicio devuelve una lista de música para fiesta.
    Si la temperatura de la ciudad está entre 15°C y 30°C, el servicio devuelve una lista de música pop.
    Si la temperatura de la ciudad está entre 10°C y 14°C, el servicio devuelve una lista de música rock.
    Si la temperatura es inferior a los 10°C, el servicio devuelve una lista de música para clásica.

La aplicación guarda información, con fines estadísticos, de las solicitudes que se le realizan al sistema, como son la temperatura y la lista de canciones sugeridas.

-------------------------------------------------------------------------------------------------------------------------------------------
# Detalles tecnológicos de la solución

La estrategia que se utilizó para diseñar un servicio tolerante a fallas, receptivo y resistente, esta basada sobre el framework Laravel, el cual se potenció con Octane y un servidor Swoole.

Se instaló Octane para administrar peticiones paralelas en función de los núcleos del procesador, ya que este genera un worker por cada núcleo, capaz de procesar las peticiones de forma paralela.

Se implementa Swoole para servir la solución ya que con esta tecnología se obtiene mayor rendimiento que utilizando Nginx o Apache, ya que el framework se inicia una sola vez y queda disponible para las siguientes solicitudes.

Esta selección, partió de la base de que Lumen ya no tiene más soporte y la implementación de Octane y Swoole permite administrar las peticiones de forma asíncrona, sacando un mayor partido de las nuevas características de PHP 8. 

La gestión de las transacciones, generadas por las solicitudes al servicio, se basó en colas de prioridades, que re encolan las peticiones fallidas, en la cola de prioridad más alta, con el fin de que se reprocesen antes que las peticiones de prioridad normal, que son las que se realizan por primera vez.

Se desarrolló un comando para realizar pruebas de la cola que, en función de la performance del servidor y las respuestas de los servicios de terceros consultados, como lo son el de Spotify y el de OpenWeatherMaps, permita revisar si las fallas que ocasionan el re encolamiento, son producto de la necesidad de escalar la infraestructura, o de la respuesta de las consultas a terceros.

En caso de que las fallas sean por la necesidad de escalar la infraestructura, puede implementarse la llamada a un script de automatización, que permita replicar el server y adicionar los balanceadores requeridos en función del proyecto de infraestructura.

Para el desarrollo a nivel de código se buscó no usar recursos de sobre ingeniería, que por un lado hacen economía de código, pero por otro dificultan la lectura y posterior mantenimiento de la solución. Por lo anterior se buscó mantener el estándar PSR-2 con el fin de darle un orden estandarizado a la lectura del código, no se usaron abreviaciones que la dificultaran y, para no sobre documentar, se usaron nombres de variables, métodos y clases que van acompañando la narrativa de las funciones que cada componente desempeña.

La documentación de los endpoint se realizó en OpenAPI 3.0 y está disponible en {APP_URL}/api/documentation

La API sigue las recomendaciones de Laravel en cuanto a distribución de archivos, nomenclaturas de variables, métodos, clases, rutas, modelos y sus campos, así como el mantenimiento de sus principios S.O.L.I.D.

Se desarrollaron pruebas funcionales y unitarias, para permitir revisar su cobertura, por medio de Xdebug, incluso permitirían probar los beneficios de Octane, por medio de la gestión de pruebas paralelas.

La BD está configurada con el patrón CQRS que permite realizar la escritura y la lectura de manera independiente, con el fin de poder escalar la infraestructura de servidores de forma separada, ya sea del servidor de lectura o del de escritura.

Los aspectos de seguridad se desarrollaron mediante la implementación de un stack de middlewares, que se basa en la autenticación por Bearer token, y valida que la ip del servidor que consulta, esté registrada en la tabla de ips permitidas.

-------------------------------------------------------------------------------------------------------------------------------------------
# Como ejecutar el proyecto

1.- Baja o clona el proyecto desde el repositorio a la carpeta que destinarás para el mismo.

![ScreenHunter 2923](https://user-images.githubusercontent.com/11873645/199619622-f9149863-2caf-4a32-8172-932c5dd32a67.png)

2.- Configura tu archivo de entorno .env con la información de tu conexión a la base de datos.

3.- Abre la terminal desde la carpeta de tu proyecto y ejecuta los siguientes comandos artisan para instalar las dependencias y las tablas:

    composer install

después

    php artisan migrate
 
---------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Integraciones

Para completar la integración de los servicios de terceros revise los siguientes apartados de cada empresa desarrolladora.

Información para integrar Spotify:
https://developer.spotify.com/documentation/web-api/

Información para integrar OpenWeatherMaps:
https://openweathermap.org/current

Ya teniendo las llaves, coloca en el archivo .env, las claves correspondientes para consumir los recursos de Spotify y OpenWeatherMaps. 

    SPOTIFY_CLIENT_ID=
    SPOTIFY_CLIENT_SECRET=

    OPEN_WEATHER_MAP_API_KEY=

----------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Consumo de la API

Para efectuar las pruebas, y ver los resultados de las consultas puedes revisar la documentación de la API en OpenAPI 3.0, disponible en tu navegador en la dirección de tu proyecto:

    APP_URL/api/documentation
    
O puedes descargar en tu postman la colección que se preparó con este fin desde la siguiente liga:

    https://www.getpostman.com/collections/322ee0502dbaf66fb2d8

En esta última ya viene configurado un token, que puedes reemplazarlo por uno que hayas guardado en la tabla AuthorizedToken. 
Al mismo tiempo, debe de guardarse en la tabla AuthorizedIp, la dirección ip de tu máquina, en el caso de pruebas locales 127.0.0.1
El body de ejemplo viene con el campo City o Latitud y Longitud, dependiendo del request a usar, cuyos valores pueden ser reemplazados con la intención de obtener diferentes resultados.

---------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Pruebas del desarrollo

## Análisis estático del código

Para realizar el análisis estático del código de la aplicación se configura la variable level, dentro del archivo phpstan.neon, en niveles de 1 a 9, siendo este último el de más alto nivel.

        includes:
            - ./vendor/nunomaduro/larastan/extension.neon

        parameters:

            paths:
                - app/

            # Level 9 is the highest level
            level: 9

        #    ignoreErrors:
        #        - '#PHPDoc tag @var#'
        #
        #    excludePaths:
        #        - ./*/*/FileToBeExcluded.php
        #
        #    checkMissingIterableValueType: false

Luego desde la terminal, en el directorio o carpeta raiz del proyecto, se ejecuta el siguiente comando que muestra los errores a depurar, en caso de que haya.

        Para Linux: ./vendor/bin/phpstan analyse

        Para Windows: vendor\bin\phpstan analyse

![ScreenHunter 2930](https://user-images.githubusercontent.com/11873645/200731060-9dd78139-9075-4550-8a85-a7f4a3bd90e6.png)

## Pruebas unitarias y funcionales

Para correr las pruebas, tanto funcionales como unitarias, ejecute el siguiente comando artisan desde la terminal:

    php artisan test 

![ScreenHunter 2928](https://user-images.githubusercontent.com/11873645/199622735-3ec8702c-f8ea-49cd-b72b-9a736312bef9.png)

Es importante destacar que la primera vez que se ejecuta, la respuesta de Time es mucho mayor a la que refleja la segunda ejecución, ya que en la segunda entra en juego la estrategia Octane-Swoole, y se refleja inmediatamente el aumento de la performance de la API, ya que, como se comentó al principio el framawork queda disponible para las siguientes solicitudes.

-------------------------------------------------------------------------------------------------------------------------------------------
# Prueba de la cola y generación de solicitudes

1.- Abre la terminal desde la carpeta de tu proyecto y ejecuta los siguientes comandos artisan:

    php artisan crasyweather:sendrequeststoqueue quantityOfRequestsToSend

Reemplaza quantityOfRequestsToSend por el número de solicitudes que quieras para probar el sistema.

![ScreenHunter 2924](https://user-images.githubusercontent.com/11873645/199620511-53b7d329-89be-41f9-b97c-96b8e9af901f.png)

2.- Abre el webbrowser y pon la dirección a la que esta apuntado tu proyecto, ejemplo: localhost, o en caso de que utilices Laragon, pon crasyweather.test para monitorear el comportamiento.

![Cola Front](https://user-images.githubusercontent.com/11873645/199620650-6812afa8-8f6a-4763-80c4-10a70d1b6e66.png)

3.- En el terminal ejecuta el comando: php artisan queue:work --queue=high,low

![ScreenHunter 2926](https://user-images.githubusercontent.com/11873645/199620880-6a369b64-9a6d-4f15-97b6-0ce0f75879ae.png)

4.- Para limpiar la tabla de trabajos fallidos después de la prueba, puedes eliminar todos con el siguiente comando artisan: php artisan queue:flush

![ScreenHunter 2927](https://user-images.githubusercontent.com/11873645/199620895-114a4d7c-cde6-4883-8e79-055ddd96982b.png)

----------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Recursos externos

Colección de pruebas para Postman:
https://www.getpostman.com/collections/322ee0502dbaf66fb2d8

Información para integrar Spotify:
https://developer.spotify.com/documentation/web-api/

Información para integrar OpenWeatherMaps:
https://openweathermap.org/current

----------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Backlog

Facilitar la implementación/ejecución del servicio local, test y producción.
Pasar phpstand en modo estricto.
Dockerizar el proyecto.
