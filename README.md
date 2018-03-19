# SlabPHP Web Framework

This is the core SlabPHP library. The purpose of it is to create a system object, select an appropriate controller, execute it, and display the result. That's the general flow of every of any particular SlabPHP request. This library depends on all the other SlabPHP libraries.  

* [Installation and Setup Guide](docs/implementation.md)
* [History and More Info](docs/history.md)
* [Bootstrap Documentation](docs/bootstrap.md)
* [System Object Documentation](docs/system.md)

## SlabPHP Components

SlabPHP is made of a bunch of atomic components that can be used separately or together. The components rely on a shared library of interfaces. You can use the pre-built components or even write your own. Some libraries are also optional so feel free to bring your own logger, database, session handler, etc.

Component | Description
--- | --- 
[Core SlabPHP Library](https://github.com/SlabPHP/slabphp) | includes all of these via composer and provides structure for using the framework.
[Templating and Display Library](https://github.com/SlabPHP/display) | SlabPHP's output and templating library
[Router](https://github.com/SlabPHP/router) | light-weight web router with validators for routes
[Component Interface Library](https://github.com/SlabPHP/component-interfaces) | external interface library with shareable testing mocks
[Debug Utility](https://github.com/SlabPHP/debug) | the debug utility displays a helpful debug bar at the bottom of default SlabPHP pages
[Base Controller Library](https://github.com/SlabPHP/controllers) | some base controllers to help you get started
[Bundle Stack Library](https://github.com/SlabPHP/bundle-stack) | manage a hierarchy of bundles so you can easily share code between them
[Landing Page Bundle](https://github.com/SlabPHP/landing) | this page's source code and an example SlabPHP bundle
[Sequencer Library](https://github.com/SlabPHP/sequencer) | this library helps you write re-usable controllers by extending controller sequences
[Database Library](https://github.com/SlabPHP/database) | a simple relational database wrapper with token binding
[Cache Manager Library](https://github.com/SlabPHP/cache-manager) | a simple cache manager wrapper with memcache and redis providers
[Configuration Library](https://github.com/SlabPHP/configuration-manager) | a configuration library that loads files from a hierarchy of bundles
[Session Library](https://github.com/SlabPHP/session-manager) | provides flash data capabilities and session handlers that work in both native and the slabphp session system
[Input Manager Library](https://github.com/SlabPHP/input-manager) | a small input manager library that sanitizes input to the application
[Concatenator Library](https://github.com/SlabPHP/concatenator) | a simple library that provides concatenation of files, urls, etc. for use in controllers

## Production Deployment

SlabPHP will include PHPUnit and a debug library that can add a [debug bar](docs/debug-bar.md) to the standard template. When deploying to production, make sure you use no-dev and optimize-autoloader.

    composer update --no-dev -o
    
## Feedback, Changes, Comments, Concerns

Feel free to open tickets, [email Salerno Labs LLC staff](https://www.salernolabs.com/contact), or most preferably create pull requests for any changes you'd like in the codebase.