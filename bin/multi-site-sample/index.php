<?php
/*
 * This is a sample index.php you may have in your site's docroot if you have multiple sites from one codebase.
 *
 * See ../sample-docroot/index.php for a sample of one site's namespace.
 *
 * For this example, lets assume you have three sites with the following configurations.
 */

require_once(__DIR__ . '/../vendor/autoload.php');

// Instantiate the bootstrap with the current directory as the docroot.
$bootstrap = new \Slab\Bootstrap(__DIR__);

// This adds the site's bundle to the SlabPHP server name resolver
$bootstrap
    ->addSite('another.com', '\Another')
    ->addSite('example.com', '\Example')
    ->addSite('mysite.com', '\MySite');

// This resolves the correct namespace to add to the bundle stack
$bootstrap->pushNamespaceFromServerName();

// If the $_SERVER['HTTP_HOST'] matches www.another.com, your bundle stack will be \Slab, \Another
// If the $_SERVER['HTTP_HOST'] matches dev.mysite.com, your bundle stack will be \Slab, \MySite

$bootstrap->bootSystem();