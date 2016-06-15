<?php

/** $Id$
 * ArrayFileCache.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Cache\Storage {

    class ArrayFileCache extends FileCache {
        
        public function __construct($name, $ext = '') {
            parent::__construct($name, $ext);
        }

        /** Zapisuje do pliku przekazaną tablicę
         * @param Array $var Tablica do zapisania (może być wielowymiarowa)
         */
        public function set($var) {
            if(!is_array($var)) return false;
            $out = "return ".var_export($var, true);
            var_dump($out);
            parent::set('<?php'."\n\n".$out.';');
        }
        
        /** Wczytuje z pliku, zapisaną wcześniej tablicę danych.
         * @return mixed Tablica, lub null jeśli nie znaleziono pliku
         */
        public function load() {
            if(file_exists($this->_path)){
                return require $this->_path;
            }
            return null;
        }
        
    }
    
}