<?php

namespace Slab\Tests\Mocks;

class Configuration extends \Slab\Bundle\Standard
{
    protected function getCurrentWorkingDirectory()
    {
        return __DIR__;
    }
}