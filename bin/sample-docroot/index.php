<?php
/*
 * This is a sample index.php you may have in your site's docroot.
 *
 * SlabPHP assumes you have your own project setup with a public directory with an index.php in it.
 *
 * For this example, lets assume that your site code is in namespace \MySite
 * but you also have an optional library with shared code you want in the stack.
 */

require_once(__DIR__ . '/../vendor/autoload.php');

// Instantiate the bootstrap with the current directory as the docroot.
$bootstrap = new \Slab\Bootstrap(__DIR__);

// This directly adds the namespace to the bundle stack.
$bootstrap
    ->pushNamespace('\Shared')
    ->pushNamespace('\MySite');

// After the previous line your bundle stack hierarchy is \Slab, \Shared, \MySite

// Begin the fun!
$bootstrap->bootSystem();