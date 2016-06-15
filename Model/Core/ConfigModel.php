<?php
/** $Id$
 * ConfigModel.php
 *
 * @version 1.0.0, $Revision$
 * @package Core\Model\Core
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Model\Core;

use \ArrayIterator;


class ConfigModel extends ArrayIterator {

    public function __construct( array $configuration = null) {

        if(is_array($configuration)){
            $this->addConfigPath($configuration);
        }
    }

    /** Add new path to configuration
     * @param array $config_path
     * @return void
     */
    protected function addConfigPath(array $config_path){

        foreach($config_path as $key => $value){
            if(is_array($value)){
                $this[$key] = new ConfigModel($value);
            } else {
                $this[$key] = $value;
            }
        }

    }

    public function __get($name) {
        if(isset($this[$name])){
            return $this[$name];
        } else {
            return null;
        }
    }

    public function getValue($name, $default = null){

        if(!isset($this[$name])) return $default;

        return $this[$name];
    }

}