<?php

class Cache
{
    const PREFIX = 'cache-tekhack---';
    const TTL = 3600;

    /**
     * @var string The path of the cache
     */
    protected $_path;
    /**
     * @var int Time to live of the cache
     */
    protected $_ttl;

    /**
     * Constructor for this cache class
     *
     * @param null|int $ttl The time-to-live of the cache
     */
    public function __construct($ttl = null)
    {
        $this->_path = realpath(__DIR__ . '/../../cache');
        if (null === $ttl) {
            $this->_ttl = self::TTL;
        }
    }

    /**
     * Load an object from cache
     *
     * @param $key The label for the cached object
     * @return bool|mixed
     */
    public function load($key)
    {
        $this->cleanUp();
        $file = sprintf('%s/%s-%s',
            $this->_path, self::PREFIX, $key);
        if (!file_exists($file)) {
            return false;
        }
        $data = file_get_contents($file);
        return unserialize($data);
    }

    /**
     * Store an object to cache (it will be automatically serialized)
     *
     * @param $key The label for the cached object
     * @param $data The data object you want to cache
     */
    public function save($key, $data)
    {
        $file = sprintf('%s/%s-%s',
            $this->_path, self::PREFIX, $key);
        file_put_contents($file, serialize($data));
    }

    /**
     * Cleanup routine to clear cache entries that are older then provided
     * time-to-live
     */
    public function cleanUp()
    {
        $dirIt = new DirectoryIterator($this->_path);
        while ($dirIt->valid()) {
            $file = $dirIt->current();
            if (self::PREFIX === substr($file->getFilename(), 0, strlen(self::PREFIX))) {
                $timeout = new DateTime(strftime('%c', time() - $this->_ttl));
                if ($file->getMTime() < $timeout->format('U')) {
                    unlink ($file->getFileInfo());
                }
            }
            $dirIt->next();
        }
    }
}