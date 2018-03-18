<?php
/**
 * Framework system class
 *
 * The main system class sets up essential framework libraries, begins
 * the routing, and gives structure to the application. You can override
 * this class at an application level.
 *
 * @author Eric
 * @package Slab
 * @subpackage Core
 */
namespace Slab;

class System implements \Slab\Components\SystemInterface
{
    /**
     * Router
     *
     * @var \Slab\Components\Router\RouterInterface
     */
    private $router;

    /**
     * Configuration Manager
     *
     * @var \Slab\Components\ConfigurationManagerInterface
     */
    private $configuration;

    /**
     * Input Manager Object
     *
     * @var \Slab\Components\InputManagerInterface
     */
    private $input;

    /**
     * @var \Slab\Components\SessionDriverInterface
     */
    private $session;

    /**
     * Log Object
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $log;

    /**
     * Database Object
     *
     * @var \Slab\Components\Database\DriverInterface
     */
    private $db;

    /**
     * Cache driver
     *
     * @var \Slab\Components\Cache\DriverInterface
     */
    private $cache;

    /**
     * @var \Slab\Bundle\Stack;
     */
    private $bundleStack;

    /**
     * @var \Slab\Components\Debug\ManagerInterface
     */
    private $debug;

    /**
     * System constructor.
     * @param Bundle\Stack $bundleStack
     * @throws Exceptions\System\ObjectCreationFailure
     */
    public function __construct(\Slab\Bundle\Stack $bundleStack)
    {
        $this->bundleStack = $bundleStack;

        $bundles = $this->bundleStack->getBundles(true);

        //Build independent objects first
        $this->buildIndependentObjects($bundles);

        //Build dependent objects second, these can assume there is a system object
        $this->buildDependentObjects($bundles);

        $this->performAfterInitializationTasks();
    }

    /**
     * @param \Slab\Components\Debug\ManagerInterface $debug
     * @return $this
     */
    public function setDebug(\Slab\Components\Debug\ManagerInterface $debug)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * @param \Slab\Components\BundleInterface[] $bundles
     * @throws Exceptions\System\ObjectCreationFailure
     */
    private function buildIndependentObjects($bundles)
    {
        foreach ($bundles as $bundle) {
            if (empty($this->log) && ($log = $bundle->getLogger())) {
                $this->log = $log;
                break;
            }
        }

        if (empty($this->log)) {
            throw new \Slab\Exceptions\System\ObjectCreationFailure('Missing compatible log component. Include monolog\monolog if you need one.');
        }

        foreach ($bundles as $bundle) {
            if (empty($this->input) && ($input = $bundle->getInputManager())) {
                $this->input = $input;
                break;
            }
        }

        if (empty($this->input)) {
            throw new \Slab\Exceptions\System\ObjectCreationFailure('Missing compatible input manager component. Include slabphp/input-manager');
        }
    }

    /**
     * @param \Slab\Components\BundleInterface[] $bundles
     * @throws Exceptions\System\ObjectCreationFailure
     */
    private function buildDependentObjects($bundles)
    {
        foreach ($bundles as $bundle) {
            if (empty($this->configuration) && ($configuration = $bundle->getConfigurationManager($this)))
            {
                $this->configuration = $configuration;
                $this->configuration->loadConfiguration();
                break;
            }
        }

        if (empty($this->configuration)) {
            throw new \Slab\Exceptions\System\ObjectCreationFailure('Missing compatible configuration manager component. Include slabphp/configuration-manager');
        }

        foreach ($bundles as $bundle) {

            if (empty($this->db) && ($databaseProvider = $bundle->getDatabaseProvider($this))) {
                $this->db = new \Slab\Database\Driver();
                $this->db->setProvider($databaseProvider);
                break;
            }
        }

        foreach ($bundles as $bundle) {
            if (empty($this->cache) && ($cacheProvider = $bundle->getCacheProvider($this))) {
                $this->cache = new \Slab\Cache\Driver();
                $this->cache->setProvider($cacheProvider);
                break;
            }
        }

        foreach ($bundles as $bundle) {
            if (empty($this->session) && ($sessionHandler = $bundle->getSessionHandler($this))) {
                $this->session = new \Slab\Session\Driver();
                $this->session->setHandler($sessionHandler);
                break;
            }
        }

        foreach ($bundles as $bundle) {
            if (empty($this->router) && ($router = $bundle->getRouter($this))) {
                $this->router = $router;
                break;
            }
        }

        if (empty($this->router))
        {
            throw new \Slab\Exceptions\System\ObjectCreationFailure('Missing compatible router component. Include slabphp/router');
        }
    }


    /**
     * After we've initialized our site level objects, any additional tasks can be done here
     *
     * @return boolean
     */
    private function performAfterInitializationTasks()
    {
        if (php_sapi_name() == 'cli' || !empty($this->debug)) {
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors', '1');
        }

        return true;
    }

    /**
     * Route the request
     * @return bool
     * @throws \Exception
     */
    public function routeRequest()
    {
        $this->log->debug("Routing request...");

        $route = $this->router->routeRequest($this);

        if (empty($route))
        {
            $this->handleUnhandled404();
            return false;
        }

        return $this->handleRoute($route);
    }

    /**
     * @param \Slab\Components\Router\RouteInterface $route
     * @return bool
     * @throws \Exception
     */
    private function handleRoute(\Slab\Components\Router\RouteInterface $route)
    {
        $className = $route->getClass();

        if (empty($className) || !class_exists($className)) {
            if (!empty($this->log)) {
                $this->log->error('The routed class does not exist ' . $className . ', returning a 404 instead.');
            }
            $this->handleUnhandled404();
            return false;
        }

        if (!empty($this->debug))
        {
            $this->debug->startBenchMark('controller');
        }

        /**
         * @var \Slab\Components\Router\RoutableControllerInterface $class
         */
        $class = new $className();

        $class->setSystemReference($this);
        $class->setRouteReference($route);

        $response = $class->executeControllerLifecycle();

        if (!empty($this->debug))
        {
            $this->debug->endBenchmark('controller');
        }

        return $this->handleRoutedResponse($response);
    }

    /**
     * Handle an unhandled 404
     * @throws \Exception
     */
    private function handleUnhandled404()
    {
        if (!headers_sent())
        {
            $protocol = !empty($_SERVER["SERVER_PROTOCOL"]) ? $_SERVER["SERVER_PROTOCOL"] : 'HTTP/1.0';
            header($protocol . " 404 Not Found");
        }

        $routeParameters = new \stdClass();
        $routeParameters->class = '\Slab\Controllers\Page';
        $routeParameters->path = '/404-route';
        $routeParameters->name = '404';
        $routeParameters->parameters = [
            'pageTitle' => '404 Not Found',
            'pageDescription' => 'The content you were looking for does not exist or has been moved.',
            'subTemplate' => 'pages/errors/404.php'
        ];

        $route404 = new \Slab\Router\Route($routeParameters);

        $this->handleRoute($route404);
    }

    /**
     * @param Components\Output\ControllerResponseInterface $response
     * @return bool
     * @throws \Exception
     */
    private function handleRoutedResponse(\Slab\Components\Output\ControllerResponseInterface $response)
    {
        $resolverClass = $response->getResolver();

        if (!class_exists($resolverClass))
        {
            throw new \Exception("The resolver class specified " . $resolverClass . " does not exist.");
        }

        /**
         * @var \Slab\Components\Output\ResolverInterface $resolver;
         */
        $resolver = new $resolverClass($this);

        return $resolver->resolveResponse($response);
    }

    /**
     * @return Components\ConfigurationManagerInterface
     */
    public function config()
    {
        return $this->configuration;
    }

    /**
     * @return Components\SessionDriverInterface
     */
    public function session()
    {
        return $this->session;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function log()
    {
        return $this->log;
    }

    /**
     * @return Components\InputManagerInterface
     */
    public function input()
    {
        return $this->input;
    }

    /**
     * @return Components\Router\RouterInterface
     */
    public function router()
    {
        return $this->router;
    }

    /**
     * @return Components\Database\DriverInterface
     */
    public function db()
    {
        return $this->db;
    }

    /**
     * @return Components\Cache\DriverInterface
     */
    public function cache()
    {
        return $this->cache;
    }

    /**
     * @return Bundle\Stack
     */
    public function stack()
    {
        return $this->bundleStack;
    }

    /**
     * @return Components\Debug\ManagerInterface
     */
    public function debug()
    {
        return $this->debug;
    }
}