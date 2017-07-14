<?php
/*
 * Archivo de testeo del framework
 */
session_start();

define('ROOT', dirname(dirname(__FILE__)));
define('DS', DIRECTORY_SEPARATOR);

require ROOT . DS . 'vendor' . DS . 'autoload.php';

$validator = new \PowerOn\Validation\Validator();
$validator->add('tst', 'email', TRUE);

!d($validator->validate(['tst' => 'infarrobacaleta.er']));
!d($validator->getErrors());