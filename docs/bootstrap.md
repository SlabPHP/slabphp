# SlabPHP Bootstrap

The purpose of the bootstrap is to build a [system object](system.md). The bootstrap can be configured to do several useful things and can boot sites in several different ways. 

The major thing to keep in mind is that the bootstrap is building a stack of [Bundles](https://github.com/slabphp/bundle-stack) and using the configuration objects from them to start things up.

## Loading a Single Bundle

Assuming you have a bundle and the namespace is \MyApplication, your bootstrap could look as simple as:

    // Instantiate the bootstrap with the current directory as the docroot.
    $bootstrap = new \Slab\Bootstrap(__DIR__);
    $bootstrap
        ->pushNamespace('MyApplication');

    $bootstrap->bootSystem(); 

Provided your web server is configured to point to this file, it should load your bundle's code.

## Multiple Bundles Selected by DNS

Now lets assume for this example you have several bundles configured in your autoloader along with some domain names you want to point them to. Your bootstrap code may look like:

    $bootstrap = new \Slab\Bootstrap(__DIR__);
    
    $bootstrap
        ->addSite('another.com', '\Another')
        ->addSite('example.com', '\Example')
        ->addSite('mysite.com', '\MySite');
    
    $bootstrap->pushNamespaceFromServerName();
    
    $bootstrap->bootSystem();

The addSite method adds the namespace with the specified domain to the internal selector. When you run the pushNamespaceFromServerName() method, it will select the appropriate site to push onto the stack. 

If the $_SERVER['HTTP_HOST'] matches www.another.com, your bundle stack will be \Slab, \Another. If the $_SERVER['HTTP_HOST'] matches dev.mysite.com, your bundle stack will be \Slab, \MySite. 

## Landing Namespace

When you have multiple vhosts pointed to one SlabPHP and you want a "default" bundle to act as a catch-all for domains that are not specified via the addSite methods. You can add a landing bundle with the ->setDefaultNamespace('MyDefaultBundleNamespace') method. 

