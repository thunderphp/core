<?php

/** $Id$
 * ExceptionHandler.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Loader {
    
    class ExceptionHandler {
        
        public static function catch_exception($e) {
            echo 'Wyjątek! '.$e->getMessage();
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
        }
        
        public static function catch_error($errno , $errstr, $errfile, $errline, $errcontext) {
            $msg = ''.$errstr.'<span style="color: #666;"> w <i>'.$errfile.'</i> linia '.$errline.'</span>';
            //echo '<p style="font: 13px Arial;">'.$msg.'</p>';
            return false;
        }
        
    }
    
}