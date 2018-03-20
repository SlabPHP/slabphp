# Installation and Setup Guide

## Requirements

* Web Server ([Apache](https://httpd.apache.org/)/[Nginx](https://www.nginx.com/)) with url rewriting module enabled (mod_rewrite, etc.)
* [Composer](https://getcomposer.org)
* [PHP7](http://www.php.net/)

## Quick Install

Creating a SlabPHP project can be as simple as something like this:

    mkdir myproject
    cd myproject
    composer init --require slabphp/slabphp
    composer install
    php vendor/slabphp/slabphp/bin/bundle/generate.php . myproject
    composer dump-autoload
    
This will first create a directory and go into it, initialize composer with the requirement to grab SlabPHP and monolog and then fire off the bundle generator script included in this library. The last step is optional and can be accomplished manually.

You'll just need to [setup your web server](server.md) to point at the public directory. You should be able to visit the site you configure in your browser and see the SlabPHP welcome page. 

## Single Website Implementation

SlabPHP follows the [standard PHP library skeleton](https://github.com/php-pds/skeleton) format. The easiest way to implement a site (bundle) is to run the slabphp setup script. If you don't want to run it and you want to do it manually you'll basically be doing something similar to the following steps. 

But before you start, decide on a namespace for your application code. For this example, the namespace will be "\MyProject".

### Step 1 - Add Namespace to Composer

Since SlabPHP relies on the psr4 autoloader in composer, you can add your namespace to it by modifying your composer.json with something like as follows and then run composer dump-autoload to rebuild the autoloader.

    ...
    "autoload": {
        "psr-4": {
            "MyProject\\": "src/"
        }
    },
    ...
    
### Step 2 - Create/Validate Project Directory Structure

Your project file structure should look as follows:

* ~/myproject - your root project directory, can be anything really
    * src - psr4 root for \MyProject namespace
    * views - default location where the display configuration will look for your bundles templates
    * configs - this as a place to store configs for your project
    * resources - this as a location to store css/js/sql etc. resources
    * tests - store your tests for your application here
    * docs - store your markdown documents here
    * public - the public docroot, contains an index.php that boots SlabPHP and any static http assets
    * vendor - automatically created by composer, add to your .gitignore

### Step 3 - Create a Bundle Configuration
Create the file ~/myprojects/src/Configuration.php and edit the contents to look something like this:

    <?php
    namespace MyProject;
    
    class Configuration extends \Slab\Bundle\Standard
    {
        /**
         * @return string
         */
        protected function getCurrentWorkingDirectory()
        {
            return __DIR__;
        }
    }
    
You can override many aspects of the parent class to radically change the behavior and components of the framework on a per-bundle basis.

### Step 4 - Create A Page

Create a page by creating a controller, a route configuration file with the route in it, and a template.

#### 4a. Creating a Controller

Create the file ~/myprojects/src/Controllers/Homepage.php and edit the contents to look something like this:

    <?php
    namespace MyProject\Controllers;
    
    class Homepage extends \Slab\Controllers\Page
    {
        /**
         * @var string
         */
        protected $title = 'My Controller';
    
        /**
         * @var string
         */
        protected $description = 'This is my website!';
    }
    
#### 4b. Create the Route

Create/edit the file ~/myprojects/configs/routes.xml and make the contents look something like this.

    <?xml version="1.0" encoding="UTF-8" ?>
    <routes>
        <route>
            <path>/</path>
            <name>Homepage</name>
            <class>\MyProject\Controllers\Homepage</class>
        </route>
    </routes>
    
#### 4c. Create the Template
Create/edit the file ~/myprojects/src/views/pages/homepage.php, notice this is all lowercase where the controller class name was capitalized. This is by design. Put whatever html contents you want in it, we'll pretend it says:

    <h1>Hi there!</h1>
    
### Step 5 - Put Your Namespace In The Bundle Hierarchy

Open ~/myproject/public/index.php and add your namespace to the bootstrap loader to make it look like something that follows:

    ...
    $bootstrap = new \Slab\Bootstrap(__DIR__);
    $bootstrap->pushNamespace('MyProject');
    
    $bootstrap->bootSystem();
    ...
    
At this point your homepage controller should respond to http://local.slabproject.com/

## Multiple Site Implementations

You can create individual repositories for your bundles and include them via composer. As long as they have the configuration object you can just make your modified bootstrap index.php push or add namespaces to the domain list. See the core SlabPHP library Bootstrap component documentation for more information.