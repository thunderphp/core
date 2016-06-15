<?php

/** $Id$
 * AbstractUser.php
 * @version 1.0.0, $Revision$
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Users {
    
    use \Serializable;
    
    abstract class AbstractUser implements Serializable {

        protected $attributes = array();

        public function __construct() {
            if(session_id() == "") session_start();
            foreach($_SESSION as $name => $value){
                $this->attributes[$name] = $value;
            }
        }

        public function __toString(){
                return json_encode($this->attributes);
        }
        
        public function serialize() {
            return (string)$this->__toString();
        }
        
        public function unserialize($data) {
            $this->attributes = json_decode($data);
        }

        public function __get($name) {
            if (isset($this->attributes[$name])){
                return $this->attributes[$name];
            } else {
                return false;
            }
        }

        public function __set($name, $value = NULL){
            if (is_array($value)) {
                foreach ($value as $key => $val){
                    $this->__set($key, $val);
                }
            } else {
                if(!isset($this->attributes[$name]) || $this->attributes[$name] != $value){
                    $_SESSION[$name]         = $value;
                    $this->attributes[$name] = $value;
                }
            }
        }
        
    }
    
}