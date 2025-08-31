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
		$this->_provider = new Memcache();
		if (!($connected = $this->_provider->connect(Config::cache_host, Config::cache_port))) {
			Logger::addError('Could not connect to memcached server');
		}
	}

	public static function get($key, $tag = null) {
		if (!is_null($tag) && is_string($tag)) {
			$key = self::tagKey($tag, $key);
		}
		return self::getInstance()->getProvider()->get($key);
	}

	public static function set($key, $object, $tag = null, $ttl = -1) {
		($ttl !== false && $ttl < 0) && $ttl = self::$_defaultTTL;

		// Check if we need to cache
		if ($ttl === false) {
			Logger::addDebug('Salteando cache para ' . $tag);
			return false;
		}

		// Check if its a "tagged" key
		if (!is_null($tag) && is_string($tag)) {
			return self::set(self::tagKey($tag, $key), $object, null, $ttl);
		}

		// Now we finally store
		if (!($inserted = self::getInstance()->getProvider()->set($key, $object, false, $ttl))) {
			Logger::addError('Could not insert into memcached server (key: ' . $key . ')');
		}
		return $inserted;
	}

	public static function deleteAllByTag($tag) {
		// Its a fake delete. We just override the tag prefix
		return self::set('tags::' . $tag, self::getRandomHash(), null, 0); // Forever
	}

	public static function stats() {
		return self::getInstance()->getProvider()->getStats();
	}

	// Private auxiliary functions

	private static function tagKey($tag, $key) {
		return self::getTagCurrentId($tag) . '_' . $key;
	}

	private static function getTagCurrentId($tag) {
		// Search the current prefix for the given tag
		$currentId = self::get('tags::' . $tag);

		// If there's no tag yet, we create one random
		if (!$currentId) {
			$currentId = self::getRandomHash();
			self::set('tags::' . $tag, $currentId, null, 0); // Forever
		}

		return $currentId;
	}

	private static function getRandomHash() {
		return substr(md5(time() . rand(0, 7777777)), 0, 7);
	}
}

?>