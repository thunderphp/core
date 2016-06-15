<?php

/** $Id$
 * ConsoleRequest.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Router {

    class ConsoleRequest {

        private $argv = array();
        private $argc = 0;
        private $base = null;
        private $path = array();
        
        public function __construct() {
            $this->argc = $_SERVER['argc'];
            $this->argv = $_SERVER['argv'];
            $this->base = $this->argv[0];
            if($this->argc > 1){
                $this->path = explode('/', trim($this->argv[1], '/'));
            }            
        }

        public function getPathArray() {
            return $this->path;
        }
        
        public function getFirstParam() {
            if(isset($this->path[0])){
                return $this->path[0];
            } else {
                return false;
            }
        }
        
        public function getParam($name = null, $default = false) {
            if ($name === null){
                return $this->path;
            }
            if (is_numeric($name)) {
                if (isset($this->path[$name]))
                    return $this->path[$name];
            }

            return $default;
        }
        
    }
}