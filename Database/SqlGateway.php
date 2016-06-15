<?php

/** $Id$
 * TableGateway.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Database {
    
    class SqlGateway extends Adapter\PdoAdapter {
        
        private static $Instance = false;
        
        /* @return \Core\Database\SqlGateway */
        public static function getInstance() {
            if (self::$Instance == false)
                self::$Instance = new \Core\Database\SqlGateway();
            return self::$Instance;
        }
        
        public function __construct() {
            parent::__construct();
        }
        
        public function getRowByWhere($table, $select, $where){
            return $this->get_row($table, $select, $where);
        }
        
        public function getRows($table, $select, $limit = false){
            return $this->get_rows($table, $select, '1', $limit);
        }
        
        public function getRowsByWhere($table, $select, $where, $limit = false){
            return $this->get_rows($table, $select, $where, $limit);
        }
        
        public function getRowByField($table, $select, $pk, $value){
            return $this->get_row($table, $select, "`$pk` = '$value'");
        }
        
        public function getRowsByField($table, $select, $pk, $value, $limit = false){
            return $this->get_all($table, $select, "`$pk` = '$value'", $limit);
        }
        
        public function insertRow($table, &$data){
            return $this->insert($table, $data);
        }
        
        public function updateRow($table, &$data, $where){
            return $this->update($table, $data, $where);
        }
        
    }
    
}