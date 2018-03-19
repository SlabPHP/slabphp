# SlabPHP System Object

The system class is created by the bootstrap and is responsible for the following major tasks:

* build and store a reference to appropriate booting components based on bundles pushed into the stack
    * required components
        * router
        * configuration manager
        * logger
        * input manager
    * optional components
        * cache driver
        * database driver
        * session handler
* query the router for the appropriate controller
* execute the controller's lifecycle, lending a reference to itself
* display the output

The system interface provides methods for accessing the booted components. In default SlabPHP controllers, you can access most of them with the ->system member of the controller class.

## Component Overrides via Bundle Configs

During setup, the system object will loop through each bundle in the stack starting from the last bundle to find the appropriate component. For example, the base SlabPHP configuration class will return a system default router of type \Slab\Router\Router. If your site's bundle configuration is pushed in the stack and it returns a different router object that adopts the appropriate interface, you can override the system level component.

