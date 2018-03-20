<?php
/**
 * Class SlabSetup
 *
 * @package Slab
 * @subpackage Scripts
 * @author Eric
 */
class SlabSetup
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $namespace;

    /**
     * SlabSetup constructor.
     */
    public function __construct()
    {
        global $argv;

        if (!empty($argv[1]))
        {
            $this->directory = realpath($argv[1]);
        }

        if (!empty($argv[2]))
        {
            $this->namespace = ucfirst($argv[2]);
        }
    }

    /**
     * @param $filename
     * @return string
     */
    private function getSample($filename)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Setup a project directory
     */
    public function setupProject()
    {
        if (empty($this->directory) || !is_dir($this->directory))
        {
            $this->printInstructions();
            return;
        }

        echo 'Attempting to run composer install...';
        exec('composer install -d ' . $this->directory);

        echo ($this->initializeConfiguration() ? 'Configuration initialized.' : 'Configuration did not complete.') . PHP_EOL;
        echo ($this->initializePublic() ? 'Public initialized.' : 'Public did not complete.') . PHP_EOL;
        echo ($this->initializeSrc() ? 'Src initialized.' : 'Src did not complete.') . PHP_EOL;
        echo ($this->initializeTests() ? 'Test initialized.' : 'Test did not complete.') . PHP_EOL;
        echo ($this->initializeNamespace() ? 'Namespace initialized.' : 'Namespace initialization did not complete.') . PHP_EOL;

        echo 'Attempting to update composer autoloader...';
        exec('composer dump-autoload -d ' . $this->directory);

        echo 'Done!' . PHP_EOL;
    }

    /**
     * @return bool
     */
    private function initializeConfiguration()
    {
        $dir = $this->directory . DIRECTORY_SEPARATOR . 'config';
        if (!$this->createDirIfNotExists($dir)) return false;

        if (!$this->installFile('config.php', $dir . DIRECTORY_SEPARATOR . 'default.php')) return false;

        if (empty($this->namespace))
        {
            echo 'No namespace specified, skipping creation of routes file.' . PHP_EOL;
            return true;
        }

        return $this->installFile('routes.xml', $dir . DIRECTORY_SEPARATOR . 'routes.xml');
    }

    /**
     * @return bool
     */
    private function initializeTests()
    {
        $dir = $this->directory . DIRECTORY_SEPARATOR . 'tests';
        if (!$this->createDirIfNotExists($dir)) return false;

        if (empty($this->namespace))
        {
            echo 'No namespace specified, skipping the rest of the test initialization.' . PHP_EOL;
            return true;
        }

        if (!$this->createDirIfNotExists($dir . DIRECTORY_SEPARATOR . 'Controllers')) return false;

        return $this->installFile('test.php', $dir . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'HomepageTest.php');
    }

    /**
     * @return bool
     */
    private function initializeSrc()
    {
        $dir = $this->directory . DIRECTORY_SEPARATOR . 'src';

        if (!$this->createDirIfNotExists($dir)) return false;

        if (empty($this->namespace))
        {
            echo 'No namespace specified, skipping the rest of the controller initialization.' . PHP_EOL;
            return true;
        }

        if (!$this->installFile('configuration.php', $dir . DIRECTORY_SEPARATOR . 'Configuration.php')) return false;

        if (!$this->createDirIfNotExists($dir . DIRECTORY_SEPARATOR . 'Controllers')) return false;

        if (!$this->installFile('controller.php', $dir . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Homepage.php')) return false;

        if (!$this->createDirIfNotExists($dir . DIRECTORY_SEPARATOR . 'views')) return false;

        if (!$this->createDirIfNotExists($dir . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'pages')) return false;

        return $this->installFile('template.php', $dir . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . 'homepage.php');
    }

    /**
     * @return bool
     */
    private function initializePublic()
    {
        $dir = $this->directory . DIRECTORY_SEPARATOR . 'public';

        if (!$this->createDirIfNotExists($dir)) return false;

        echo 'Copying default SlabPHP public directory.' . PHP_EOL;

        $slabPublic = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public');
        $this->copyDirectory($slabPublic, $dir);

        if (!empty($this->namespace))
        {
            return $this->installFile('index.php', $dir . DIRECTORY_SEPARATOR . 'index.php');
        }

        return true;
    }

    /**
     * @param $source
     * @param $destination
     */
    private function copyDirectory($source, $destination)
    {
        $files = scandir($source);
        foreach ($files as $file)
        {
            if ($file == '.' || $file == '..' || !is_readable($source . DIRECTORY_SEPARATOR . $file)) continue;

            if (is_dir($source . DIRECTORY_SEPARATOR . $file))
            {
                mkdir($destination . DIRECTORY_SEPARATOR . $file);
                $this->copyDirectory($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file);
            }
            else
            {
                echo 'Copying ' . $source . DIRECTORY_SEPARATOR . $file . ' to ' . $destination . DIRECTORY_SEPARATOR . $file . PHP_EOL;
                copy($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file);
            }
        }
    }

    /**
     * @return bool
     */
    private function initializeNamespace()
    {
        if (empty($this->namespace))
        {
            echo 'No namespace specified so skipping composer.json modification.' . PHP_EOL;
            return true;
        }

        if (!file_exists($this->directory . DIRECTORY_SEPARATOR . 'composer.json')) return false;

        $data = file_get_contents($this->directory . DIRECTORY_SEPARATOR . 'composer.json');

        if (empty($data)) return false;

        $info = json_decode($data);

        if (empty($info)) return false;

        if (empty($info->autoload)) $info->autoload = new \stdClass();
        if (empty($info->autoload->{'psr-4'})) $info->autoload->{'psr-4'} = new \stdClass();
        $info->autoload->{'psr-4'}->{$this->namespace . '\\'} = 'src/';
        $info->autoload->{'psr-4'}->{$this->namespace . '\\Tests\\'} = 'tests/';

        $data = json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return file_put_contents($this->directory . DIRECTORY_SEPARATOR . 'composer.json', $data);
    }

    /**
     * @param $sampleFileSource
     * @param $fileDestination
     * @return bool
     */
    private function installFile($sampleFileSource, $fileDestination)
    {
        $data = file_get_contents($this->getSample($sampleFileSource));

        $data = str_replace(
            ['APPNAMESPACE', 'TIMESTAMP'],
            [$this->namespace, date('Y-m-d H:i:s')],
            $data);

        if (empty($data)) return false;

        echo 'Installing file ' . $fileDestination . '.' . PHP_EOL;
        if (!file_put_contents($fileDestination, $data)) return false;

        return true;
    }

    /**
     * @param $directory
     * @return bool
     */
    private function createDirIfNotExists($directory)
    {
        if (!is_dir($directory))
        {
            echo 'Creating directory ' . $directory . '.' . PHP_EOL;
            return mkdir($directory);
        }

        echo 'The directory ' . $directory . ' already exists.' . PHP_EOL;

        return false;
    }

    /**
     * Print instructions
     */
    private function printInstructions()
    {
        echo 'SlabPHP Project Initialization Script' . PHP_EOL;
        echo 'Usage: php ' . __FILE__ . ' <directory> <namespace>' . PHP_EOL . PHP_EOL;
    }
}

$slabSetup = new SlabSetup();
$slabSetup->setupProject();