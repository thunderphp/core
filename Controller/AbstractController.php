<?php

/** $Id$
 * AbstractController.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Controller {
    
    use \Api;
    use \Core\Events\TaskQueueManager;
    
    abstract class AbstractController implements BasicAbstractController {
        
        private $session = null;
        private $redis   = null;
        
        public function __construct(){
            $config = Api::getConfig()->getCoreConfig();
            $this->session = new \Core\Session\SessionGateway($config['session']['handler']);
            
            //$this->redis = Api::getRedis();
            //$this->redis->select(\Core\Cache\Shared\Redis::DB_EVENT_QUEUE);
            //$queue = $this->redis->set_get(TaskQueueManager::QUEUE_USERTASK_ID);
            //$manager = new TaskQueueManager(TaskQueueManager::QUEUE_USERTASK_ID, $queue, function($id, $key){
            //    return $this->redis->set_remove(TaskQueueManager::QUEUE_USERTASK_ID, $key);
            //}, 3);
        }
        
        public function getSession(){
            return $this->session;
        }
        
    }
    
    interface BasicAbstractController {
        function defaultAction();
        function getSession();
    }
    
}