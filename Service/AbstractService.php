<?php

/** $Id$
 * AbstractService.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Service {
    
    use ReflectionProperty as Prop;
    
    abstract class AbstractService {
        
        /** Metoda zwraca wszystkie publiczne atrybuty klasy w postaci tablicy.
         * Szuka również metod zapisujących i odczytujących dane w przeszukiwanej klasie.
         * @access protected
         * @return Array 
         */
        protected function getClassProperties($class, $filter = Prop::IS_PUBLIC) {
            $reflect = new \ReflectionClass($class);
            $props = $reflect->getProperties($filter);
            $data = array();
            foreach($props as $prop){
                
                $name = str_replace(' ', '', ucwords(str_replace('_', ' ', $prop->getName())));

                if(method_exists($class, 'get'.$name)){
                    $get_method = 'get'.$name;
                } else {
                    $get_method = false;
                }

                if(method_exists($class, 'set'.$name)){
                    $set_method = 'set'.$name;
                } else {
                    $set_method = false;
                }
                
                $data[$prop->getName()] = array(
                    'getMethod'  => $get_method,
                    'setMethod'  => $set_method,
                    'reflection' => $prop,
                );
            }
            return $data;
        }
        
        /** Metoda zwraca wszystkie publiczne atrybuty klasy wraz z danymi w postaci tablicy asocjacyjnej.
         * @access protected
         * @return Array
         */
        protected function getObjectData($class) {
            $struct = $this->getClassProperties($class);
            $data = array();
            foreach($struct as $field) {
                $key = $field->name;
                $data[$key] = $this->$key;
            }
            return $data;
        }
        
        protected function detectDataType($var){
            if(is_numeric($var)){
                if(is_float($var)){
                    return (float)$var;
                } else {
                    return (int)$var;
                }
            } else {
                return (string)$var;
            }
        }
        
    }
    
}