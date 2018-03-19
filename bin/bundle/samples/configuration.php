<?php
/**
 * Configuration Object
 *
 * @package APPNAMESPACE
 * @author SlabSetup
 */
namespace APPNAMESPACE;

class Configuration extends \Slab\Bundle\Standard
{
    /**
     * @return string
     */
    protected function getCurrentWorkingDirectory()
    {
        return __DIR__;
    }
}