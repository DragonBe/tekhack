<?php

class Cache
{
    const PREFIX = 'cache-tekhack---';
    protected $_path;

    public function __construct()
    {
        $this->_path = realpath(__DIR__ . '/../../cache');
    }

    public function load($key)
    {
        $file = sprintf('%s/%s-%s',
            $this->_path, self::PREFIX, $key);
        if (!file_exists($file)) {
            return false;
        }
        $data = file_get_contents($file);
        return unserialize($data);
    }

    public function save($key, $data)
    {
        $file = sprintf('%s/%s-%s',
            $this->_path, self::PREFIX, $key);
        file_put_contents($file, serialize($data));
    }
}