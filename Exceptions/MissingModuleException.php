<?php

/** $Id$
 * MissingModuleException.php
 *
 * @version 1.0.0, $Revision$
 * @package Core\Exceptions
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Exceptions;

class MissingModuleException extends \Exception {

    # Missing module name
    private $moduleName = null;

    public function __construct($module, $code = 0x00, \Exception $previous = null) {

        # Save module name
        $this->moduleName = trim($module);

        # Create a simple error message
        $message = 'Required PHP module "'.$this->moduleName.'" was not found.';

        # Push the exception further
        parent::__construct($message, $code, $previous);
    }

    /** Return missing module name.
     * @return string
     */
    public function getModuleName(){
        return $this->moduleName;
    }


}