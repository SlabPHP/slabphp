# SlabPHP Web Framework

This framework was started in early 2009 under a different name. Some of the ideas presented are obselete but the framework is pretty neat so it's being reformatted for public consumption. This is mostly an academic exercise as we've already internally switched to other technologies. Some of the interface patterns were designed by Michael Venezia, a friend and mentor to the author of this library.

It is known by the authors of this library that inheritance is sometimes frowned-upon and the use of protected values without proper encapsulation is considered bad design. It is also known that some of the methods in this library may be considered anti-patterns. This is mostly being released as an academic exercise.

We will most likely continue to keep it updated and maintained but major architectural modifications most likely won't occur.

## Installation

This is preliminary but assuming the final installation and usage will be similar to this:

    composer require slabphp/slabphp

Then crafting an index.php similar to the following:

    <?php

    require_once(__DIR__ . '/../vendor/autoload.php');

    $system = new \SlabPHP\Bootstrap();

    $system->addSite('example.com', 'ExampleSiteNamespace');

    $system->bootSystem();

## Methodologies

SlabPHP is built around several methodologies that govern and guide the development of SlabPHP applications. It is not meant as a RAD style framework but actually tends more towards the verbose. This is to hopefully promote re-use of code to increase integrity of the code written.

### Methodology One - Polymorphic Hierarchy

During initialization of the SlabPHP framework, you can push namespaces onto an internal hierarchy and then use the findClass method of the system object to resolve the appropriate class at runtime. The namespace hierarchy always begins with SlabPHP\ and ends with your currently selected application's namespace. When you push a new one, it will be between those two in the order in which you push them. For example, lets say during initialization this is your booting sequence:

    $system =  new \SlabPHP\Bootstrap();
    $system->pushNamespace('Shared');
    $system->addSite('example.com', 'ExampleSiteNamespace');
    $system->bootSystem();

    //Namespace hierarchy is now \SlabPHP\, \Shared\, \ExampleSiteNamespace\

If in your \ExampleSiteNamespace\Controllers\Homepage class, you do this:

    $object = $this->getSystem()->findClass('Utilities\Sausage');

SlabPHP will attempt to return a \ExampleSiteNamespace\Utilities\Sausage class if it exists, but if it doesn't it will attempt to private a \Shared\Utilities\Sausage class and if that doesn't exist, it will attempt to provide a \SlabPHP\Utilities\Sausage class before returning nothing.

Many base level SlabPHP classes use the findClass() functionality so at your application level, you can actually override functionality of the framework by specifying a class in the hierarchy above.

### Methodology Two - Pipeline-based Controller Execution

Controllers don't need to work this way however it was intended that they are built using three different pipelines. The input, operation, and output pipelines. The purpose of this is to aid re-use by allowing child controllers to add to or modify methods of a parent controller.

An example controller where you get a GET parameter, double it, and set it in the template for output may look like:

    namespace ExampleSiteNamespace\Controllers;

    class Doubler extends \SlabPHP\Controllers\Template
    {
        /**
         * @var integer
         */
        protected $parameter;

        /**
         * Set Inputs
         */
        protected function setInputs()
        {
            parent::setInputs();

            $this->inputs
                ->determineGetParameter();
        }

        /**
         * Get parameter value
         */
        protected function determineGetParameter()
        {
            $this->parameter = $this->getSystem()->input->get('value');
        }

        /**
         * Set operations pipeline
         */
        protected function setOperations()
        {
            parent::setOperations();

            $this->operations
                ->performParameterOperation();
        }

        /**
         * Perform parameter operation
         */
        protected function performParameterOperation()
        {
            $this->parameter *= 2;
        }

        /**
         * Set outputs
         */
        protected function setOutputs()
        {
            parent::setOutputs();

            $this->outputs
                ->setParameterInTemplate();
        }

        /**
         * set parameter in template
         */
        protected function setParameterInTemplate()
        {
            $this->templateData['parameter'] = $this->parameter;
        }
    }

Notice the extreme verbosity and generalized parameter naming. It's intended for re-use of base classes. If we wanted a tripler controller we could do something like:

    namespace ExampleSiteNamespace;

    class Tripler extends Doubler
    {
        /**
         * Perform parameter operation
         */
        protected function performParameterOperation()
        {
            $this->parameter *= 3;
        }
    }

I'm sure you can imagine the pros and cons for this type of architecture.

### Methodology Three - Bundles

SlabPHP was originally built to serve multiple sites from one codebase allowing each site to re-use code from other ones. Each site is basically a "bundle". During boot-up you define what bundles should boot for specific domains. This allows you to deliver your site code via composer as a bundle.

    $system = new \SlabPHP\Bootstrap();
    $system
        ->addSite('example.com', 'ExampleSiteNamespace')
        ->addSite('sample.com', 'SampleSite')
        ->pushNamespaceFromServerName();

In your composer you may be delivering a site that lives in \ExampleSiteNamespace and \SampleSite and SlabPHP will push the correct one to the hierarchy based on the $_SERVER['SERVER_NAME'] variable.

## Production Deployment

Make sure you use no-dev and optimize-autoloader for deploying to production. SlabPHP will install phpunit as a dev dependency along with the debug bar, which is something you don't want appearing on your production website.

    composer update --no-dev -o