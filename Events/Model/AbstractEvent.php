<?php

/** $Id$
 * AbstractEvent.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Events\Model;

use \Core\Events\EventDispatcherInterface as EventDispatcher;

abstract class AbstractEvent implements EventModelInterface {

    protected $data = array();

    private $channel = null;

    private $dispatched = false;

    public function dispatch(EventDispatcher &$dispatcher){

        $event = json_encode($this->data);

        $dispatcher->publish($this->channel, $event);

        $this->dispatched = true;
    }

    public function isDispatched(){
        return $this->dispatched;
    }

    protected function setChannel($channel){
        $this->channel = $channel;
    }

}