<?php
include 'codigo.php';
$arrayDeNoticiasFulp = unionArray();

function replacePublicForUri()
{
    global $arrayDeNoticiasFulp;
    $countArrayNoticias = count($arrayDeNoticiasFulp);
    for ($i = 0; $i < $countArrayNoticias; $i++) {
        $cadena_de_texto = &$arrayDeNoticiasFulp[$i]['uri_img_portada'];
        $cadena_de_texto1 = &$arrayDeNoticiasFulp[$i]['uri_img_interna'] ?: '';
        if (preg_match("/^public:/", $cadena_de_texto)) {
            $cadena_de_texto = substr($cadena_de_texto, 8);
        }

        if (preg_match("/^public:/", $cadena_de_texto1)) {
            $cadena_de_texto1 = substr($cadena_de_texto1, 8);
        }
        if ($cadena_de_texto) {
            $srcfile = "../sites/default/files" . $cadena_de_texto;
            $dstfile = "../sites/default/files/guardadoImagenPrueba" . $cadena_de_texto;
            $directorio = '';
            $st = explode("/", $cadena_de_texto);
            if (count($st) > 2) {
                $str = explode("/", $cadena_de_texto, -1);
                $str = implode("/", $str);
                $directorio = "../sites/default/files/guardadoImagenPrueba" . $str;
                print_r($directorio);
                echo '<br>';
            }

            if (file_exists($directorio)) {
                echo "existe directorio";
            } else {
                mkdir($directorio, 0755, true);
                echo "no existe,creando directorio: $directorio";
                echo '<br>';
            }

            if (!copy($srcfile, $dstfile)) {
                echo "no se pudo copiar";  
            } else {
                echo "copiado en" . $dstfile;
            }
        }
        if ($cadena_de_texto1) {
            $srcfile = "../sites/default/files" . $cadena_de_texto1;
            $dstfile = "../sites/default/files/guardadoImagenPrueba" . $cadena_de_texto1;
            $directorio = '';
            $st = explode("/", $cadena_de_texto1);
            if (count($st) > 2) {
                $str = explode("/", $cadena_de_texto1, -1);
                $str = implode("/", $str);
                $directorio = "../sites/default/files/guardadoImagenPrueba" . $str;
            }

            if (file_exists($directorio)) {
                echo "existe directorio";
            } else {
                mkdir($directorio, 0755, true);
                echo "no existe directorio";
            }

            if (!copy($srcfile, $dstfile)) {
                echo "no se pudo copiar";
            } else {
                echo "copiado en" . $dstfile;
            }
        }
    }
}

replacePublicForUri();
