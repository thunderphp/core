<?php

/** $Id$
 * RouterException.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Router {
    
    class RouterException extends \Exception {
        
        const ERROR_MISSING_CONTROLLER  = 0xA001;
        const ERROR_MISSING_INTERFACE   = 0xA002;
        
    }
    
}