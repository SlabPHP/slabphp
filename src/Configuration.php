<?php
/**
 * Framework Bundle Configuration
 *
 * @package Slab
 * @subpackage Configuration
 * @author Eric
 */
namespace Slab;

class Configuration extends \Slab\Bundle\Standard
{
    /**
     * @return \Psr\Log\LoggerInterface|null
     */
    public function getLogger()
    {
        $monolog = '\Monolog\Logger';
        if (!class_exists($monolog)) return null;

        $log = new $monolog('Slab::' . $this->getNamespace());

        return $log;
    }

    /**
     * @return string
     */
    protected function getCurrentWorkingDirectory()
    {
        return __DIR__;
    }

    /**
     * @return null|Components\InputManagerInterface|Input\Manager
     */
    public function getInputManager()
    {
        $inputManager = new \Slab\Input\Manager();

        return $inputManager;
    }

    /**
     * @param \Slab\Components\SystemInterface $system
     * @return null|Components\ConfigurationManagerInterface|Configuration\Manager
     */
    public function getConfigurationManager(\Slab\Components\SystemInterface $system)
    {
        $configuration = new \Slab\Configuration\Manager();

        if ($system->stack())
        {
            $configuration->setFileDirectories($system->stack()->getConfigDirectories());

            $fileNames = ['default.php'];
            if (!empty($_SERVER['SERVER_NAME']))
            {
                $fileNames[] = $_SERVER['SERVER_NAME'] . '.php';
            }

            $configuration->setFileNames($fileNames);
        }

        return $configuration;
    }

    /**
     * @param \Slab\Components\SystemInterface $system
     * @return null|\Slab\Components\Router\RouterInterface
     */
    public function getRouter(\Slab\Components\SystemInterface $system)
    {
        $router = new \Slab\Router\Router();

        if ($system->stack())
        {
            $router->setConfigurationPaths($system->stack()->getConfigDirectories());
            $router->addRouteFile('routes.xml');
            $router->setLog($system->log());

            if ($system->config() && !empty($system->config()->routeFiles)) {
                foreach ($system->config()->routeFiles as $file) {
                    $router->addRouteFile($file);
                }
            }
        }

        return $router;
    }

    /**
     * @param Components\SystemInterface $system
     * @return null|Components\Database\DriverInterface
     */
    public function getDatabaseProvider(\Slab\Components\SystemInterface $system)
    {
        return null;
    }

    /**
     * @param Components\SystemInterface $system
     * @return mixed|null|Session\Handlers\File
     */
    public function getSessionHandler(\Slab\Components\SystemInterface $system)
    {
        return new \Slab\Session\Handlers\File();
    }

    /**
     * @param Components\SystemInterface $system
     * @return mixed|null|Cache\Providers\Dummy
     */
    public function getCacheProvider(\Slab\Components\SystemInterface $system)
    {
        return null;
    }
}