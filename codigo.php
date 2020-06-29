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

// SELECT PARA DRUPAL 7
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

// SELECT PARA DRUPAL 7
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
                break;
            }
        }
    }
    return $array0;
}

function subiendoNoticiaDrupal()
{
    $arrayNoticiasFulp = unionArray();
    
    for ($i=0; $i < count($arrayNoticiasFulp) ; $i++) { 

        $titleNew = mb_convert_encoding($arrayNoticiasFulp[$i]['title'],'UTF-8', 'Windows-1252') ?: '';

        $bodyValue = mb_convert_encoding($arrayNoticiasFulp[$i]['body_value'],'UTF-8', 'Windows-1252') ?: '';
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
    // }
}

subiendoNoticiaDrupal();
