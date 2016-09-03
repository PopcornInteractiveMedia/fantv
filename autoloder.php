<?php

require_once getcwd() . '/config.php';

function __autoload($className) {
    $extensions = array(".php", ".class.php");
    $path = array('..', 'class', 'lib', 'include', 'includes');
    $paths = explode(PATH_SEPARATOR, get_include_path());
    $paths = array_merge($paths, $path);
    $className = str_replace("_", DIRECTORY_SEPARATOR, $className);
    foreach ($paths as $path) {
        $filename = $path . DIRECTORY_SEPARATOR . $className;
        foreach ($extensions as $ext) {
            if (file_exists($filename . $ext)) {
                require_once $filename . $ext;
            }
        }
    }
}
