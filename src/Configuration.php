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
     * @return null|Components\Database\ProviderInterface
     */
    public function getDatabaseProvider(\Slab\Components\SystemInterface $system)
    {
        $provider = null;

        try
        {
            if (!empty($system->config()->database->mysqli)) {
                $provider = $this->getMySQLiProvider($system->config()->database->mysqli);
            }
        }
        catch (\Exception $exception)
        {
            $system->log()->critical("Failed to instantiate the mysqli database object requested.");
        }

        if (!empty($provider)) {
            $provider->setLog($system->log());
        }

        return $provider;
    }

    /**
     * Get mysqli data provider
     *
     * @param $configuration
     * @return \Slab\Database\Providers\MySQL\Provider
     * @throws \Exception
     */
    private function getMySQLiProvider($configuration)
    {
        if (!class_exists('\Mysqli')) {
            throw new \Exception("A mysqli database configuration exists but the extension is not loaded.");
        }

        $host = 'localhost';
        $port = null;
        $user = 'root';
        $pass = '';
        $persistent = false;
        $database = '';

        if (!empty($configuration->host)) {
            $host = $configuration->host;
        }

        if (!empty($configuration->port)) {
            $port = $configuration->port;
        }

        if (!empty($configuration->user)) {
            $user = $configuration->user;
        }

        if (!empty($configuration->pass)) {
            $pass = $configuration->pass;
        }

        if (!empty($configuration->persistent)) {
            $persistent = $configuration->persistent;
        }

        if (!empty($configuration->database)) {
            $database = $configuration->database;
        }

        if ($persistent) {
            $host = 'p:' . $host;
        }

        if (!empty($port))
        {
            $host .= ':' . $port;
        }

        $db = new \Mysqli($host, $user, $pass, $database, $port);

        if ($db->connect_error) {
            throw new \Exception("Failed to connect: " . $db->connect_error);
        }

        $databaseProvider = new \Slab\Database\Providers\MySQL\Provider();
        $databaseProvider->setMySQL($db);

        return $databaseProvider;
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