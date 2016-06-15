<?php

/** $Id$
 * AbstractActionController.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Controller {
    
    use \Api;
    use \Core\Controller\AbstractConsoleController;
    
    abstract class AbstractActionController extends AbstractConsoleController implements BasicActionController {
    
        protected $allowLogin  = false;
        protected $sslRedirect = false;

        public function __construct() {
            parent::__construct();
        }
        
        /* Zwraca obiekt HttpRequest
         * @return @return \Core\Router\HttpRequest 
         */
        protected function getRequest(){
            return Api::getRouter()->getHttpRequest();
        }
        
        protected function sslRedirect(){
            
            $config = Api::getConfig()->getCoreConfig('server');
            
            if($config['ssl_on'] == true){
                $router = Api::getRouter();
                $request = $router->getHttpRequest();
                $is_ssl = $request->isSSLRequest();
                if(!$is_ssl){
                    $router->sslRedirect();
                }
            }
            
            return $config['ssl_on'];
        }
    
    }
    
    interface BasicActionController {
        
        public function defaultAction();
        
    }
    
}