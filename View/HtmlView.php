<?php

/** $Id$
 * HtmlView.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\View {

    use \Api;
    use \SplSubject;
    use \Core\Session\AbstractSessionHandler;

    class HtmlView extends AbstractView {
        
        const OPT_RETURN_TPL             = 0x01;
        const OPT_DISABLE_COMPRESSION    = 0x02;
        const OPT_STRONG_COMPRESSION     = 0x04;
        const OPT_REPLEACE_NEWLINE_TO_BR = 0x08;
        const OPT_NO_SESSION_MSG_PARSE   = 0x10;
        
        private $tags = array();
        private $code = '';
        private $preg_callback = null;

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
        public function update(SplSubject $subject){

            if($subject instanceof AbstractSessionHandler){

                if($subject->getState() == AbstractSessionHandler::STATE_SESSION_WRITE){
                    /** @var \Core\Session\userSession $session */
                    $session = Api::getSession();

                    foreach($session->getMessages()->getAll() as $level => $list){
                        foreach($list as $msg){
                            $html = '<div class="message '.$level.'"><p>'.$msg['message'].'</p></div>';
                            $this->_session_messages = $html;
                        }
                    }

                    $session->getMessages()->removeAll();
                }
            }

        }

        public function prepareView() {
        }
        
        public function parseView() {
            if(connection_aborted() || empty($this->code)) return;
            $this->parse();
            return $this->code;
        }
        
        public function cleanView(){
            
        }
        
        public function __construct($template = 'default'){
            parent::__construct($template);
            
            header('Content-Type: text/html; charset=utf-8');
            $router       = \Api::getRouter();
            $root_path    = $router->getRemotePath();
            $request_path = $router->getHttpRequest()->getRequestPath(); 

            $this->preg_callback = function($matches) use($root_path, $request_path){
                
                $part = explode('|', $matches[1]);
                $name = $part[0];
                $tag  = '{$'.$name.'}';
                $mod  = isset($part[1])?$part[1]:false;
                $val  = false;
                
                if(isset($this->tags[$tag])){
                    $val = $this->tags[$tag];
                }
                
                if($val == false){
                    switch($name){
                        
                        case '_root_path':
                            $val = $root_path;
                            break;
                        
                        case '_request_path':
                            $val = $request_path;
                            break;
                        
                    }
                }
                
                if($val && $mod){                    
                    switch($mod){
                        
                        case 'md5':
                            $val = md5($val);
                            break;
                        
                        case 'sha1':
                            $val = sha1($val);
                            break;
                        
                        case 'to_lower':
                        case 'lower':
                            $val = strtolower($val);
                            break;
                        
                        case 'to_upper':
                        case 'upper':
                            $val = strtoupper($val);
                            break;
                        
                        case 'link':
                            $val = '<a href="'.$val.'" rel="nofollow">'.$val.'</a>';
                            break;

                        case 'color':
                            $val = '<span style="color: '.$val.';">'.$val.'</span>';
                            break;

                        case 'ceil':
                            $val = ceil(floatval($val));
                            break;

                        case 'floor':
                            $val = floor(floatval($val));
                            break;

                        case 'round':
                            $val = round(floatval($val), 2);
                            break;
                    }
                }
                
                return $val;
            };
        }

        public function serialize() {
            return json_encode(array(
                'tags' => $this->tags,
                'code' => $this->code,
            ));
        }

        public function unserialize($serialized) {

            $data = json_decode($serialized);

            $this->tags = $data['tags'];
            $this->code = $data['code'];
        }
        
        protected function parse(){

            # Regular expression for {$example} or {$example|option} tags.
            $regex = "/{\\$(\\w+|\\w+\\|\\w+)}/";

            # Replace tags count
            $count = 0;

            # Replace tags until there is no tags to replace
            do {
                $this->code = preg_replace_callback($regex, $this->preg_callback, $this->code, -1, $count);
            } while($count > 0);

            # Return parsed code
            return trim($this->code);
        }

        public function __set($name, $value = NULL) {
            if (is_array($value)) {
                foreach ($value as $key => $val) $this->__set($name.$key, $val);
            } else {
                if (isset($this->tags['{$' . $name . '}'])){
                    $this->tags['{$' . $name . '}'] .= $value;
                } else {
                    $this->tags['{$' . $name . '}'] = $value;
                }
            }
        }

        public function __get($name) {
            if (isset($this->tags['{$' . $name . '}'])){
                return $this->tags['{$' . $name . '}'];
            }
            return false;
        }

        public function __call($name, $arg) {
            if (connection_aborted()) return false;
            if (!isset($arg[0]))      $arg[0] = false;
            if (!isset($arg[1]))      $arg[1] = false;
            
            $file = realpath($this->path.'/'.$name.'.tpl');
            if(!$file) return false;
            $content = file_get_contents($file);

            if ($arg[0] & self::OPT_REPLEACE_NEWLINE_TO_BR){
                $content = str_replace("\n", "<br>", $content);
            }
            
            if (!($arg[0] & self::OPT_DISABLE_COMPRESSION)) {                                                           // Dokonujemy kompresji kodu o ile nie została wyłączona
                $content = $this->code_compress($content);
            }
            
            if ($arg[0] & self::OPT_RETURN_TPL) {                                                                       // Jeżeli wybrano opcje zwrócenia kodu (zamiast dodawania do bufora)
                return $content;
            }
            
            $this->code .= $content;                                                                                    // Przenosimy kod do bufora i zwracamy true
            return true;
        }
        
        public function purgeView(){
            $this->tags = array();
            $this->code = '';
            return $this;
        }
        
        private function code_compress($content) {

            // (?:[^"'-]([\/]{2,}.*?(\n|\r)))

            $order = array("\n", "\r", "\t", "  ");
            $content = str_replace($order, '', $content);
            return trim($content);
        }

    }
    
}