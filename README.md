# Team-Manager

## 4.1. Introducción a la aplicación (Getting Started)
"Team-Manager" o "Gestor de equipos" es una aplicación web que usa un back-end con Symfony-php y un front-end con React-js. Cada parte de la aplicación está dockerizada (frontend(vite), mysql, php y nginx) y requiere de una serie de instrucciones para su despliegue.

## 4.2. Manual de Instalación
### Para instalar la aplicación necesitarás una terminal de comandos (CMD,POWERSHELL o BASH), visual studio code, la extensión devcontainers, instalar docker en tu terminal de linux/mac y autenticarte o en caso de windows instalar docker desktop desde su web oficial y WSL2 en la terminal con ```WSL --install```.
- A continuación clona este repositorio en tu ordenador: ```git clone URL```.
![Captura desde 2024-06-09 19-08-02](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/378e2034-2b79-4167-b0d8-56777ba0ba27)
- Entramos en Team-Manager/backend/.docker
- Añadimos el archivo .env.nginx.local con el contenido: NGINX_BACKEND_DOMAIN='localhost'
![Captura desde 2024-06-09 19-10-25](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/efa49d25-7a8d-47b2-95af-8059a72597b1)
![Captura desde 2024-06-09 19-13-23](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/198e0148-c74b-42a0-ba78-f15e182ca29f)
- Ahora lanzamos ```docker-compose up -d --build```
- Abrimos desde el visual studio code la carpeta backend. Pulsamos control + shift + P y lanzamos reopen in DevContainer
- Abrimos una nueva terminal dentro del visual studio code y veremos como se muestra la ruta del contenedor php de docker:
![Captura desde 2024-06-09 19-14-22](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/35bcfe03-8d25-415b-a4f2-1c1d587f0b0b)
![Captura desde 2024-06-09 19-14-52](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/3d371cf1-eb0d-4c4c-a495-14ca84516377)
- Lanzamos desde la terminal un ```composer install``` y ```php bin/console lexik:jwt:generate-keypair```
![Captura desde 2024-06-09 19-23-41](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/8be67e42-3214-40a9-b360-8d6fbe7dd49f)
- Ya tenemos acceso a la api:
![Captura desde 2024-06-09 19-15-39](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/28075a6c-4b27-4ebc-b49b-d0fb4489a50c)
- El frontend es accesible también
![Captura desde 2024-06-09 19-25-21](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/f1070f17-b0a2-4827-83e4-daebc51cf72e)

## 4.3. Manual de usuario.
  Al entrar a la aplicación procede a hacer un registro si es que no tienes una cuenta o accede mediante login con tu correo y contraseña
  ![Captura de pantalla 2024-06-09 222040](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/5a07b885-ff7f-473b-b1b9-f9db83d0aa76)
  Accede a tu pestaña de Estadísticas y añade tus estadísticas según el nivel que pienses que tienes. Añade cuantas posiciones domines, pero cuidado, si pones la de portero además de otras podría tocarte parar.
  ![Captura de pantalla 2024-06-09 222917](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/6f5a015b-310d-44de-87b1-f991317fa4aa)
  Puedes darle tu email a un administrador si ya han creado una peña y quieres unirte o si lo quieres es crear una, accede a la pestaña Equipo, introduce un nombre y crea uno nuevo.
  ![Captura de pantalla 2024-06-09 220619](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/721823fc-c8bc-4e3a-a550-769e5ca8a3b8)
  Disfruta creando un partido desde la pestaña Partido o deja que alguien de tu peña lo haga por ti. Puedes ver tus partidos creados con los equipos equilibrados en la pestaña de Equipo, al final de la vista de tu equipo
  ![image](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/18756e45-cd71-4ebd-a5b4-b6c133dc5d22)


## 4.4. Manual de administración
  Si además de lo anterior mencionado eres tú quien pretende gesionar la peña has de saber que podrás borrar y añadir compañeros además de borrar el equipo si es que cerrais la peña.
  ![Captura de pantalla 2024-06-09 222937](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/90544629-60f1-4e12-a93a-111045dbeb3a)
  ![Captura de pantalla 2024-06-09 225955](https://github.com/CarlosAlvarez96/Team-Manager/assets/116850911/cef3263b-10b2-4a31-acf4-d199ab49c447)
