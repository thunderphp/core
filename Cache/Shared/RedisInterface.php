<?php
/** $Id$
 * RedisInterface.php
 *
 * @version 1.0.0, $Revision$
 * @package Core\Cache\Shared
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Cache\Shared;


interface RedisInterface {

    /** Redis constructor.
     * @param string $host Can be a host, or the path to a unix domain socket.
     * @param int $port Port 6379 by default
     * @param null $pass Authenticate the connection using a password. <i>Warning: The password is sent in plain-text over the network.</i>
     * @param string $persistent_id Identity for the requested persistent connection.
     * @param int|float $timeout Value in seconds.
     * @param int $retry_interval Value in milliseconds.
     * @throws \RedisException
     */
    public function __construct($host = 'localhost', $port = 6379, $pass = null, $persistent_id = null, $timeout = 0, $retry_interval = 0);

    /** Returns class instances, if any was created, throws an exception otherwise.
     * @return \Core\Cache\Shared\Redis
     * @throws \Exception
     */
    public static function getInstance();

    /** Get Redis native driver.
     * @return \Redis
     */
    public function getDriver();

    /** Set the string value in argument as value of the key.
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value);

    /** Get the value related to the specified key
     * @param string $name
     * @return bool|string
     */
    public function __get($name);

    /** Change the selected database for the current connection.
     * @param $dbindex
     * @return bool TRUE in case of success, FALSE in case of failure.
     */
    public function select($dbindex = 0);

    /** Sets an expiration date or remove the expiration timer from a key or just returns the TTL left for a given key in seconds.
     * @param string $key Key to set or read.
     * @param null $ttl If $ttl is set to null method returns TTL value, if false - set key persist, integer od double set value in seconds.
     * @return bool|int
     */
    public function expire($key, $ttl = null);

    /** Get the value of a key.
     * @param string $key
     * @return mixed If key didn't exist, FALSE is returned. Otherwise, the value related to this key is returned.
     */
    public function get($key);

    /** Set the string value in argument as value of the key
     * @param string $key
     * @param string $value
     * @return bool True if the command is successful.
     */
    public function set($key, $value);

    /** Remove specified keys.
     * @param string|array $key Key to remove, or array of keys.
     * @return int Number of keys deleted.
     */
    public function delete($key);

    /** Set the string value in argument as value of the key, with a time to live.
     * @param string $key
     * @param string $value
     * @param int $ttl Sets an expiration date (a timeout) on an item.
     * @return bool True if the command is successful.
     */
    public function setEx($key, $value, $ttl = 0);

    /** Set the string value in argument as value of the key if the key doesn't already exist in the database.
     * @param string $key
     * @param string $value
     * @param int $ttl Sets an expiration date (a timeout) on an item.
     * @return bool True if the command is successful.
     */
    public function setNx($key, $value, $ttl = null);

    /** Sets a value and returns the previous entry at that key.
     * @param string $key
     * @param string $value
     * @param int $ttl Sets an expiration date (a timeout) on an item.
     * @return string A string, the previous value located at this key.
     */
    public function getSet($key, $value, $ttl = null);

    /** Get the values of all the specified keys. If one or more keys dont exist, the array will contain FALSE at the position of the key.
     * @param array $keys Array containing the list of the keys.
     * @return array Array containing the values related to keys in argument.
     */
    public function mGet(array $keys);

    /** Sets multiple key-value pairs in one atomic command.
     * @param array $data_array Pairs: array(key => value, ...)
     * @return bool True in case of success, false in case of failure.
     */
    public function mSet(array $data_array);

    /** Gets a value from the hash stored at key. If the hash table doesn't exist, or the key doesn't exist, FALSE is returned.
     * @param string $key
     * @param string $hashKey
     * @return string|bool The value, if the command executed successfully FAalse in case of failure.
     */
    public function hGet($key, $hashKey);

    /** Returns the whole hash, as an array of strings indexed by strings.
     * @param string $key
     * @return array|bool An array of elements, the contents of the hash.
     */
    public function hGetAll($key);

    /** Retrieve the values associated to the specified fields in the hash.
     * @param string $key
     * @param array $fields
     * @return array An array of elements, the values of the specified fields in the hash, with the hash keys as array keys.
     */
    public function hMGet($key, array $fields);

    /** Adds a value to the hash stored at key.
     * @param string $key
     * @param string $hashKey
     * @param $value
     * @return int|bool 1 if value didn't exist and was added successfully, 0 if the value was already present and was replaced, FALSE if there was an error.
     */
    public function hSet($key, $hashKey, $value);

    /** Adds a value to the hash stored at key only if this field isn't already in the hash.
     * @param string $key
     * @param string $hashKey
     * @param $value
     * @return bool True if the field was set, False if it was already present.
     */
    public function hSetNx($key, $hashKey, $value);

    /** Fills in a whole hash. Non-string values are converted to string, using the standard (string) cast. NULL values are stored as empty strings.
     * @param string $key
     * @param array $values
     * @return bool
     */
    public function hMSet($key, array $values);

    /** Returns the keys in a hash, as an array of strings.
     * @param string $key
     * @return array An array of elements, the keys of the hash. This works like PHP's array_keys().
     */
    public function hKeys($key);

    /** Removes a value from the hash stored at key.
     * @param string $key
     * @param string $hashKey
     * @return bool If the hash table doesn't exist, or the key doesn't exist, FALSE is returned, TRUE in case of success.
     */
    public function hDelete($key, $hashKey);

    /** Verify if the specified member exists in a key.
     * @param string $key
     * @param string $hashKey
     * @return bool If the member exists in the hash table, return TRUE, otherwise return FALSE.
     */
    public function hExists($key, $hashKey);

    /** Increments the value of a member from a hash by a given amount.
     * @param string $key
     * @param string $hashKey
     * @param int $value
     * @return int The new value
     */
    public function hIncrBy($key, $hashKey, $value);

}