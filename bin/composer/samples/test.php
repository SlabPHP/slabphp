<?php
/**
 * Homepage Test
 *
 * @package APPNAMESPACE
 * @subpackage Tests
 * @author SlabSetup
 */
namespace APPNAMESPACE\Tests;

class HomepageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test controller
     */
    public function testController()
    {
        $controller =  new \APPNAMESPACE\Controllers\Homepage();

        $route = new \Slab\Tests\Components\Mocks\Router\Route();
        $system = new \Slab\Tests\Components\Mocks\System();

        $controller->setSystemReference($system);
        $controller->setRouteReference($route);
        $response = $controller->executeControllerLifecycle();

        $data = $response->getData();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($data->template);
        $this->assertNotEmpty($data->subTemplate);
        $this->assertNotEmpty($data->title);
        $this->assertNotEmpty($data->description);
    }
}