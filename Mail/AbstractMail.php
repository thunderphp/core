<?php

/** $Id$
 * AbstractMail.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Mail {
    
    abstract class AbstractMail {
        
        protected $to       = array();
        protected $cc       = array();
        protected $bcc      = array();
        protected $date     = null;
        protected $from     = null;
        protected $reply    = null;
        protected $subject  = null;
        protected $content  = null;
        
        public function __construct() {
            
        }
        
        public function __toString() {
            return $this->content;
        }
        
        protected function set_from($from, $name = null){
            if($name == null) $name = $from;
            $this->from = $name.' <'.$from.'>';
        }
        
        protected function add_to($to, $name = null){
            if($name == null) $name = $to;
            $this->to[trim($to)] = trim($name);
        }
        
        protected function add_cc($cc, $name){
            if($name == null) $name = $cc;
            $this->cc[trim($cc)] = trim($name);
        }
        
        protected function add_bcc($bcc, $name){
            if($name == null) $name = $bcc;
            $this->bcc[trim($bcc)] = trim($name);
        }
        
        protected function title($title){
            $this->title = trim($title);
        }
        
        protected function content($content){
            $this->content = $content;
        }
        
        abstract public function send($from, $name = null);
        
    }
    
}