<?php
/**
 * SlabPHP Generated Index
 */

require_once(__DIR__ . '/../vendor/autoload.php');

try
{
    // Instantiate the bootstrap with the current directory as the docroot.
    $bootstrap = new \Slab\Bootstrap(__DIR__);
    $bootstrap
        ->pushNamespace('APPNAMESPACE')
        ->setDefaultNamespace('SlabLanding');

    $bootstrap->bootSystem();
}
catch (\Exception $exception)
{
    error_log($exception->getMessage());
}
