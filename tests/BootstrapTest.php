<?php
/**
 * Bootstrap Tests
 *
 * @package Slab
 * @subpackage Tests
 * @author Eric
 */
namespace Slab\Tests;

class BootstrapTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws \Exception
     */
    public function testBootStrapCreation()
    {
        $bootstrap = new \Slab\Bootstrap(__DIR__ . '/public');

        $bootstrap->pushNamespace('Slab\Tests\Mocks');
    }
}