<?php

/** $Id$
 * SessionGateway.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Session {
    
    class SessionGateway implements SessionGatewayInterface {
        
        const ADAPTER_REDIS = '\Core\Session\Adapter\Redis';
        
        private $adapter = null;

        public function __construct($adapter) {
            switch($adapter){
                
                case self::ADAPTER_REDIS:

                    $name = self::ADAPTER_REDIS;
                    $redis = \Api::getRedis();
                    $this->adapter = new $name($redis);
                    break;
            
            }
        }

        public function __set($name, $value){
            $_SESSION[$name] = $value;
        }
        
        public function __get($name){
            return $_SESSION[$name];
        }

        public function getAdapter(){
            return $this->adapter;
        }
        
        public function set($name, $value){
            $this->$name = $value;
        }
        
        public function get($name){
            return $this->$name;
        }
        
    }
    
}