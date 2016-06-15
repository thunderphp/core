<?php

/** $Id$
 * api.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */


use Core\Model\Core\ConfigModel;

class Api {

    /** @var \Core\Cache\Shared\Redis  */
    private static $redis = null;

    /** @var Core\Cache\Volatile\Apcu  */
    private static $apcu = null;

    private static $extensions = array();

    public static function init() {

        # Load list available PHP extensions
        self::$extensions = get_loaded_extensions();

    }

    /** Return list of loaded PHP extensions
     * @return array
     */
    public static function getExtensions() {
        return self::$extensions;
    }

    /**
     * @return \Core\Cache\Volatile\Apcu
     */
    public static function getCache(){
        if(self::$apcu === null) self::$apcu = \Core\Cache\Volatile\Apcu::getInstance();
        return \Core\Cache\Volatile\Apcu::getInstance();
    }

    /** Pobiera aktywne połączenie z serwerem Redis
     *
     * @param int $bank
     * @return \Core\Cache\Shared\Redis Instancja klasy Redis
     */
    public static function getRedis($bank = null){
        if(self::$redis === null){

            /** @var ConfigModel $config */
            $config  = self::getConfig()->getCoreConfig('redis');

            $host       = $config->getValue('host', 'localhost');
            $port       = $config->getValue('port', 6379);
            $auth       = $config->getValue('auth');
            $session_id = 'core_'.md5($host.$port.$auth);

            self::$redis = new \Core\Cache\Shared\Redis($host, $port, $auth, $session_id, $config->timeout, $config->retry);
        }

        # Select Redis bank
        if($bank !== null) self::$redis->select($bank);

        return self::$redis;
    }
    
    /** @return \Core\Router\StandardRouter */
    public static function getRouter() {
        return \Core\Router\StandardRouter::getInstance();
    }
    
    /** @return \Core\Loader\ConfigurationLoader */
    public static function getConfig() {
        return \Core\Loader\ConfigurationLoader::getInstance();
    }
    
    /** @return \Core\Users\User */
    public static function getUser() {
        return \Core\Users\User::getInstance();
    }

    /** @return \Core\Session\userSession */
    public static function getSession() {
        return \Core\Session\userSession::getInstance();
    }


}
