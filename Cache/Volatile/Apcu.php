<?php

/** $Id$
 * Apcu.php
 *
 * @version 1.0.0, $Revision$
 * @package Core\Cache\Volatile
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Cache\Volatile;

use Core\Exceptions\MissingModuleException;


class Apcu {

    # Instance of created class
    private static $instance = null;

    /** Return instance of Apcu class
     * @return \Core\Cache\Volatile\Apcu
     */
    public static function getInstance(){
        if(!self::$instance instanceof Apcu){
            self::$instance = new Apcu();
        }
        return self::$instance;
    }

    private function __construct() {
        if(!extension_loaded('apcu') && !!extension_loaded('apc')){
            throw new MissingModuleException('apcu');
        }
    }

    public function __set($key, $value) {
        $this->add((string)$key, $value);
    }

    public function __get($key) {
        return $this->get((string)$key);
    }

    /** Cache a variable in the data store.
     * @param $key
     * @param $value
     * @param int $ttl
     * @return mixed
     */
    public function add($key, $value, $ttl = 0){
        return apcu_store((string)$key, $value, $ttl);
    }

    /** Cache an array in the data store.
     * @param array $values
     * @param int $ttl
     * @return mixed
     */
    public function add_array(array $values, $ttl = 0){
        return apcu_store($values, null, $ttl);
    }

    /** Fetch a stored variable from the cache.
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null) {
        $success = null;
        $value = apcu_fetch((string)$key, $success);
        if(!$success) return $default;
        return $value;
    }

    /** Atomically fetch or generate a cache entry.
     * @param string $key Identity of cache entry
     * @param callable $func A callable that accepts key as the only argument and returns the value to cache.
     * @param int $ttl Time To Live
     * @return mixed
     */
    public function entry($key, callable $func, $ttl = 0){

        # Try to fetch cache entry
        $value = $this->get($key);

        # If cache entry is empty, run generator function
        if($value === null){
            $value = $func((string)$key);
            $this->add($key, $value, $ttl);
        }

        # Return value
        return $value;
    }

    /** Removes a stored variable from the cache.
     * @param $key
     */
    public function delete($key){
        apcu_delete((string)$key);
    }

    /** Clears the APCu cache.
     */
    public function clear(){
        apcu_clear_cache();
    }

}


