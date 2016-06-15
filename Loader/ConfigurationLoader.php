<?php

/** $Id$
 * ConfigurationLoader.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Loader {
    
    use \Api;
    use \Core\Model\Core\ConfigModel;
    
    class ConfigurationLoader {

        # Set cache TTL on 15 minutes
        const CONFIG_CACHE_TTL  = 900;

        const CORE_CACHE_KEY    = 'framework_core_config';
        const MODULE_CACHE_KEY  = 'framework_module_config';
        
        private static $Instance = false;
        private $core    = array();
        private $modules = array();

        /** Tworzy nową instancje klasy, lub zwraca już istniejącą
         *  @return \Core\Loader\ConfigurationLoader
         */
        public static function getInstance() {
            if (self::$Instance == false) self::$Instance = new ConfigurationLoader();
            return self::$Instance;
        }
        
        private function __construct() {}

        public function loadCoreConfig($path) {
            
            $config = Api::getCache()->entry(self::CORE_CACHE_KEY, function() use ($path){

                # Configuration data buffer
                $buffer = array();

                $file = $path.DIRECTORY_SEPARATOR.'config.php';
                if(file_exists($file)){
                    $data = require_once $file;
                    if(is_array($data)){
                        $buffer = $data;
                    }
                }

                $file = $path.DIRECTORY_SEPARATOR.'config.local.php';
                if(file_exists($file)){
                    $data = require_once $file;
                    if(is_array($data)){
                        $buffer = array_merge($buffer, $data);
                    }
                }

                # Return core configuration
                return $buffer;
            }, self::CONFIG_CACHE_TTL);

            $this->core = new ConfigModel($config);
        }
        
        public function loadModulesConfig($path, $modules) {

            $config = Api::getCache()->entry(self::MODULE_CACHE_KEY, function() use ($path, $modules){

                # Configuration data buffer
                $buffer = array();

                # Load config file for each module
                foreach ($modules as $module){
                    $file = $path.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'config.php';
                    if(file_exists($file)){

                        # Load file to variable
                        $data = include_once $file;

                        # Simple file validation
                        if(!is_array($data)) continue;

                        # Save to the buffer
                        $buffer[$module] = $data;
                    }
                }

                # Return modules configuration
                return $buffer;
            }, self::CONFIG_CACHE_TTL);

            $this->modules = new ConfigModel($config);
        }
        
        public function getCoreConfig($key = null) {

            if($key == null){
                return $this->core;
            } else if(isset($this->core[$key])) {
                return $this->core[$key];
            } else {
                return null;
            }
        }
        
        public function getModuleConfig($module) {
            if(isset($this->modules[$module])){
                return $this->modules[$module];
            } else {
                return array();
            }
        }
        
        public function getRoutingTable() {
            
            $table = array();
            
            foreach($this->modules as $module => $data){
                if(!isset($data['router']['routes'])) continue;

                /** @var ConfigModel $route */
                foreach($data['router']['routes'] as $path => $route){

                    $table[trim($path)] = array_merge($route->getArrayCopy(), array(
                        'module'  => $module,
                    ));
                }
            }
            
            return $table;
        }
        
        public function getDefaultRoute() {
            if(isset($this->core['router']['default'])){
                return $this->core['router']['default'];
            } else {
                return false;
            }
        }
        
    }
    
}