# PowerOn System - ValidationService

Servicio de validación de datos con mensajes personalizables

## Instalación vía Composer

Podés instalar ValidationService vía
[Composer](https://getcomposer.org)  a través de la consola:

``` bash
$ composer require poweronsystem/validationservice
```
## Requisitos

* PHP >= 5.4
* poweronsystem/utility: "^0.1.3"

## Uso

Creación de la clase y configuración, utilización de validadores estandar

``` php
//Autoload composer
require '/vendor/autoload.php';

//Configuración del validador (Ver archivo src/Validation.php)
$config =  [
  'return_boolean' => FALSE,
  'date_format' => 'd/m/Y',
  'date_time_format' => 'd/m/Y H:i',
  'time_format' => 'H:i',
];

//Creamos una instancia del validador
$validator = new PowerOn\Validation\Validator($config);

//Creamos reglas de validación
$validator
  ->add('field_1', 'string_allow', ['alpha', 'spaces'])
  ->add('field_2', 'custom', function($value, $formData) {
    //Lógica personalizada
    return $value == 1 && key_exists('field_3', $formData) && $formData['field_3'] === 'some_value';
  })
  ->add('field_2', 'decimal', 2)
  ->add('field_3', 'number', true, \PowerOn\Validation\Rule::WARNING);

//Datos obtenidos de un formulario
$data = [
  'field_1' => 'campo-prueba',
  'field_2' => 12.34,
  'field_3' => 'some_value'
];

//Ejecuto la validación con los datos obtenidos
$validator->validate($data);

//Resultado
var_dump($validator->getErrors()); //array(size=1) 'field_1' => 'Este campo no admite: guiónes medios'
var_dump($validator->getWarnings()); //array(size=1) 'field_3' => 'Debe ser un valor numérico.'

```

## Errores y Advertencias

Al validador se le pueden configurar errores y advertencias, cuando un campo tiene una regla de validación 
como **Rule::WARNING** y no pasa la comprobación, el validador continúa la operación y retorna TRUE al resultado de la misma.

Las alertas generadas son recuperadas mediante la función **$validator->getWarnings()**
