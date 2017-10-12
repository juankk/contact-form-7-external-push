<?php

//TODO develop correct autoloader
/*spl_autoload_register( function ($class_name) {
    include $class_name . '.php';
});*/

include_once ( __DIR__ . '/admin/apis.php' );
include_once ( __DIR__ . '/admin/connector.php' );

$api = new Admin_Apis();
