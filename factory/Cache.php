<?php

class Cache {
    private $_provider = null;
    private static $_defaultTTL = false; // 2 hours
    private static $_instance = null;

    public static function getInstance(){
        if(!isset(self::$_instance)){
            self::$_instance = new Cache();
        }
        return self::$_instance;
    }

    private function getProvider(){
        return $this->_provider;
    }

    public function __construct() {
        if (!class_exists('Memcache')) {
            $this->_provider = false;
            return;
        }

        $this->_provider = new Memcache();
        if (!($connected = $this->_provider->connect(Config::cache_host, Config::cache_port))) {
            Logger::addError('Could not connect to memcached server');
            $this->_provider = false;
        }
    }

    public static function get($key, $tag = null) {
        if (!self::getInstance()->getProvider()) {
            return false;
        }

        if (!is_null($tag) && is_string($tag)) {
            $key = self::tagKey($tag, $key);
        }
        return self::getInstance()->getProvider()->get($key);
    }

    public static function set($key, $object, $tag = null, $ttl = -1) {
        ($ttl !== false && $ttl < 0) && $ttl = self::$_defaultTTL;

        if ($ttl === false) {
            Logger::addDebug('Salteando cache para ' . $tag);
            return false;
        }

        if (!self::getInstance()->getProvider()) {
            return false;
        }

        if (!is_null($tag) && is_string($tag)) {
            return self::set(self::tagKey($tag, $key), $object, null, $ttl);
        }

        if (!($inserted = self::getInstance()->getProvider()->set($key, $object, false, $ttl))) {
            Logger::addError('Could not insert into memcached server (key: ' . $key . ')');
        }
        return $inserted;
    }

    public static function deleteAllByTag($tag) {
        if (!self::getInstance()->getProvider()) {
            return false;
        }

        return self::set('tags::' . $tag, self::getRandomHash(), null, 0);
    }

    public static function stats() {
        if (!self::getInstance()->getProvider()) {
            return false;
        }

        return self::getInstance()->getProvider()->getStats();
    }

    private static function tagKey($tag, $key) {
        return self::getTagCurrentId($tag) . '_' . $key;
    }

    private static function getTagCurrentId($tag) {
        if (!self::getInstance()->getProvider()) {
            return false;
        }

        $currentId = self::get('tags::' . $tag);

        if (!$currentId) {
            $currentId = self::getRandomHash();
            self::set('tags::' . $tag, $currentId, null, 0);
        }

        return $currentId;
    }

    private static function getRandomHash() {
        return substr(md5(time() . rand(0, 7777777)), 0, 7);
    }
}

?>
