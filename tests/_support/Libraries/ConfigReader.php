<?php

namespace Tests\Support\Libraries;

use Config\App;

/**
 * An extension of BaseConfig that prevents the constructor from
 * loading external values. Used to read actual local values from
 * a config file.
 */
class ConfigReader extends App
{
    public function __construct()
    {
    }
}
