<?php
/**
 * Slab Framework Bootstrap
 *
 * @package Slab
 * @subpackage Core
 * @author Eric
 */
namespace Slab;

class Bootstrap
{
    /**
     * @var \Slab\Bundle\Stack
     */
    private $bundleStack;

    /**
     * @var \Slab\Components\BundleInterface[]
     */
    private $allowableDomains = [];

    /**
     * @var string
     */
    private $docroot;

    /**
     * @var string
     */
    private $defaultServername = 'localhost';

    /**
     * @var string
     */
    private $defaultNamespace = '';

    /**
     * @var \Slab\Components\Debug\ManagerInterface
     */
    private $debug;

    /**
     * Bootstrap constructor.
     * @param $docroot
     * @throws \Exception
     */
    public function __construct($docroot)
    {
        if (class_exists('\Slab\Debug\Manager'))
        {
            $this->debug = new \Slab\Debug\Manager();
            $this->debug->startBenchMark('bootstrap');
        }

        $frameworkConfiguration = new \Slab\Configuration();
        $this->bundleStack = new \Slab\Bundle\Stack($frameworkConfiguration);

        //Docroot is the location of the index.php
        $this->docroot = $docroot . ((substr($docroot, -1) != DIRECTORY_SEPARATOR) ? DIRECTORY_SEPARATOR : '');

        //Any relative files should be handled from the site's root
        chdir(dirname($this->docroot));
    }

    /**
     * Push a namespace onto the internal bundle stack
     *
     * @param $namespace
     * @return $this
     * @throws Exceptions\Exception
     * @throws Exceptions\System\InvalidParameter
     * @throws \Exception
     */
    public function pushNamespace($namespace)
    {
        $class = $this->getConfigurationClassForNamespace($namespace);

        $this->bundleStack->pushBundle($class);

        return $this;
    }

    /**
     * Add a site to the domain extraction listing. If the current domain matches
     * the pattern entered as parameter number one the namespace will be returned.
     *
     * @param $domainPattern
     * @param $namespace
     * @return $this
     * @throws Exceptions\System\InvalidParameter
     */
    public function addSite($domainPattern, $namespace)
    {
        $config = $this->getConfigurationClassForNamespace($namespace);

        $this->allowableDomains[$domainPattern] = $config;

        return $this;
    }

    /**
     * @param $namespace
     * @return mixed
     * @throws Exceptions\System\InvalidParameter
     */
    private function getConfigurationClassForNamespace($namespace)
    {
        $configurationClass = $namespace . '\Configuration';

        if (!class_exists($configurationClass))
        {
            throw new \Slab\Exceptions\System\InvalidParameter("A configuration class was not found in the pushed namedspace " . $namespace);
        }

        $class = new $configurationClass();

        if (!($class instanceof \Slab\Components\BundleInterface))
        {
            throw new \Slab\Exceptions\System\InvalidParameter($class . " is not an instance of \Slab\Components\BundleInterface");
        }

        return $class;
    }

    /**
     * @return $this
     * @throws Exceptions\Exception
     * @throws Exceptions\System\InvalidParameter
     * @throws \Slab\Exceptions\System\InvalidParameter
     * @throws \Exception
     */
    public function pushNamespaceFromServerName()
    {
        if (empty($this->allowableDomains))
        {
            throw new \Slab\Exceptions\System\InvalidParameter("No allowable domains have been set to use the domain resolver.");
        }

        $namespace = $this->extractNamespaceFromServerName();

        $this->pushNamespace($namespace);

        return $this;
    }

    /**
     * Set default catch-all namespace
     *
     * @param $defaultNamespace
     * @return $this
     */
    public function setDefaultNamespace($defaultNamespace)
    {
        $this->defaultNamespace = $defaultNamespace;

        return $this;
    }

    /**
     * Set default server name
     *
     * @param $defaultServerName
     * @return $this
     */
    public function setDefaultServerName($defaultServerName)
    {
        $this->defaultServername = $defaultServerName;

        return $this;
    }

    /**
     * Boot the system
     */
    public function bootSystem()
    {
        $this->setCharacterEncoding();

        if (!empty($this->debug)) {
            $this->debug->endBenchmark('bootstrap');
        }

        try {
            if (!empty($this->debug)) {
                $this->debug->startBenchmark('system initialization');
            }

            $system = new \Slab\System($this->bundleStack);
            if (!empty($this->debug)) {
                $system->setDebug($this->debug);
                $this->debug->endBenchmark('system initialization');
            }

            $route = $system->routeRequest();
        } catch (\Slab\Exceptions\System\ObjectCreationFailure $exception) {
            $this->bootstrapError("Failed to create boot objects for SlabPHP system: " . $exception->getMessage());
            return;
        } catch (\Exception $exception) {
            $this->bootstrapError("Unknown exception occurred while booting SlabPHP:" . $exception->getMessage());
            return;
        }
    }

    /**
     * Turn on unicode character processing
     */
    public function setCharacterEncoding()
    {
        \mb_internal_encoding('UTF-8');
        \mb_http_output('UTF-8');
        \mb_http_input('UTF-8');
        \mb_language('uni');
        \mb_regex_encoding('UTF-8');

        if (php_sapi_name() != 'cli') {
            ob_start('mb_output_handler');
        }
    }

    /**
     * Return a namespace from a domain name
     *
     * @return string
     */
    public function extractNamespaceFromServerName()
    {
        if (php_sapi_name() == 'cli') {
            //In CLI mode, lets spoof the server variables so we can continue as if this is an HTTP request
            global $argv;

            //pop off index.php
            array_shift($argv);
            $url = array_shift($argv);

            //Parse entered URL
            $urlData = parse_url($url);

            //Make sure it's legit
            if (empty($urlData['host']) || empty($urlData['scheme'])) {
                $this->displayCLIUsage("Missing URL.");
                return null;
            }

            //Start spoofin'
            $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = $urlData['host'];

            if ($urlData['scheme'] == 'https') $_SERVER["HTTPS"] = 'on';

            $_SERVER['REQUEST_URI'] = $urlData['path'];
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

            //Set the query string if its available
            if (!empty($urlData['query'])) {
                parse_str($urlData['query'], $_GET);
            }

            //Continue as normal...
        }

        if (empty($_SERVER["SERVER_NAME"]) || $_SERVER["SERVER_NAME"] == $this->defaultServername) {
            return $this->defaultNamespace;
        }

        $serverName = strtolower($_SERVER["SERVER_NAME"]);

        //Loop through all ->addSite'd domains and check them against the pattern
        foreach ($this->allowableDomains as $domainPattern => $bundleConfiguration) {
            //A little short circuit
            if ($domainPattern == $serverName) return $bundleConfiguration->getNamespace();

            //Check servername against patterns for subdomains
            if (preg_match('#' . $domainPattern . '$#', $serverName)) {
                return $bundleConfiguration->getNamespace();
            }
        }

        error_log("Slab can't extract a namespace from server name '" . $serverName . "', returning landing page.");
        return $this->defaultNamespace;
    }

    /**
     * Display how to use the framework from a command line. Kills the app.
     * @param string $message
     */
    private function displayCLIUsage($message = null)
    {
        $message = "SlabPHP CLI" . ($message ? " Instructions" : ": " . $message) . PHP_EOL;
        $message .= "Usage: php index.php <site url>" . PHP_EOL;
        $message .= "For example, to emulate http://www.example.com/route/path/test you would run" . PHP_EOL;
        $message .= "  php index.php http://www.example.com/route/path/test" . PHP_EOL;

        exit($message);
    }

    /**
     * Wrapper around the bootstrap error handler
     *
     * @param string $errorMessage
     */
    private function bootstrapError($errorMessage)
    {
        error_log("BOOTSTRAP FATAL: " . $errorMessage);

        if (php_sapi_name() == 'cli')
        {
            $this->failBootstrapForCLI($errorMessage);
            return;
        }

        $this->failBootstrapForHTTP($errorMessage);
    }

    /**
     * Behavior for the site when site is in CLI mode and we fail bootstrapping
     *
     * @param string $errorMessage
     */
    private function failBootstrapForCLI($errorMessage)
    {
        echo $errorMessage . "\n";
    }

    /**
     * Behavior for the site when site is in HTTP mode and we fail bootstrapping
     *
     * @param string $errorMessage
     */
    private function failBootstrapForHTTP($errorMessage)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');

        $display = new \Slab\Display\Template();

        $display->renderTemplate('pages/errors/boot.php', ['error' => $errorMessage]);
        exit();
    }

}