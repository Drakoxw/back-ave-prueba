# PRUEBA TECNICA AVE

## Ruta de despliegue
> https://cifrado.com.co/prueba-ave-back/public/api/

> Requiere `PHP 8.1` superior

* Una vez descargado el archivo, instalar dependencias:
* Recomendacion del chef -> si hay algun error al instalar remover el atchivo `composer.lock`

```sh
    composer install
```
ó
```sh
    composer update
```

### Documentación
> En la raiz del proyecto hay un archivo `request.http`, con la extension `REST Client` de Huachao Mao; se pueden ejecutar los servicios sin un cliente tipo postman


## Docs Tests

Agregar Test al final de la clase y test_ al principio de los métodos

```sh
php artisan make:test FeatureTest
```

```sh
php artisan make:test UnitTest --unit
```

## Tests
```sh
php artisan test
```
```sh
php artisan test --filter testExampleTest
```
