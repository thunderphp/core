<?php

/**
 * Tree.php
 * @version 1.0.0
 * @package lightbay.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2014, Marek Ulwański
 */

namespace Core\Math {

    use \Iterator;
    use \ArrayAccess;

    class Tree implements Iterator, ArrayAccess {

        private $last_key   = 0;
        private $position   = 0;
        private $container  = array();
        private $attributes = array();

        public function __construct() {
            $this->position = 0;
            $this->last_key = 0;
        }

        function rewind() {
            $this->position = 0;
        }

        function current() {
            return $this->container[$this->position];
        }

        function key() {
            return $this->position;
        }

        function next() {
            ++$this->position;
        }

        function valid() {
            return isset($this->container[$this->position]);
        }

        public function offsetSet($offset, $value) {
            if (is_null($offset)) {
                $this->attributes[] = $value;
            } else {
                $this->attributes[$offset] = $value;
            }
        }

        public function offsetExists($offset) {
            return isset($this->attributes[$offset]);
        }

        public function offsetUnset($offset) {
            unset($this->attributes[$offset]);
        }

        public function offsetGet($offset) {
            return isset($this->attributes[$offset]) ? $this->attributes[$offset] : null;
        }
        
        public function addChild(){
            $key = $this->last_key;
            $this->last_key = $this->last_key + 1;
            $this->container[$key] = new tree();
            return $this->container[$key];
        }
        
        public function getAttributes(){
            return (array)$this->attributes;
        }
        
        private function to_array( $var ){
            $arr = array();
            if(is_array($var) && count($var)){
                foreach( $var as $id => $val ) $arr[$id] = $this->to_array ($val);
            } else if($var instanceof tree){
                foreach( $var->getAttributes() as $id => $val ) $arr[$id] = $this->to_array ($val);
            } else {
                return $var;
            }
            return (array)$arr;
        }
        
        /* Zwraca dane w postaci tablicy wielowymiarowej.
         * @return array
         */
        public function getArray(){
            return $this->to_array($this->container);
        }

    }

}