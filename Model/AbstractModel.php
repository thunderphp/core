<?php

/** $Id$
 * AbstractModel.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Model {

    use \Serializable;

    abstract class AbstractModel implements Serializable {

        private function underscoreToCamelCase($string) {
            $string[0] = strtoupper($string[0]);
            $func = create_function('$c', 'return strtoupper($c[1]);');
            return preg_replace_callback('/_([a-z])/', $func, $string);
        }

        private function getReflection() {
            static $reflection = null;
            if ($reflection === null) {
                $reflection = new \ReflectionClass($this);
            }
            return $reflection;
        }

        private function getProperties() {
            $reflection = $this->getReflection();
            $properties = $reflection->getProperties();
            $result = array();
            foreach ($properties as $property) {
                $key = $property->getName();
                $cam = $this->underscoreToCamelCase($key);

                $set = (method_exists($this, 'set' . $cam)) ? 'set' . $cam : false;
                $get = (method_exists($this, 'get' . $cam)) ? 'get' . $cam : false;
                $result[$key] = array(
                    'property' => $property,
                    'set' => $set,
                    'get' => $get,
                );
            }
            return $result;
        }
        
        public function __toArray(){
            $properties = $this->getProperties();
            $data = array();
            foreach($properties as $name => $property){
                if($property['get'] != false){
                    $data[$name] = $this->$property['get']();
                } elseif($property['property']->isPublic() == true){
                    $data[$name] = $property['property']->getValue($this);
                }
            }
            return $data;
        }
        
        public function __fromArray($data){
            $properties = $this->getProperties();
            foreach($properties as $name => $property){
                if($property['set'] != false){
                    $set = $property['set'];
                    if(isset($data[$name])) $this->$set($data[$name]);
                } elseif($property['property']->isPublic() == true){
                    $this->$name = $data[$name];
                }
            }
        }
        
        public function __fromString($data){
            $this->__fromArray((array)json_decode((string)$data));
        }
        
        public function __toString() {
            return (string)json_encode((array)$this->__toArray());
        }

        public function serialize() {
            return (string) $this->__toString();
        }

        public function unserialize($data) {
            $this->__fromString($data);
        }

    }

}