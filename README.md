# Team-Manager

4.1. Introducción a la aplicación (Getting Started)
      "Team-Manager" o "Gestor de equipos" es una aplicación web que usa un back-end con Symfony-php y un front-end con React-js. Cada parte de la aplicación está dockerizada (frontend(vite),             mysql, php y nginx) y requiere de una serie de instrucciones para su despliegue. 
4.2. Manual de Instalación
      - Para instalar la aplicación necesitarás una terminal de comandos (CMD,POWERSHELL o BASH),visual studio code, la extensión devcontainers, instalar docker en tu terminal de linux/mac y               autenticarte o en caso de windows instalar docker desktop desde su web oficial y WSL2 en la terminal con ```WSL --install```. 
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


      
4.3. Manual de usuario.
4.4. Manual de administración
