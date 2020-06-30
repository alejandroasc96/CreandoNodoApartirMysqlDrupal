## Descripción
A la hora de crear nodos dentro de Drupal podemos agregarles diferentes campos, uno de ellos es poder agregar una foto de portada.

Para ello uno debe seguir el siguiente flujo de códgio:
```php
//cogemos nuestro fichero
$data = file_get_contents(__DIR__ . '/images/my_image.jpeg');
$file = file_save_data($data, 'public://my_image.jpeg');

//Creando nuestro nodo
$node = \Drupal\node\Entity\Node::create([
  'type'             => 'page',
  'title'            => 'Foobar',
  'field_my_image' => [
    'target_id' => $file->id(),
    'alt'       => 'Lorem ipsum',
    'title'     => 'Dolor sit amet',
  ],
]);

$node->save();
```

Dicho esto vamos a verlo con nuestro ejemplo.

## Añadiendo portada a nuestro nodo noticiasFulp

Dada nuestra ruta donde tenemos guardada la imagen
[Mostrando_ruta_imagen_server]()

Nuestra instrucción quedería de la siguiente forma

```php
 $node = Node::create(['type' => 'noticiasfulp']);
        $node->set('title', $titleNew);
        // Select que indica qué tipo de noticia es
        $node->set('field_portfolio_tags', [
            'target_id' => 28
        ]);
        $node->set('body', [
            'value' => $bodyValue,
            'format' => $bodyFormat
        ]);
        $node->set('created', $created);
        $data = file_get_contents(__DIR__ . '/sites/default/files/guardadoImagenPrueba/foto_2.jpg');
        $file = file_save_data($data, 'public://foto_2.jpg');
        $node->set('field_image', [
            'target_id' => $file->id(),
            'alt' => $altImgPortada,
            'title'=> $titleImgPortada,
            'width' => $withImgPortada,
            'height' => $heightImgPortada
        ]);
        $node->set('gva_breadcrumb', 'disable');

        $node->enforceIsNew();
        $node->save();
```

>**NOTA** recuerda que para ver todo el código y lo que significan las variables puedes verlo [aquí](https://github.com/alejandroasc96/CreandoNodoApartirMysqlDrupal/blob/master/codigo.php)