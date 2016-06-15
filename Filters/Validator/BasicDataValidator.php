<?php

/** $Id$
 * BasicDataValidator.php
 * @version 1.0.0, $Revision$
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Filters\Validator {

    class BasicDataValidator {

        public function email($email) {
            $sanitized = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
            $data = filter_var($sanitized, FILTER_VALIDATE_EMAIL);
            return $data;
        }
        
        public function length($subject, $min = null, $max = null) {
            $len = (int)strlen($subject);
            if($min !== null) if($len < $min) return false;
            if($max !== null) if($len > $max) return false;
            return $subject;
        }
        
        public function regex($subject, $pattern) {
            $matches = null;
            $result  =  preg_match( $pattern, $subject, $matches );
            if($result == 0) return false;
            if($matches[0] == $subject) return $subject;
            return false;
        }

    }

}