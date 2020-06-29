# Creando Nodo a partir base de datos Mysql de Drupal

**Índice**

1. [ Descripción](#id1)
2. [ Instrucción para crear contenido](#id2)
    - [2.1 Ejecutar php en consola Drupal 8](#id2.1)
3. [ Datos requeridos](#id3)
4. [ Tabla de relaciones de Drupal](#id4)
5. [Ejecutando nuestro script](#id5)
6. [Script de ejemplo](#id6)
7. [Posibles problemas](#id7)
8. [Bibliografía](#id8)

## 1. Descripción <a name="id1"></a>

En este documento se va a detallar como crear nodos de Drupal cargando los datos de MySql. La importancia de este documento radica en que podemos exportar toda nuestra base de datos de Drupal 7 y llevarnosla a cualquier otro servidor y desde allí automatizar el proceso de creación de contenido.

Para este ejemplo se va a crear contenido para el nodo tipo 'noticias_fulp' (dicho nodo ya ha sido creado desde la entrada gráfica) en Drupal 8 cargando los datos desde Drupal 7.

## 2. Instrucción para crear contenido <a name="id2"></a>

La estructura para crear un nodo sería el siguiente:

```php
use Drupal\node\Entity\Node;

    $node = Node::create(['type' => 'noticiasfulp']);
    $node->set('title', 'Nuestro título');
    $node->set('body', [
        'value' => 'Este es el body',
        'format' => 'basic_html'
    ]);

    $node->enforceIsNew();
    $node->save();
```

Podemos probarlo en ejecutar_php <a name="id2.1"></a>


![enseñando_ejecutar_php](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/video/ejecutando_php.gif?raw=true)

No recomendado ya que la llamadas a bases de datos externas no se mostrarán adecuadamente

Ver el punto [ejecutando mediante include](#id5.1)

## 3. Datos requeridos (nodo noticias_fulp) <a name="id3"></a>

A la hora de crear un nodo es importante saber qué información nos hace falta, para ello podemos consultarlo desde el propio Drupal.

Para ver los campos tenemos dos ayudas
*/admin/structure/types*
![contenido_nodo_noticia](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/video/ver_los_campos.gif?raw=true)

**Desde un nodo del mismo tipo**
![contenido_nodo_noticia](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/video/ver_los_campos_desarrolo.gif?raw=true)

## 4. Tabla de relaciones de Drupal <a name="id4"></a>

>**NOTA** En este apartado vamos a extraer toda la información de las tablas originales dado que no tenemos activado el control de ediciones. Si usted guarda una copia de todos los cambios efectuados  deberá recoger los datos de las tablas con la etiqueta revision. Ejemplo: field_data_body pasaría a ser -> field_revision_body(En está tabla se guardan todas las ediciones del body de nuestro contenido).
 Tenga cuidado a la hora de extraer contenido de esta tabla ya que para un nodo con un id puede haber varias entradas, haga uso de la sentencia JOIN en sql para resolver dicho problema.

Una vez que hemos identificado todo el contenido que nos hace falta para el nodo hay que observar como guarda la información drupal.

![tabla_de_relaciones_drupal](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/images/drupal7-db-schema.png?raw=true)

De dicha tabla con un poco de imaginación observamos lo siguiente:

_Tabla node_:

- Buscamos todas las noticias mediante type = 'noticias_fulp'

- Extraemos de ella nid,title

_Tabla field_data_body_

- Búsqueda

  - bundle = noticias_fulp
  - entity_id = nid (tabla node)

- Extraemos
  - body_value
  - body_summary
  - body_format

_Tabla field_data_field_image_portada_noticia_

- Búsqueda
  - bundle = 'noticias_fulp'
  - entity_id = nid (tabla node)

* Extraemos 
    - field_image_interna_noticicia_fid 
    - "........."\_alt
    - ".........."\_title
    - ".........."\_width
    - ".........."\_height

_Tabla file_managed_

- Búsqueda

  - fid = field_image_interna_noticicia_fid

- Extraemos
  - filename
  - uri

> **NOTA** es importante entender que todo el contenido de Drupal son nodos


## 5. Ejecutando nuestro script <a name="id5"></a>

Si lo que necesitamos es poder ejecutar nuestro script podemos hacerlo de varios modos. Una posibilidad es mediante el uso de módulos tales como [DRUSH](https://www.drush.org/) mediante el comando:

```php
$ drush php-script script.php
```
<a name="id5.1"></a>
Otra posibilidad, y la que vamos a usar en este ejemplo es llamando al script desde la consola de php de drupal haciendo un include. Dicho script debe estar dentro de los archivos de Drupal para que pueda ser llamado. 

![guardadoScript](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/images/guardadoScript.PNG?raw=true)

```php
include 'nombreScript.php';
```
Ejemplo usando el [ejecutar_php](#id2.1) de Drupal

![guardadoScript](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/images/inlcudeConsolaDrupal.PNG?raw=true)
## 6. Script de ejemplo <a name="id6"></a>

Este [script](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/codigo.php) consume de la base de datos de Drupal 7 y creará nodos tipo noticias_fulp con los datos proporsionados (puede crear nodos tando en drupal 7 y 8)

>**NOTA** Los datos deberían sacarse de las tablas no
## 7. Posibles problemas <a name="id8"></a>
[Caracteres especiales body y title](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/OtrosDocu/CaracteresEspeciales.md)
[Recoger todas las fotos de drupal 7 para poder subirlar y enlazarlas con nuevas publicaciones](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/OtrosDocu/RecorrerYGuardarFotosDrupal7.md)


## 8. Bibliografía <a name="id8"></a>

[Vídeo Youtube](https://www.youtube.com/watch?v=BhH34McCoB0)