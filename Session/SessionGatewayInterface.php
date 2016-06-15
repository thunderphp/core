<?php

/** $Id$
 * SessionGatewayInterface.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Session {

    interface SessionGatewayInterface {
        function set($name, $value);
        function get($name);
        function getAdapter();
    }

}