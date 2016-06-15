<?php

/** $Id$
 * BasicView.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\View {
    
    use \Api;
    use \SplObserver;
    use \Serializable;
    use \Core\View\Interfaces\SimpleView;

    abstract class AbstractView implements SimpleView, Serializable, SplObserver {
        
        protected $path = null;

        public function __construct($template = 'default') {
            $router = Api::getRouter();
            $base = $router->getRootPath().'/modules/'.$router->getModuleName();
            $this->path = realpath($base.'/layout/'.$template);
        }
        
        public function __destruct() {
            $this->prepareView();
            echo $this->parseView();
            $this->cleanView();
            
            // Powoduje błąd "failed to send buffer of zlib output compression" na nginx przy włączonej kompresji
//            while (ob_get_level() > 0){
//                ob_end_flush();                                                                                         // Czyszczenie bufora danych (jeśli istnieje)
//            }
        }
        
    }
    
}