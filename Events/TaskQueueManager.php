<?php

/** $Id$
 * TaskQueueManager.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Events {
    
    class TaskQueueManager {
        
        const QUEUE_USERTASK_ID = 'user_task_queue_manager';
        const QUEUE_CRONTASK_ID = 'cron_task_queue_manager';
        
        private $task_id = null;
        
        public function __construct( $task_id, &$task_list, $callback = null, $limit = 5 ) {
            $this->task_id = $task_id;
            if(is_array($task_list)){
                return $this->processQueue($task_list, $callback, $limit);
            }
            return false;
        }
        
        private function processQueue($list, &$callback = null, $limit = false) {
            $count = 0;
            $result = array();
            foreach ($list as $key => $task){
                if($callback !== null) $code = $callback($key, $task);
                if($code == 0) continue;
                $class = "\\Scripts\\Task\\".$task;
                $t = new $class();
                $result[$key] = $t->run();
                if(++$count >= $limit && $limit != false) break;
            }
            return $result;
        }
        
    }
    
}