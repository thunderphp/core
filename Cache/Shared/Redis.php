<?php
/** $Id$
 * Redis.php
 * Redis semi-native driver
 *
 * @version 1.0.0, $Revision$
 * @package Core\Cache\Shared
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Cache\Shared;

use \SessionHandlerInterface;
use Core\Events\EventDispatcherInterface;
use \Redis as RedisDriver;
use \RedisException;
use \Exception;

class Redis implements RedisInterface, EventDispatcherInterface {

    # TODO: Przenieść to do bardziej sensownej lokalizacji
    const DB_USER_SESSIONS  = 0;
    const DB_BROADCAST_LIST = 1;
    const DB_PRIVROOM_LIST  = 2;
    const DB_APP_CACHE      = 3;
    const DB_EVENT_QUEUE    = 4;
    const DB_BROADCAST_INFO = 5;
    const DB_USER_EVENTS    = 6;
    const DB_FIFO_BUFFER    = 7;

    # Instance of created class
    private static $instance = null;

    # Currently selected database for the connection
    protected $database = 0;

    /** @var RedisDriver  */
    protected $redis = null;

    /** Redis constructor.
     * @param string $host Can be a host, or the path to a unix domain socket.
     * @param int $port Port 6379 by default
     * @param null $pass Authenticate the connection using a password. <i>Warning: The password is sent in plain-text over the network.</i>
     * @param string $persistent_id Identity for the requested persistent connection.
     * @param int|float $timeout Value in seconds.
     * @param int $retry_interval Value in milliseconds.
     * @throws \RedisException
     */
    public function __construct($host = 'localhost', $port = 6379, $pass = null, $persistent_id = null, $timeout = 0, $retry_interval = 0){

        # Create new driver instance
        $this->redis = new RedisDriver();

        if($persistent_id === null){
            # Connects to a Redis instance.
            die('non persistent connect');
            $result = $this->redis->connect($host, intval($port), floatval($timeout), null, $retry_interval);
        } else {
            # Connects to a Redis instance or reuse a connection already established.
            $result = $this->redis->pconnect($host, $port, floatval($timeout), $persistent_id, $retry_interval);

        }

        # Check if connection established succesfully.
        if($result === false) throw new RedisException("Failed to establishing connection to redis server.");

        if($pass !== null && is_string($pass)){

            # Authenticate the connection using a password.
            $result = $this->redis->auth($pass);

            # Check if authenticate was succesfully.
            if($result === false) throw new RedisException("Failed to authentication redis connection. Bad password?");
        }

        # Save instance to static field
        self::$instance = $this;
    }

    public function __destruct() {

        # Disconnects from the Redis instance, except when pconnect is used.
        $this->redis->close();
    }

    /** Returns class instances, if any was created, throws an exception otherwise.
     * @return \Core\Cache\Shared\Redis
     * @throws \Exception
     */
    final public static function getInstance() {

        if(!self::$instance instanceof Redis){
            throw new Exception('An instance of Redis class hasn\'t been created yet, but '.static::class.'::getInstance was called!');
        }

        return self::$instance;
    }

    /** Get Redis native driver.
     * @return \Redis
     */
    public function getDriver(){
        return $this->redis;
    }

    /** Set the string value in argument as value of the key.
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value) {
        $this->redis->set($name, strval($value));
    }

    /** Get the value related to the specified key.
     * @param string $name
     * @return bool|string
     */
    public function __get($name) {
        return $this->redis->get($name);
    }

    /** Change the selected database for the current connection.
     * @param $dbindex
     * @return bool TRUE in case of success, FALSE in case of failure.
     */
    public function select($dbindex = 0){

        # If database is allready selected, just return
        if($dbindex == $this->database) return true;

        # Send command to select database
        $result = $this->redis->select($dbindex);

        # Write database in local variable
        $this->database = $dbindex;

        # Return the result
        return $result;
    }

    /** Sets an expiration date or remove the expiration timer from a key or just returns the TTL left for a given key in seconds.
     * @param string $key Key to set or read.
     * @param null $ttl If $ttl is set to null method returns TTL value, if false - set key persist, integer od double set value in seconds.
     * @return bool|int
     */
    public function expire($key, $ttl = null){

        if($ttl === null){

            # For null $ttl, get TTL value from get and return
            $result = $this->redis->ttl(strval($key));
        } else if ($ttl === false){

            # For false $ttl, set key as persist
            $result = $this->redis->persist(strval($key));
        } else {

            # For int or double, set key TTL
            if(doubleval($ttl)){
                $pttl = ceil($ttl * 1000);
                $result = $this->redis->pExpire(strval($key), $pttl);
            } else {
                $result = $this->redis->expire(strval($key), intval($ttl));
            }
        }

        # Return value.
        return $result;
    }

    /** Get the value of a key.
     * @param string $key
     * @return mixed If key didn't exist, FALSE is returned. Otherwise, the value related to this key is returned.
     */
    public function get($key){

        # Get data from redis
        return $this->redis->get(strval($key));
    }

    /** Set the string value in argument as value of the key
     * @param string $key
     * @param string $value
     * @return bool True if the command is successful.
     */
    public function set($key, $value){

        return $this->redis->set(strval($key), strval($value));
    }

    /** Remove specified keys.
     * @param string|array $key Key to remove, or array of keys.
     * @return int Number of keys deleted.
     */
    public function delete($key){

        return $this->redis->del(strval($key));
    }

    /** Set the string value in argument as value of the key, with a time to live.
     * @param string $key
     * @param string $value
     * @param int $ttl Sets an expiration date (a timeout) on an item.
     * @return bool True if the command is successful.
     */
    public function setEx($key, $value, $ttl = 0){

        return $this->redis->setex(strval($key), $ttl, strval($value));
    }

    /** Set the string value in argument as value of the key if the key doesn't already exist in the database.
     * @param string $key
     * @param string $value
     * @param int $ttl Sets an expiration date (a timeout) on an item.
     * @return bool True if the command is successful.
     */
    public function setNx($key, $value, $ttl = null){

        # Set the string value if the key doesn't already exist.
        $result = $this->redis->setnx(strval($key), strval($value));

        # Sets an expiration date (a timeout) on an item.
        if($result == true && $ttl !== null) $this->redis->expire(strval($key), $ttl);

        # Return the result
        return $result;
    }

    /** Sets a value and returns the previous entry at that key.
     * @param string $key
     * @param string $value
     * @param int $ttl Sets an expiration date (a timeout) on an item.
     * @return string A string, the previous value located at this key.
     */
    public function getSet($key, $value, $ttl = null){

        # Set the string value if the key doesn't already exist.
        $result = $this->redis->getSet(strval($key), strval($value));

        # Sets an expiration date (a timeout) on an item.
        if($result != false && $ttl !== null) $this->redis->expire(strval($key), $ttl);

        # Return the result
        return $result;
    }

    /** Get the values of all the specified keys. If one or more keys dont exist, the array will contain FALSE at the position of the key.
     * @param array $keys Array containing the list of the keys.
     * @return array Array containing the values related to keys in argument.
     */
    public function mGet(array $keys){

        return $this->redis->mget($keys);
    }

    /** Sets multiple key-value pairs in one atomic command.
     * @param array $data_array Pairs: array(key => value, ...)
     * @return bool True in case of success, false in case of failure.
     */
    public function mSet(array $data_array){

        # Cast values to string (non-string values may cause fatal error in php-redis module)
        foreach($data_array as $hashKey => $value){
            $data_array[$hashKey] = strval($value);
        }

        return $this->redis->mset($data_array);
    }

    /** Gets a value from the hash stored at key. If the hash table doesn't exist, or the key doesn't exist, FALSE is returned.
     * @param string $key
     * @param string $hashKey
     * @return string|bool The value, if the command executed successfully FAalse in case of failure.
     */
    public function hGet($key, $hashKey){

        return $this->redis->hGet(strval($key), $hashKey);
    }

    /** Returns the whole hash, as an array of strings indexed by strings.
     * @param string $key
     * @return array|bool An array of elements, the contents of the hash.
     */
    public function hGetAll($key){

        return $this->redis->hGetAll(strval($key));
    }

    /** Retrieve the values associated to the specified fields in the hash.
     * @param string $key
     * @param array $fields
     * @return array An array of elements, the values of the specified fields in the hash, with the hash keys as array keys.
     */
    public function hMGet($key, array $fields){

        return $this->redis->hMGet(strval($key), $fields);
    }

    /** Adds a value to the hash stored at key.
     * @param string $key
     * @param string $hashKey
     * @param $value
     * @return int|bool 1 if value didn't exist and was added successfully, 0 if the value was already present and was replaced, FALSE if there was an error.
     */
    public function hSet($key, $hashKey, $value){

        return $this->redis->hSet(strval($key), strval($hashKey), strval($value));
    }

    /** Adds a value to the hash stored at key only if this field isn't already in the hash.
     * @param string $key
     * @param string $hashKey
     * @param $value
     * @return bool True if the field was set, False if it was already present.
     */
    public function hSetNx($key, $hashKey, $value){

        return $this->redis->hSetNx(strval($key), strval($hashKey), strval($value));
    }

    /** Fills in a whole hash. Non-string values are converted to string, using the standard (string) cast. NULL values are stored as empty strings.
     * @param string $key
     * @param array $values
     * @return bool
     */
    public function hMSet($key, array $values){

        # Cast values to string (non-string values may cause fatal error in php-redis module)
        foreach($values as $hashKey => $value){
            $values[$hashKey] = strval($value);
        }

        return $this->redis->hMset(strval($key), $values);
    }

    /** Returns the keys in a hash, as an array of strings.
     * @param string $key
     * @return array An array of elements, the keys of the hash. This works like PHP's array_keys().
     */
    public function hKeys($key){

        return $this->redis->hKeys(strval($key));
    }

    /** Removes a value from the hash stored at key.
     * @param string $key
     * @param string $hashKey
     * @return bool If the hash table doesn't exist, or the key doesn't exist, FALSE is returned, TRUE in case of success.
     */
    public function hDelete($key, $hashKey){

        return $this->redis->hDel(strval($key), strval($hashKey));
    }

    /** Verify if the specified member exists in a key.
     * @param string $key
     * @param string $hashKey
     * @return bool If the member exists in the hash table, return TRUE, otherwise return FALSE.
     */
    public function hExists($key, $hashKey) {

        return $this->redis->hExists(strval($key), strval($hashKey));
    }

    /** Increments the value of a member from a hash by a given amount.
     * @param string $key
     * @param string $hashKey
     * @param int $value
     * @return int|float The new value
     */
    public function hIncrBy($key, $hashKey, $value){

        return $this->redis->hIncrBy(strval($key), $hashKey, $value);
    }


    /** Enter transactional mode.
     * @param int $type
     * @return \Redis
     */
    public function multi($type = RedisDriver::MULTI){
        return $this->redis->multi($type);
    }

    /** Performs the user function in transactional mode.
     * @param callable $transaction
     * @param int $type
     * @return array
     */
    public function transaction(callable $transaction, $type = RedisDriver::MULTI){

        /** @var RedisDriver $multi */
        $multi = $this->redis->multi($type);

        $transaction( $multi );

        return $multi->exec();
    }

    public function publish($channel, $message){
        return $this->redis->publish($channel, $message);
    }

}