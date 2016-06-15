<?php

/** $Id$
 * JsonEncryption.php
 * @version 1.0.0, $Revision$
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Encryption {
    
    class JsonEncryption extends AbstractEncryption {

        private $data = null;
        private $crc  = null;
        
        public function __construct($data, $key = null) {
            if($key === null){
                $this->data = $data;
            } else {
                $this->crc  = self::crc($data, $key);
                $this->data = $this->encode($data, $key);
            }
        }
        
        public function __toString() {
            return $this->toString();
        }
        
        public function toString(){
            return json_encode(array(
                'crc'  => $this->crc,
                'data' => $this->data,
            ));
        }
        
        public function decrypt($key){
            $data = $this->decode($this->data, $key);
            $crc  = self::crc($data, $key);
            if($this->crc !== $crc){
                return false;
            }
            
            return $data;
        }
    
    }
    
}