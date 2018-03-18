<?php
/**
 * System Tests
 *
 * @package Slab
 * @subpackage Tests
 * @author Eric
 */
namespace Slab\Tests;

class SystemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws \Exception
     * @throws \Slab\Exceptions\System\ObjectCreationFailure
     */
    public function testSystemCreation()
    {
        $_SERVER['SERVER_NAME'] = 'www.example.com';

        //This needs to exist
        if (!class_exists('\Monolog\Logger'))
        {
            $mock = $this
                ->getMockBuilder('\Monolog\Logger')
                ->getMock();
        }

        $config = new \Slab\Configuration();
        $bundleStack = new \Slab\Bundle\Stack($config);

        $system = new \Slab\System($bundleStack);

        $this->assertEquals(null, $system->cache());
        $this->assertEquals(null, $system->db());
        $this->assertInstanceOf('\Slab\Session\Driver', $system->session());
        $this->assertInstanceOf('\Slab\Router\Router', $system->router());
        $this->assertInstanceOf('\Slab\Input\Manager', $system->input());
        $this->assertInstanceOf('\Slab\Configuration\Manager',$system->config());
    }
}