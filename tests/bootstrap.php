<?php
require_once "../../tests/bootstrap.php";

\OC::$loader->addValidRoot(realpath(__DIR__ . "/../"));

spl_autoload_register(function ($class_name) {
    $namespace = 'OCA\\CustomProperties\\';
    if (strpos($class_name, $namespace) !== 0) {
        return;
    }

    $classToLoad = str_replace($namespace, "", $class_name);

    $basedir = realpath(__DIR__ . DIRECTORY_SEPARATOR . "..");
    $pathToClass = str_replace("\\", DIRECTORY_SEPARATOR, $classToLoad);
    $file = ($basedir . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . $pathToClass . '.php');

    if (file_exists($file)) {
        require_once($file);
    }
});
