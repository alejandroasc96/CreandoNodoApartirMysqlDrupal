# Creando Nodo apartir base de datos Mysql de Drupal

**Índice**

1. [ Descripción](#id1)
2. [ Tabla de relacion](#id2)
3. [ Operadores aritméticos](#id3)
4. [ Operadores de asignación](#id4)
   - [4.1 Operadores de asignación](#id4.1)
   - [4.2 Operador combinado](#id4.2)
5. [Operadores lógicos](#id5)
6. [Operadores para strings](#id6)
7. [Operadores para arrays](#id7)
8. [Operador Ternario](#id8)
9. [Operador Elvis](#id9)
10. [Operador Fusión Null](#id10)

## 1. Descripción <a name="id1"></a>

En este documento se va a detallar como crear nodos de Drupal cargando los datos de la base de datos. La importancia de este documento radica en que podemos exportar toda nuestra base de datos de Drupal 8 y llevarnosla a cualquier otro servidor y desde allí automatizar el proceso de creación de contenido.

Para este ejemplo se va a crear contenido para el nodo tipo 'noticias_fulp' (dicho ya ha sido creado desde la entrada gráfica).

## Instrucción para crear contenido

La estructura para crear un nodo sería la siguiente.

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

Podemos probarlo en ejecutar_php


![enseñando_ejecutar_php](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/video/ejecutando_php.gif)

No recomendado ya que la llamadas a bases de datos externas no se mostrarán adecuadamente

Ver el punto [ejecutando mediante include](#id)

## Datos requeridos (nodo noticias_fulp)

A la hora de crear un nodo es importante saber qué información nos hace falta, para ello podemos consultarlo desde el propio Drupal.

Para ver los campos tenemos dos ayudas
*/admin/structure/types*
![contenido_nodo_noticia]()

**Desde una nodo del mismo tipo**
![contenido_nodo_noticia]()

## Tabla de relaciones de Drupal

Una vez que hemos identificado todo el contenido que nos hace falta para el nodo hay que observar como guarda la información drupal.

![tabla_de_relaciones_drupal]()

De dicha tabla con un poco de imaginación observamos lo siguiente:

_Tabla node_:

- Buscamos todas las noticias mediante type = 'noticias_fulp'

- Extraemos de ella nid,title

_Tabla field_data_body_

- busqueda

  - bundle = noticias_fulp
  - entity_id = nid (tabla node)

- Extraemos
  - body_value
  - body_summary
  - body_format

_Tabla field_data_field_image_portada_noticia_

- búsqueda
  - bundle = 'noticias_fulp'
  - entity_id = nid (tabla node)

\*extraemos - field_image_interna_noticicia_fid - "........."\_alt
-".........."\_title
-".........."\_width
-".........."\_height

_Tabla file_managed_

- búsqueda

  - fid = field_image_interna_noticicia_fid

- extraemos
  - filename
  - uri

> **NOTA** es importante entender que todo el contenido de Drupal son nodos

// AÑADIR GIF
![](name-of-giphy.gif)


## Ejecutando nuestro script

Si lo que necesitamos es poder ejecutar nuestro script podemos hacerlo de varíos modos. Una posibilidad es mediante el uso de módulos tales como [DRUSH](https://www.drush.org/) mediante el comando:

```php
$ drush php-script script.php
```

Otra posibilidad, y la que vamos a usar en este ejemplo es llamando al script desde la consola de php de drupal haciendo un include. Dicho script debe estar dentro de los archivos de Drupal para que pueda ser llamado.

//FOTO 

![guardadoScript]()

```php
include 'nombreScript.php';
```
//GIF CORRER SCRIPT
## Nuestro Script de ejemplo

Este script consume de la base de datos de Drupal y creará nodos tipo noticias_fulp con los datos proporsionados

```php
<?php
use Drupal\node\Entity\Node;
function connectionMysql()
{
    // Variables de conección
    $servername = "YOUR_SERVER";
    $database = "YOUR_DATABASE";
    $username = "YOUR_USERNAME";
    $password = "YOUR_PASSWORD";

    // Creando la conección
    $conn = mysqli_connect($servername, $username, $password, $database);
    // Comprobando conección
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}


function nodoBodyImagenPortadaNoticiasFulp()
{
    $conn = connectionMysql();

    $sql = "SELECT
    N.nid,
    N.title,
    FROM_UNIXTIME(N.created) AS created,
    B.language,
    B.body_value,
    B.body_summary,
    B.body_format,
    P.field_imagen_portada_fid AS fid_img_portada,
    P.field_imagen_portada_alt AS alt_img_portada,
    P.field_imagen_portada_title AS title_img_portada,
    P.field_imagen_portada_width AS width_img_portada,
    P.field_imagen_portada_height AS height_img_portada,
    M.filename AS nombre_img_portada,
    M.uri AS uri_img_portada,
    null AS fid_img_interna,
    null AS alt_img_interna,
    null AS title_img_interna,
    null AS width_img_interna,
    null AS height_img_interna,
    null AS nombre_img_interna
    FROM node N, field_data_body B, file_managed M, field_data_field_imagen_portada P
    WHERE N.type = 'noticias_fulp'
    AND B.bundle = 'noticias_fulp'
    AND B.entity_id = N.nid
    AND P.bundle = 'noticias_fulp'
    AND P.entity_id = N.nid
    AND M.fid = P.field_imagen_portada_fid
    ORDER BY N.nid
    ";
    $result = $conn->query($sql);


    while ($row = mysqli_fetch_assoc($result)) {
        $new_array[] = $row; // Inside while loop

    }

    $conn->close();
    return $new_array;
}

function imagenInterna()
{
    $conn = connectionMysql();

    $sql = "SELECT
    N.nid,
    F.field_imagen_interna_noticia_fid AS fid_img_interna,
    F.field_imagen_interna_noticia_alt AS alt_img_interna,
    F.field_imagen_interna_noticia_title AS title_img_interna,
    F.field_imagen_interna_noticia_width AS width_img_interna,
    F.field_imagen_interna_noticia_height AS height_img_interna,
    M.filename AS nombre_img_interna,
    M.uri AS uri_img_interna
    FROM node N, field_data_field_imagen_interna_noticia F, file_managed M
    WHERE N.type = 'noticias_fulp'
    AND F.bundle = 'noticias_fulp'
    AND M.fid = F.field_imagen_interna_noticia_fid
    AND F.entity_id = N.nid
    ORDER BY N.nid
    ";

    $result = $conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $array_img_interna[] = $row; // Inside while loop
    }
    $conn->close();
    return $array_img_interna;
}

function unionArray()
{
    $array0 = nodoBodyImagenPortadaNoticiasFulp();
    $array1 = imagenInterna();
    $formatArrayResult = [];
    for ($i = 0; $i < count($array0); $i++) {
        for ($j = 0; $j < count($array1); $j++) {
            if ($array0[$i]['nid'] == $array1[$j]['nid']) {
                $array0[$i]['fid_img_interna'] = $array1[$j]['fid_img_interna'];
                $array0[$i]['alt_img_interna'] = $array1[$j]['alt_img_interna'];
                $array0[$i]['title_img_interna'] = $array1[$j]['title_img_interna'];
                $array0[$i]['width_img_interna'] = $array1[$j]['width_img_interna'];
                $array0[$i]['height_img_interna'] = $array1[$j]['height_img_interna'];
                $array0[$i]['nombre_img_interna'] = $array1[$j]['nombre_img_interna'];
                $array0[$i]['uri_img_interna'] = $array1[$j]['uri_img_interna'];
            }
        }
    }
    return $array0;
}

function subiendoNoticiaDrupal()
{
    $arrayNoticiasFulp = unionArray();

    for ($i=0; $i < count($arrayNoticiasFulp) ; $i++) {


        $titleNew = utf8_encode($arrayNoticiasFulp[$i]['title']) ?: '';

        $bodyValue = utf8_encode($arrayNoticiasFulp[$i]['body_value']) ?: '';
        $bodyFormat = $arrayNoticiasFulp[$i]['body_format'] ?: '';

        $idImgPortada = $arrayNoticiasFulp[$i]['fid_img_portada'] ?: '';
        $altImgPortada = $arrayNoticiasFulp[$i]['alt_img_portada'] ?: '';
        $titleImgPortada = $arrayNoticiasFulp[$i]['title_img_portada'] ?: '';
        $withImgPortada = $arrayNoticiasFulp[$i]['width_img_portada'] ?: '';
        $heightImgPortada = $arrayNoticiasFulp[$i]['height_img_portada'] ?: '';

        $idImgInterna = $arrayNoticiasFulp[$i]['fid_img_interna'] ?: '';
        $altImgInterna = $arrayNoticiasFulp[$i]['alt_img_interna'] ?: '';
        $titleImgInterna = $arrayNoticiasFulp[$i]['title_img_interna'] ?: '';
        $withImgInterna = $arrayNoticiasFulp[$i]['width_img_interna'] ?: '';
        $heightImgInterna = $arrayNoticiasFulp[$i]['height_img_interna'] ?: '';

        $node = Node::create(['type' => 'noticiasfulp']);
        $node->set('title', $titleNew);
        $node->set('body', [
            'value' => $bodyValue,
            'format' => $bodyFormat
        ]);
        $node->set('field_image', [
            'target_id' => $idImgPortada,
            'alt' => $altImgPortada,
            'title'=> $titleImgPortada,
            'width' => $withImgPortada,
            'height' => $heightImgPortada
        ]);
        $node->set('field_imagen_cuerpo', [
            'target_id' => $idImgInterna,
            'alt' => $altImgInterna,
            'title'=> $titleImgInterna,
            'width' => $withImgInterna,
            'height' => $heightImgInterna
        ]);
        $node->set('gva_breadcrumb', 'disable');
        $node->enforceIsNew();
        $node->save();
    }
}

subiendoNoticiaDrupal();

```
