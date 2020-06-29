## Descripción

A la hora de realizar publicaciones automatizadas podemos encontrarnos con problemas imprimiendo caracteres especiales. 
Ejemplo:

![fotoErrorCaracter](?raw=true)

## Solución

Realizar un encode a utf8mb4 

```php
$bodyValue = mb_convert_encoding($arrayNoticiasFulp[$i]['body_value'],'UTF-8', 'Windows-1252');
```
*Resultado*:
![SolucionfotoErrorCaracter](?raw=true)

## Explicación

La codificación utf8 de MySQL no es UTF-8 real. Es una codificación similar a UTF-8, pero solo admite un subconjunto de lo que admite UTF-8. utf8mb4 es formato real de UTF-8. Esta diferencia es un detalle de implementación interna de MySQL. Ambos se ven como UTF-8 en el lado de PHP. 