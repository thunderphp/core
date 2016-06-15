<?php

/** $Id$
 * SimpleMailer.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Mail {
    
    class SimpleMailer extends AbstractMail {
        
        public function __construct() {
            parent::__construct();
        }
        
        public function addReceiver($email, $name = null){
            $this->add_to($email, $name);
        }
        
        public function setSubject($subject){
            $this->subject = $subject;
        }
        
        public function setContent($content){
            $this->content = trim($content);
        }
        
        public function send($from, $name = null) {
            
            $this->set_from($from, $name);
            
            $headers = array();
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=utf-8';
            
            $to = array();
            foreach($this->to as $email => $name){
                $to[] = $name.' <'.$email.'>';
            }
            
            $cc = array();
            foreach($this->cc as $email => $name){
                $cc[] = $name.' <'.$email.'>';
            }
            if(count($cc)) $headers[] = 'Cc: '.  implode(', ', $cc);

            $bcc = array();
            foreach($this->bcc as $email => $name){
                $bcc[] = $name.' <'.$email.'>';
            }
            if(count($bcc)) $headers[] = 'Bcc: '.  implode(', ', $bcc);
            
            if($this->from) $headers[] = 'From: '.$this->from;
            $result = mail( implode(', ', $to), trim($this->subject), trim($this->content), implode("\r\n", $headers) );
            
            return $result;
        }

    }
    
}