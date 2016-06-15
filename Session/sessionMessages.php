<?php

/** $Id$
 * sessionMessages.php
 *
 * @version 1.0.0, $Revision$
 * @package Core\Session
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Session;


class sessionMessages {

    const MSG_NAMESPACE = 'session_messages';

    public function addSuccess($message){
        $_SESSION[self::MSG_NAMESPACE]['success'][] = array(
            'message'   => trim($message),
        );
    }

    public function addNotice($message){
        $_SESSION[self::MSG_NAMESPACE]['notice'][] = array(
            'message'   => trim($message),
        );
    }

    public function addWarning($message){
        $_SESSION[self::MSG_NAMESPACE]['warning'][] = array(
            'message'   => trim($message),
        );
    }

    public function addError($message){
        $_SESSION[self::MSG_NAMESPACE]['error'][] = array(
            'message'   => trim($message),
        );
    }

    public function removeAll(){
        unset($_SESSION[self::MSG_NAMESPACE]);
        $_SESSION[self::MSG_NAMESPACE] = array();
    }

    public function getAll(){
        if(isset($_SESSION[self::MSG_NAMESPACE])){
            return $_SESSION[self::MSG_NAMESPACE];
        } else {
            return array();
        }
    }

}