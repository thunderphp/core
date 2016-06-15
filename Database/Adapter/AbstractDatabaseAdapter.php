<?php

/** $Id$
 * AbstractDatabaseAdapter.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Database\Adapter {
    
    abstract class AbstractDatabaseAdapter {
        
        abstract protected function get_row($from, $select, $where);
        abstract protected function get_rows($from, $select, $where, $limit);
        abstract protected function get_field($from, $field, $where);
        abstract protected function get_all($from, $select, $where);
        
        abstract protected function insert($table, $data);
        abstract protected function update($table, $data, $where);
        
        protected function get_array_keys_to_query(&$array){
            $values = array();
            foreach($array as $key => $val){
                $values[] = '`'.$key.'`';
            }
            return implode(', ', $values);
        }
        
        protected function get_array_values_to_query(&$array){
            $values = array();
            foreach($array as $key => $val){
                if(is_null($val)){
                    $values[] = 'NULL';
                } else {
                    $values[] = "'$val'";
                }
            }
            return implode(', ', $values);
        }
        
        protected function get_array_to_query(&$array){
            $values = array();
            foreach($array as $key => $val){
                if(is_null($val)){
                    $values[] = "`$key` = NULL";
                } else {
                    $values[] = "`$key` = '$val'";
                }
            }
            return implode(', ', $values);
        }
    
    }
    
}