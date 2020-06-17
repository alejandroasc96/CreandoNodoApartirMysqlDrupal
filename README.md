# Creando Nodo apartir base de datos Mysql de Drupal

**Índice**   
1. [ Descripción](#id1)
2. [ Tabla de relacion](#id2)
3. [ Operadores aritméticos](#id3)
4. [ Operadores de asignación](#id4)
   -  [4.1 Operadores de asignación](#id4.1)
   - [4.2 Operador combinado](#id4.2)
5. [Operadores lógicos](#id5)
6. [Operadores para strings](#id6)
7. [Operadores para arrays](#id7)
8. [Operador Ternario](#id8)
9. [Operador Elvis](#id9)
10. [Operador Fusión Null](#id10)

## 1. Descripción <a name="id1"></a>

En este documento se va a detallar como crear nodos de Drupal cargando los datos de Drupal. La importancia de este documento radica en que podemos exportar toda nuestra base de datos de Drupal 8 y llevarnosla a cualquier otro servidor y desde allí automatizar el proceso de creación de contenido.

Para este ejemplo se va a crear contenido para el nodo tipo 'noticias_fulp' (dicho ya ha sido creado desde la entrada gráfica).

## Datos requeridos (nodo noticias_fulp)

A la hora de crear un nodo es importante saber qué información nos hace falta, para ello podemos consultarlo desde el propio Drupal.

AÑADIR GIF COMO LLEGAR A LOS CAMPOS

![contenido_nodo_noticia]()


## Tabla de relaciones de Drupal

Una vez que hemos identificado todo el contenido que nos hace falta para el nodo hay que observar como guarda la información drupal.

![tabla_de_relaciones_drupal]()

De dicha tabla con un poco de imaginación observamos lo siguiente:

*Tabla node*:
- Buscamos todas las noticias mediante type = 'noticias_fulp'

- Extraemos de ella nid,title

*Tabla field_data_body*
* busqueda
    - bundle = noticias_fulp
    - entity_id = nid (tabla node) 

* Extraemos
    - body_value
    - body_summary
    - body_format

*Tabla field_data_field_image_portada_noticia*
* búsqueda
    - bundle = 'noticias_fulp'
    - entity_id = nid (tabla node) 

*extraemos
    - field_image_interna_noticicia_fid
    - "........."_alt
    -".........."_title
    -".........."_width
    -".........."_height

*Tabla file_managed*

* búsqueda
    - fid = field_image_interna_noticicia_fid

* extraemos
    - filename
    - uri

>**NOTA** es importante entender que todo el contenido de Drupal son nodos

// AÑADIR GIF
![](name-of-giphy.gif)

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
    // }
```

Podemos probarlo en ejecutar_php

GIF ENSEÑANDO COMO SE EJECUTA

![enseñando_ejecutar_php]()

No recomendado ya que la llamadas a bases de datos externas no se mostrarán adecuadamente

Ver el punto [ejecutando mediante include](#id)

## Ejecutando nuestro script

Si lo que necesitamos es poder ejecutar nuestro script podemos hacerlo de varíos modos. Una posibilidad es mediante el uso de módulos tales como [DRUSH](https://www.drush.org/) mediante el comando:
```php
$ drush php-script script.php
```

Otra posibilidad, y la que vamos a usar en este ejemplo es llamando al script desde la consola de php de drupal haciendo un include

```php
include 'nombreScript.php';
```
