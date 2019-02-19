<?php
/**
 * Configuration Object Test
 *
 * @package Slab
 * @subpackage Tests
 * @author Eric
 */
namespace Slab\Tests;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test configuration object creation
     */
    public function testConfigurationConstruction()
    {
        $configuration = new \Slab\Configuration();

        $this->assertEquals('Slab', $configuration->getNamespace());

        $slabDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');

        $this->assertEquals($slabDir . DIRECTORY_SEPARATOR . 'src', $configuration->getSourceDirectory());
        $this->assertEquals($slabDir, $configuration->getPackageDirectory());
        $this->assertEquals($slabDir . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'views', $configuration->getViewDirectory());
        $this->assertEquals($slabDir . DIRECTORY_SEPARATOR . 'config', $configuration->getConfigDirectory());
        $this->assertEquals($slabDir . DIRECTORY_SEPARATOR . 'resources', $configuration->getResourceDirectory());
    }

    /**
     * Test default component creation
     */
    public function testDefaultComponentCreation()
    {
        $_SERVER['SERVER_NAME'] = 'example.com';

        $configuration = new \Slab\Configuration();

        $system = new \Slab\Tests\Components\Mocks\System();

        $this->assertEquals(null, $configuration->getCacheProvider($system));
        $this->assertEquals(null, $configuration->getDatabaseProvider($system));
        //$this->assertInstanceOf('\Slab\Session\Handlers\File', $configuration->getSessionHandler($system));
        $this->assertInstanceOf('\Slab\Router\Router', $configuration->getRouter($system));
        $this->assertInstanceOf('\Slab\Input\Manager', $configuration->getInputManager());
        $this->assertInstanceOf('\Slab\Configuration\Manager',$configuration->getConfigurationManager($system));

        if (!class_exists('\Monolog\Logger'))
        {
            $this->assertNull($configuration->getLogger());

            $mock = $this
                ->getMockBuilder('\Monolog\Logger')
                ->getMock();
        }

        $this->assertInstanceOf('\Monolog\Logger', $configuration->getLogger());
    }
}
