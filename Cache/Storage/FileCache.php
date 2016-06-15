<?php

/** $Id$
 * File.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Cache\Storage {

    class FileCache {

        protected $_path = null;
        protected $_dir  = null;
        
        public function __construct($name, $ext = '') {
            $base = realpath(__DIR__.'/../../../data/cache');
            $hash = md5($name);
            $name = sha1($name);
            $dir  = implode(DIRECTORY_SEPARATOR, array_slice(str_split($hash, 2), 0, 4));
            $this->_dir  = $base.DIRECTORY_SEPARATOR.$dir;
            $this->_path = $base.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$name.$ext;
        }
        
        public function __toString() {
            return $this->get("");
        }

        public function get($default = null) {
            if(file_exists($this->path)){
                return file_get_contents($this->path);
            }
            return $default;
        }
        
        public function set($data) {
            if(!is_dir($this->_dir)){
                mkdir($this->_dir, 0755, true);
            }
            return file_put_contents($this->_path, $data, LOCK_EX);
        }
        
        public function append($data) {
            if(!is_dir($this->_dir)){
                mkdir($this->_dir, 0755, true);
            }
            return file_put_contents($this->_path, $data, FILE_APPEND | LOCK_EX);
        }
        
        public function delete() {
            if(file_exists($this->_path)){
                return unlink($this->_path);
            }
            return false;
        }
        
        public function size() {
            if(file_exists($this->_path)){
                return filesize($this->_path);
            }
            return false;
        }

    }

}