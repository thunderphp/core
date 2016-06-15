<?php

/** $Id$
 * AbstractConsoleController.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Controller {
    
    use \Core\Controller\AbstractController;
    
    abstract class AbstractConsoleController extends AbstractController implements BasicConsoleController {
        
        public function defaultRun() {
            echo 'This action is not designed to run on the console.';
        }
        
    }
    
    interface BasicConsoleController {
        
        public function defaultRun();
        
    }
    
}