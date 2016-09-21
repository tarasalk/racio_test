<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
spl_autoload_register('myAutoloader');
function myAutoloader($className)
{
    $ar = explode("\\",$className);
    $path = 'src/';
    $files = glob($path.$ar[2].".php");
    foreach ($files as $fName)
    {
        require_once $fName;
    }
}
