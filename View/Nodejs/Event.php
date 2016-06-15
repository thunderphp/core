<?php

/** $Id$
 * Event.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\View\Nodejs {
    
    class Event {
        
        private $name = null;
        private $room = null;
        private $user = null;
        private $data = null;
        
        public function __construct($name = null) {
            $this->name = $name;
        }
        
        public function getString() {
            return json_encode(array(
                'name' => $this->name,
                'room' => $this->room,
                'user' => $this->user,
                'data' => $this->data,
                'time' => time(),
            ));
        }
        
        public function __toString() {
            return $this->getString();
        }
        
        public function getName() {
            return $this->name;
        }

        public function getRoom() {
            return $this->room;
        }

        public function getUser() {
            return $this->user;
        }

        public function getData() {
            return $this->data;
        }

        public function setName($name) {
            $this->name = $name;
            return $this;
        }

        public function setRoom($room) {
            $this->room = $room;
            return $this;
        }

        public function setUser($user) {
            $this->user = $user;
            return $this;
        }

        public function setData($data) {
            $this->data = $data;
            return $this;
        }

    }
    
}