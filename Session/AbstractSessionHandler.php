<?php

/** $Id$
 * AbstractSessionHandler.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Session {

    use \SplSubject;
    use \Exception;
    use \SessionHandler;
    use \SessionHandlerInterface;
    
    abstract class AbstractSessionHandler extends SessionHandler implements SessionHandlerInterface, SplSubject {

        const STATE_SESSION_WRITE = 'state_session_write';
        const STATE_SESSION_READ  = 'state_session_read';
        const STATE_SESSION_CLOSE = 'state_session_close';

        /** Observers
         * @var array
         */
        private $observers = array();

        private $state = null;

        public function __construct(){

            $result = session_set_save_handler(
                array(&$this, "open"),
                array(&$this, "close"),
                array(&$this, "read"),
                array(&$this, "write"),
                array(&$this, "destroy"),
                array(&$this, "cleanup")
            );

            /* This function is registered itself as a shutdown function by
             * session_set_save_handler($obj). The reason we now register another
             * shutdown function is in case the user registered their own shutdown
             * function after calling session_set_save_handler(), which expects
             * the session still to be available.
             */
            session_register_shutdown();

            if($result == false){
                throw new Exception("Fail to sets user-level session storage functions.");
            }

        }

        public function __destruct() {
        }
        
        protected function sessionCommit(){
            session_write_close();
        }
        
        public function __set($name, $value) {
            $_SESSION[$name] = $value;
        }
        
        public function __get($name) {
            return $_SESSION[$name];
        }

        public function getState() {
            return $this->state;
        }

        public function setState($state) {
            $this->state = $state;
            $this->notify();
        }

        /** Attachy new observer
         * @param \SplObserver $observer
         */
        public function attach(\SplObserver $observer){
            $this->observers[] = $observer;
        }

        /** Detach observer
         * @param \SplObserver $observer
         */
        public function detach(\SplObserver $observer){
            $index = array_search($observer, $this->observers);

            if (false !== $index) {
                unset($this->observers[$index]);
            }
        }

        public function notify(){
            /** @var SplObserver $observer */
            foreach ($this->observers as $observer) {
                $observer->update($this);
            }
        }
        
    }
}