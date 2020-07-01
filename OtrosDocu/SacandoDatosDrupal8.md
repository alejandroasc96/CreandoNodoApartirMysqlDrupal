## Descripción

La tablas donde se guardan las información de los nodos en drupal 8 es diferente a la de otras versiones por lo que en este documento detallaremos las nuevas tablas de consulta comparandolas con drupal 7.

Para este ejemplo sacaremos los datos del nodo original y no obtendremos las posibles actualizaciones que se hayan hecho (leer más abajo para más información)

## Extrayendo la información Drupal 8

>**NOTA** al igual que pasaba en drupal 7 si quereos obtener los datos con todas las posibles modificaciones que podamos haber hecho en nuestros nodos, deberemos acudir a las tablas con la etiqueta revision *ejemplo* : la tabla node pasaría a ser node_revision | la tabla node_body sería node_revision_body.

*Diagrama :*

![Diagrama_drupal8](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/images/schema_Drupal8.png?raw=true)


_Tabla node_ -> node:

*drupal 7: Tabla field_data_body* -> *drupal 8 node_body*


*drupal 7 : Tabla field_data_field_image_portada_noticia* -> drupal 8 : node__field_image

_Tabla file_managed_
