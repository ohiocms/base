<?php

namespace Belt\Core;

use Belt;
use Illuminate\Filesystem\FilesystemManager;

/**
 * Class BeltSingleton
 * @package Belt\Core
 */
class BeltSingleton
{

    /**
     * @var array
     */
    private $packages = [];

    /**
     * @var array
     */
    private $publish = [];

    /**
     * @var array
     */
    private $seeders = [];

    /**
     * @param null $key
     * @return array
     */
    public function publish($key = null)
    {
        if (!$key) {
            return $this->publish;
        }

        if (!in_array($key, $this->publish)) {
            array_push($this->publish, $key);
        }
    }

    /**
     * @param $key
     * @param array $params
     * @return array
     */
    public function addPackage($key, $params = [])
    {
        return $this->packages[$key] = $params;
    }

    /**
     * @param null $key
     * @return array
     */
    public function packages($key = null, $params = [])
    {
        if (!$key) {
            return $this->packages;
        }

        return array_get($this->packages, $key);
    }

    /**
     * @param null $key
     * @return array
     */
    public function seeders($key = null)
    {
        if (!$key) {
            return $this->seeders;
        }

        if (!in_array($key, $this->seeders)) {
            array_push($this->seeders, $key);
        }
    }

}