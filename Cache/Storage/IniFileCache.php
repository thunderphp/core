<?php

/** $Id$
 * IniFileCache.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Cache\Storage {

    class IniFileCache extends FileCache implements \ArrayAccess {

        private $data = array();
        private $volatile = true;

        public function __construct($name, $volatile = true) {
            die('Ta klasa musi zostać dokończona, aby wspierać tablice wielowymiarowe.');
            parent::__construct($name, '.ini');
            if (file_exists($this->_path)) {
                $this->data = parse_ini_file($this->_path, true);
            }
            $this->volatile = (bool) $volatile;
            var_dump($this->data);
        }

        public function __destruct() {
            if (is_array($this->data) && $this->volatile == false) {
                $this->set(trim($this->arr2ini($this->data)));
            }
        }

        public function offsetSet($offset, $value) {
            if (is_null($offset)) {
                $this->data[] = $value;
            } else {
                $this->data[$offset] = $value;
            }
        }

        public function offsetExists($offset) {
            return isset($this->data[$offset]);
        }

        public function offsetUnset($offset) {
            unset($this->data[$offset]);
        }

        public function offsetGet($offset) {
            return isset($this->data[$offset]) ? $this->data[$offset] : null;
        }

        private function arr2ini(array $a, array $parent = array()) {
            $out = '';
            foreach ($a as $k => $v) {
                if (is_array($v)) {
                    $sec = array_merge((array) $parent, (array) $k);
                    $out .= PHP_EOL . '[' . join('.', $sec) . ']' . PHP_EOL;
                    $out .= $this->arr2ini($v, $sec);
                } else {
                    $out .= "$k=$v" . PHP_EOL;
                }
            }
            return $out;
        }

    }

}