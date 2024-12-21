<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>
<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
</p>
## Acerca del Proyecto

Este proyecto demuestra las diferencias de rendimiento entre el procesamiento server-side y client-side de DataTables en una aplicación CRUD de usuarios. Desarrollado con Laravel 9, proporciona una comparación práctica de ambos enfoques.

### Características Principales

- Implementación de DataTables en modos server-side y client-side
- Funcionalidad CRUD completa de usuarios
- Interfaz responsiva con Bootstrap 3
- Procesamiento optimizado de datos
- Comparación de rendimiento en tiempo real

## Tecnologías

- [Laravel 9](https://laravel.com/)
- [jQuery DataTables](https://datatables.net/)
- [MySQL](https://www.mysql.com/)
- [Bootstrap 3](https://getbootstrap.com/docs/3.4/)

## Requisitos

- PHP 8.1
- MySQL
- Composer
- Laravel 9

## Instalación

Clonar el repositorio:

    git clone git@github.com:Xerna/RICARDO-CERNA-P_TECPHPJR.git
    cd git@github.com:Xerna/RICARDO-CERNA-P_TECPHPJR.git

## Instalar dependencias PHP:

composer install

## Configurar archivo de entorno:

    cp .env.example .env
    php artisan key:generate

## Configurar base de datos en .env:

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=tu_base_de_datos
    DB_USERNAME=tu_usuario
    DB_PASSWORD=tu_contraseña    

## Ejecutar migraciones y seeders:

    php artisan migrate
    php artisan db:seed --class=UsuarioSeeder

## Iniciar el servidor:

    php artisan serve


## Implementaciones

### Server-Side (/usuarios)

La implementación server-side ofrece:
- Procesamiento de datos en el servidor
- Paginación eficiente
- Óptimo para grandes conjuntos de datos
- Menor consumo de memoria del cliente
- Tiempos de carga inicial optimizados

### Client-Side (/usuarios-client)

La versión client-side proporciona:
- Procesamiento de datos en el navegador
- Búsqueda y ordenamiento instantáneos
- Experiencia de usuario más fluida
- Ideal para conjuntos de datos pequeños a median
