<?php

/** $Id$
 * PdoAdapter.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Database\Adapter {
    
    use \PDO;
    
    class PdoAdapter extends AbstractDatabaseAdapter {
        
        const DB_MASTER = 'master';
        const DB_SLAVE  = 'slave';

        const MYSQL_DATETIME_FORMAT = "Y-m-d H:i:s";
        
        /* @var $driver PDO */
        private $driver = null;
        
        public function __construct() {
            $config = \Api::getConfig()->getCoreConfig();
            $db = $config['database'];
            
            $this->driver = new PDO($db['dsn'], $db['username'], $db['password'], array(
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            ));
        }
        
        public function getDriver() {
            $driver = $this->driver;
            /* @var $driver PDO */
            return $driver;
        }
        
        protected function get_field($from, $field, $where){
            $statement = "SELECT `$field` FROM `$from` WHERE $where LIMIT 1;";
            $query = $this->driver->prepare($statement);
            $query->execute();
            $data = $query->fetch(\PDO::FETCH_ASSOC);
            if(isset($data[$field])){
                return $data[$field];
            }
            return false;
        }
        
        protected function get_row($from, $select = "*", $where = '1'){
            $statement = "SELECT $select FROM `$from` WHERE $where LIMIT 1;";
            $query = $this->driver->prepare($statement);
            $query->execute();
            $data = $query->fetch(\PDO::FETCH_ASSOC);
            return $data;
        }
        
        protected function get_rows($from, $select = "*", $where = '1', $limit = '1'){
            if($limit != false) $limit = 'LIMIT '.(int)$limit;
            $statement = "SELECT $select FROM `$from` WHERE $where $limit;";
            $query = $this->driver->prepare($statement);
            $query->execute();
            $data = $query->fetchAll(\PDO::FETCH_ASSOC);
            return $data;
        }
        
        protected function get_all($from, $select = "*", $where = false){
            $statement = "SELECT $select FROM `$from`";
            if($where != false){
                $statement .= " WHERE $where";
            }
            $query = $this->driver->prepare($statement);
            $query->execute();
            $data = $query->fetchAll(\PDO::FETCH_ASSOC);
            return $data;
        }
        
        protected function insert($table, $data){
            $fields = $this->get_array_keys_to_query($data);
            $values = $this->get_array_values_to_query($data);
            
            $statement = "INSERT INTO `$table` ($fields) VALUES ($values);";
            $result  = $this->driver->exec($statement);
            $last_id = $this->driver->lastInsertId();
            return ($result == false)?null:$last_id;
        }
        
        protected function update($table, $data, $where){
            $values = $this->get_array_to_query($data);
            
            $statement = "UPDATE `$table` SET $values WHERE $where;";
            $result  = $this->driver->exec($statement);
            return $result;
        }
        
    }
    
}