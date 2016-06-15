<?php

/** $Id$
 * AbstractEncryption.php
 * @version 1.0.0, $Revision$
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Encryption {
    
    abstract class AbstractEncryption {
        
        private static $CONST_PEPPER = "^_m248#@;mu88!|";

        /** Tworzy względnie bezpieczny hash na podstawie podanego stringu i soli. Zwraca 64 znakowej długości string.
         * Przy hashowaniu haseł sól powinna być unikatowa dla każdego hasła. Użycie funkcji bez podania soli zmiejsza
         * jej bezpieczeństwo.
         * Możliwe jest również nadpisanie domyślnie zdefinowanego "pieprzu" poprzez użycie trzeciego parametru.
         * @access public
         * @author Marek Ulwański <marek@ulwanski.pl>
         * @copyright Copyright (c) 2012, Marek Ulwański
         * @return string
         */
        public static function hash($data, $salt = '', $pepper = null){
            $pepper = self::get_pepper($pepper);
            $reagent   = hash('whirlpool', strrev((string)$pepper).strrev((string)$salt));
            $ripemd256 = hash('ripemd256', strrev((string)$data).$reagent);
            return (string)$ripemd256;
        }
        
        /** Tworzy względnie bezpieczny hash na podstawie podanego stringu i soli. Zwraca 32 znakowej długości string.
         * Przy hashowaniu haseł sól powinna być unikatowa dla każdego hasła. Użycie funkcji bez podania soli zmiejsza
         * jej bezpieczeństwo.
         * Możliwe jest również nadpisanie domyślnie zdefinowanego "pieprzu" poprzez użycie trzeciego parametru.
         * @access public
         * @author Marek Ulwański <marek@ulwanski.pl>
         * @copyright Copyright (c) 2012, Marek Ulwański
         * @return string
         */
        public static function short_hash($data, $salt = '', $pepper = null){
            $pepper = self::get_pepper($pepper);
            $reagent   = hash('whirlpool', strrev((string)$pepper).strrev((string)$salt));
            $ripemd128 = hash('ripemd128', strrev((string)$data).$reagent);
            return (string)$ripemd128;
        }
        
        /** Tworzy 8 znakową sume kontrolną, mieszając dane z solą.
         * Metoda nie powinna być używana do hashowania haseł.
         * @access public
         * @author Marek Ulwański <marek@ulwanski.pl>
         * @copyright Copyright (c) 2012, Marek Ulwański
         * @return string
         */
        protected static function crc($data, $salt = '', $pepper = null){
            $pepper = self::get_pepper($pepper);
            $reagent = hash('whirlpool', strrev((string)$pepper.(string)$salt));
            $crc32   = hash('crc32', strrev((string)$data).$reagent);
            return (string)$crc32;
        }
        
        /** Funkcja implementująca proste szyfrowanie metodą XOR
         * @access public
         * @author Marek Ulwański <marek@ulwanski.pl>
         * @copyright Copyright (c) 2012, Marek Ulwański
         * @return string
         */
        protected static function xor_encode($data, $key){
            $len = strlen($data);
            $key = str_pad($key, $len, $key);
            for($i = 0; $i < $len; $i++){
                $data[$i] = $data[$i] ^ $key[$i];
            }
            return $data;
        }

        /** Szyfruje ciąg tekstowy przy użyciu podanego klucza, zaszyfrowany wynik jest kodowany metodą base64.
         * @access public
         * @author Marek Ulwański <marek@ulwanski.pl>
         * @copyright Copyright (c) 2012, Marek Ulwański
         * @return string
         */
        public static function encode($data, $key, $base64 = true){
            $lenght = strlen($data);
            $key = self::long_key($key, $lenght+1);
            $data = self::xor_encode($data, substr($key, 0, $lenght));
            if ($base64){
                $data = base64_encode($data);
            }
            return $data;
        }

        /** Deszyfruje ciąg tekstowy zaszyfrowany wcześniej metodą <i>encode</i> przy użyciu podanego klucza.
         * @access public
         * @see Api::encode
         * @author Marek Ulwański <marek@ulwanski.pl>
         * @copyright Copyright (c) 2012, Marek Ulwański
         * @return string
         */
        public static function decode($data, $key, $base64 = true){
            if ($base64){
                $data = base64_decode($data);
            }
            $lenght = strlen($data);
            $key = self::long_key($key, $lenght+1);
            return self::xor_encode($data, substr($key, 0, $lenght));
        }

        protected static function long_key($key, $max_len = null){
            $rotate = $key;
            $crypt = '';
            for ($i = 0; $i < strlen($key); $i++) {
                $rotate = self::rotate($rotate);
                $crypt .= self::hash($rotate, strlen($key));
                if($crypt > $max_len) break;
            }
            if ($max_len){
                $crypt = substr($crypt, 0, $max_len);
            }
            return $crypt;
        }

        private static function rotate($string) {
            $n = 1;
            return trim(substr($string, $n) . substr($string, 0, $n));
        }
        
        private static function get_pepper($pepper = null){
            if ($pepper === null){
                $pepper = self::$CONST_PEPPER;
            }
            return (string)$pepper;
        }
        
    }
    
}