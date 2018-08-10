<?php
/*
 * Archivo de testeo del framework
 */
session_start();

define('ROOT', dirname(dirname(__FILE__)));
define('DS', DIRECTORY_SEPARATOR);

require ROOT . DS . 'vendor' . DS . 'autoload.php';

$validator = new \PowerOn\Validation\Validator();
$validator
        ->add('test', 'upload', TRUE)
        ->add('test', 'max_size', 400000)
        ->add('test', 'extension', ['jpg', 'sql']);
if (isset($_FILES['file'])) {
    d($_FILES['file']);
    !d($validator->validate(['test' => $_FILES['file']]));
}


!d($validator->getErrors());
!d($validator->getWarnings());
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" />
    <input type="submit">
</form>