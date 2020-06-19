# Creando Nodo a partir base de datos Mysql de Drupal

**Índice**

1. [ Descripción](#id1)
2. [ Instrucción para crear contenido](#id2)
    - [2.1 Ejecutar php en consola Drupal 8](#id2.1)
3. [ Datos requeridos](#id3)
4. [ Tabla de relaciones de Drupal](#id4)
5. [Ejecutando nuestro script](#id5)
6. [Script de ejemplo](#id6)
7. [Bibliografía](#id7)

## 1. Descripción <a name="id1"></a>

En este documento se va a detallar como crear nodos de Drupal cargando los datos de MySql. La importancia de este documento radica en que podemos exportar toda nuestra base de datos de Drupal 8 y llevarnosla a cualquier otro servidor y desde allí automatizar el proceso de creación de contenido.

Para este ejemplo se va a crear contenido para el nodo tipo 'noticias_fulp' (dicho nodo ya ha sido creado desde la entrada gráfica).

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

\*Extraemos 
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

Este script consume de la base de datos de Drupal y creará nodos tipo noticias_fulp con los datos proporsionados

```php
<?php
use Drupal\node\Entity\Node;
function connectionMysql()
{
    // Variables de conexión
    $servername = "YOUR_SERVER";
    $database = "YOUR_DATABASE";
    $username = "YOUR_USERNAME";
    $password = "YOUR_PASSWORD";

    // Creando la conexión
    $conn = mysqli_connect($servername, $username, $password, $database);
    // Comprobando conexión
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

## 7. Bibliografía <a name="id7"></a>

[Vídeo Youtube](https://www.youtube.com/watch?v=BhH34McCoB0)