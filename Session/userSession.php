<?php

/** $Id$
 * userSession.php
 *
 * @version 1.0.0, $Revision$
 * @package Core\Session
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Session;


class userSession {

    # Instance of created class
    private static $instance = null;

    # Instance of sessionMessages class
    private static $messages = null;

    /** Returns class instances.
     * @return \Core\Session\userSession
     */
    final public static function getInstance() {
        if (self::$instance == null)
            self::$instance = new userSession();
        return self::$instance;
    }

    /** Return sessionMessages class
     * @return \Core\Session\sessionMessages
     */
    final public function getMessages(){
        if (self::$messages == null)
            self::$messages = new sessionMessages();
        return self::$messages;
    }
}