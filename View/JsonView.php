<?php

/** $Id$
 * JsonView.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\View {

    use SplSubject;

    class JsonView extends AbstractView {
        
        const STATUS_OK    = 'ok';
        const STATUS_WARN  = 'warn';
        const STATUS_DATA  = 'data';
        const STATUS_ERROR = 'error';
        
        private $code    = 0;
        private $data    = null;
        private $error   = null;
        private $status  = self::STATUS_OK;
        private $message = null;
        
        public function __construct($data = null, $error = null) {
            parent::__construct();
            if($data != null) {
                $this->status = self::STATUS_DATA;
                $this->setData($data);
            }
            if($error != null) {
                $this->status = self::STATUS_ERROR;
                $this->setError($error);
            }
        }
        
        public function __toString() {
            return (string)json_encode($this->__toArray());
        }
        
        public function __toArray() {
            $time = time();
            return array(
                'status'    => $this->status,
                'code'      => (int)$this->code,
                'data'      => $this->data,
                'error'     => $this->error,
                'message'   => $this->message,
                'time'      => $time,
                'md5'       => md5($time.$this->status.$this->code),
            );
        }

        public function cleanView() {
            $this->code    = 0;
            $this->data    = null;
            $this->error   = null;
            $this->status  = self::STATUS_OK;
            $this->message = null;
        }

        public function parseView() {
            return (string)$this->__toString();
        }

        public function prepareView() {
        }

        public function serialize() {
            return (string)$this->__toString();
        }

        public function unserialize($serialized) {
            $data = json_decode($serialized);
            $this->code     = $data['code'];
            $this->data     = $data['data'];
            $this->error    = $data['error'];
            $this->message  = $data['message'];
            $this->status   = $data['status'];
        }
        
        public function setupSuccessResponse($code = null, $message = null) {
            $this->status = self::STATUS_OK;
            if($code)    $this->setCode ($code);
            if($message) $this->setMessage($message);
            $this->data = null;
            $this->error = null;
        }
        
        public function setupWarningResponse( $code = null, $message = null ) {
            $this->status = self::STATUS_WARN;
            if($code)    $this->setCode ($code);
            if($message) $this->setMessage($message);
            $this->data = null;
        }
        
        public function setupErrorResponce( $error, $code = null, $message = null, $data = null ){
            $this->setError($error, $code, $message);
            if($data)    $this->data = $data;
        }
        
        public function setupDataResponce($data, $code = null, $message = null){
            $this->setData($data);
            if($code)    $this->setCode ($code);
            if($message) $this->setMessage($message);
        }
        
        public function setStatus($status) {
            $this->status = $status;
        }
        
        public function setCode($code) {
            $this->code = (int)$code;
        }
        
        public function setData($data, $code = null) {
            $this->status  = self::STATUS_DATA;
            $this->data    = $data;
            if($code)    $this->setCode ($code);
        }
        
        public function setMessage($msg) {
            $this->message = (string)trim($msg);
        }
        
        public function setError($error, $code = 0, $msg = null) {
            $this->code    = (int)$code;
            $this->error   = (string)trim($error);
            $this->status  = self::STATUS_ERROR;
            $this->setMessage($msg);
        }
        
        public function isError() {
            return (bool)($this->error !== null || $this->status == self::STATUS_ERROR);
        }

        /**
         * Receive update from subject
         *
         * @link http://php.net/manual/en/splobserver.update.php
         * @param SplSubject $subject <p>
         * The <b>SplSubject</b> notifying the observer of an update.
         * </p>
         * @return void
         * @since 5.1.0
         */
        public function update(SplSubject $subject) {
            # TODO: Implement update() method.
        }
    }
    
}