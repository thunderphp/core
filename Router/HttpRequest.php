<?php

/** $Id$
 * HttpRequest.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Router {

    class HttpRequest {

        const SCHEME_HTTP = 1;
        const SCHEME_HTTPS = 2;
        const ACCEPT_TEXT = 'text/plain';
        const ACCEPT_HTML = 'text/html';
        const ACCEPT_JSON = 'application/json';
        const ACCEPT_JAVASCRIPT = 'text/javascript';

        private $requestParams = array();                                                                               // Parametry z adresu, dane GET i POST
        private $requestRaw = array();                                                                                  // Dane przekazane metodą POST bez filtrowania (NIEBEZPIECZNE)
        private $requestArray = array();                                                                                // Tablica przechowuje tylko parametry z adresu (nie asocjacyjnie)
        private $requestHeaders = array();                                                                              // Tablica przechowuje nagłówki http żądania
        private $requestLangs = null;                                                                                   // Tablica preferowanych języków
        private $urlCount = 0;                                                                                          // Liczba parametrów w ścieżce do zasobów (np. "/home/last/10" = 3, parametry GET nie są wliczane)
        private $url_scheme = false;                                                                                    // Rodzaj protokołu (http lub https)
        // Jeżeli żądanie posiadało ID, jest ono przepisywane do tej zmiennej
        private $request_id = false;

        /**
         * Var have bool state for isPost
         *
         * @var bool
         */
        private $isPost = false;

        public function __construct() {
            if ($_SERVER['REMOTE_ADDR'] == '::1') $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            if (!isset($_SERVER['REQUEST_SCHEME'])){
                $scheme = explode('/', $_SERVER['SERVER_PROTOCOL']);
                $_SERVER['REQUEST_SCHEME'] = strtolower($scheme[0]);
            }
            $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];                // Tworzymy pełny adres zapytania, razem z parametrami
            $dir = dirname($_SERVER['SCRIPT_NAME']);                                                                    // Znajdujemy ścieżkę zdalną (część pomiędzy hostem a 'index.php')
            $url = parse_url(str_replace(trim($dir, '/'), '', $url));                                                   // Parsujemy pełny adres, ale z pominięciem ścieżki zdalnej

            if(function_exists('apache_request_headers')) $this->requestHeaders = apache_request_headers();

            if (isset($url['scheme'])) {                                                                                // Zapisujemy protokół wywołania (http lub https)
                if ($url['scheme'] == 'http')
                    $this->url_scheme = self::SCHEME_HTTP;
                if ($url['scheme'] == 'https')
                    $this->url_scheme = self::SCHEME_HTTPS;
            }

            if (isset($_POST) and !empty($_POST)) {

                $this->isPost = true;

                // Przepisywanie danych POST do lokalnej tablicy
                foreach ($_POST as $key => $val) {
                    $key = $this->clean_param($key);
                    $this->requestParams[$key] = $this->clean_data($val);
                    $this->requestRaw[$key] = $val;
                }
            }

            if (isset($url['query'])) {                                                                                 // Przepisywanie danych GET do lokalnej tablicy
                $_query = explode('&', $url['query']);
                foreach ($_query as $q) {
                    $tmp = explode('=', $q);
                    $key = $this->clean_param($tmp[0]);
                    if (isset($tmp[1]))
                        $this->requestParams[$key] = $this->clean_param($tmp[1]);
                    else
                        $this->requestParams[$key] = null;
                }
            }

            if ($url['path'] != '' and $url['path'] != '/') {                                                           // Jeżeli przesłano ścieżkę do żądanych zasobów (np. /home/last), dzielimy ją na tablicę
                $path = explode('/', trim($url['path'], '/'));
                foreach ($path as $i => $p) {
                    $this->requestArray[$i] = $this->clean_param($p);
                    $this->urlCount++;
                }
            }

            $count = ((int) $this->urlCount) - 1;
            if (isset($this->requestArray[$count])) {
                if ($this->is_decimal($this->requestArray[$count]))
                    $this->request_id = (int) $this->requestArray[$count];                                              // Jeżeli ostatni parametr jest liczbą dziesiętną, przepisujemy je jako 'id'
            }

            if(isset($this->requestHeaders['Accept-Language'])){
                $this->requestLangs = $this->parse_accept_language( $this->requestHeaders['Accept-Language'] );
            }
                    
            //$_REQUEST = array();
            //$_POST = array();
            //$_GET = array();
        }

        public function isPost()
        {
            return $this->isPost;
        }

        public function __get($name) {
            if (isset($this->requestParams[$name])) return $this->requestParams[$name];
            else return false;
        }

        public function toArray() {
            $host = parse_url($_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
            $host['sub'] = explode('.', str_replace('www.', '', $host['host']), -2);
            return $host;
        }

        public function __set($name, $value = null) {
            if (!isset($this->requestParams[$name]))
                $this->requestParams->$name = $value;
        }

        /** Zwraca ID przekazane w adresie lub zwraca wartość domyślną przekazaną w parametrze.
         * @access public
         * @param  mixed $default Domyślna wartość, zwracana jeśli ID nie istnieje.
         * @return int Numer id przekazany w rządaniu http (jeśli został przekazany)
         */
        public function getId($default = false) {
            if ($this->request_id)
                return (int) $this->request_id;
            else
                return $default;
        }

        /** Zwraca parametr <b>POST</b> lub <b>GET</b> przekazany w żądaniu. Po przekazaniu numeru parametry zamiast nazwy,
         *   próbuje najpierw dopasować pasujący parametr ścieżki w żądania http, bez argumentów zwraca tablicę rządań.
         * @access public
         * @param  string|int $name Nazwa lub numer parametru.
         * @param  mixed $default Wartość zwracana, jeśli parametr nie został odnaleziony.
         * @return mixed Dane z rządania http
         */
        public function getParam($name = null, $default = false) {
            if ($name === null)
                return $this->requestArray;
            if (is_numeric($name)) {
                if (isset($this->requestArray[$name]))
                    return $this->requestArray[$name];
            }
            if (isset($this->requestParams[$name]))
                return $this->requestParams[$name];
            return $default;
        }

        /** Funkcja zwraca w żaden sposób <b>nie filtrowane</b> dane przesłane metodą POST.
         *   Zwrócone dane, należy <i>samodzielnie przefiltrować</i> przed zapisaniem do bazy danych.
         * @access public
         * @param  string $name Nazwa przekazanego parametru metodą POST.
         * @return string Dane POST
         */
        public function getRaw($name = null) {
            if ($name === null)
                return (array) $this->requestRaw;
            if (isset($this->requestRaw[$name]))
                return $this->requestRaw[$name];
            return false;
        }

        /** Funkcja zwraca ostatni parametr z żądania ścieżki
         * @access public
         * @param  int $offset Przekazanie dodatniej liczby spowoduje przesunięcie do tyłu licznika.
         * @return string Dane z żądania http
         */
        public function getLastParam($offset = 0) {
            $count = ((int) $this->urlCount - $offset);
            return $this->requestArray[$count - 1];
        }

        public function getFirstParam() {
            $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];                // Tworzymy pełny adres zapytania, razem z parametrami
            $dir = dirname($_SERVER['SCRIPT_NAME']);                                                                    // Znajdujemy ścieżkę zdalną (część pomiędzy hostem a 'index.php')
            $url = parse_url(str_replace(trim($dir, '/'), '', $url));                                                   // Parsujemy pełny adres, ale z pominięciem ścieżki zdalnej
            $path =  trim($url['path'], '/');
            $array = explode('/', $path);
            return $array[0];
        }

        public function getRequestArray() {
            return $this->requestArray;
        }

        public function getRawRequestParams() {
            return $this->requestParams;
        }

        public function getRequestPath($force_ssl = false) {
            if($force_ssl){
                $scheme = 'https';
            } else {
                $scheme = $_SERVER['REQUEST_SCHEME'];
            }
            $url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];                // Tworzymy pełny adres zapytania, razem z parametrami
            return trim($url, '/');
        }

        /* Metoda zwraca <i>true</i> jeżeli żądanie zostało wykonane metodą Ajax, biblioteki jQuery
         * @access public
         * @return bool
         */
        public function isAjaxRequest() {
            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
                return false;
            $hrw = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
            return (bool) (!empty($hrw) && $hrw == 'xmlhttprequest');
        }
        
        public function isXmlRequest() {
            $pos = strpos($_SERVER['REQUEST_URI'], '?');
            if($pos === false) $pos = strlen($_SERVER['REQUEST_URI']);
            $ext = substr($_SERVER['REQUEST_URI'], $pos - 4, 4);
            if($ext == ".xml") return true;
            return false;
        }
        
        public function isSSLRequest() {
            return (bool)($_SERVER['REQUEST_SCHEME'] == self::SCHEME_HTTPS)?true:false;
        }

        /* Metoda zwraca pierwszy ze znanych sobie formatów danych ze wszystkich wysłanych w żądaniu http
         * @access public
         * @return mixed Zwraca string opisujący żądanie lub <i>false</i>
         */
        public function getAcceptFormat() {
            $hac = strtolower(filter_input(INPUT_SERVER, 'HTTP_ACCEPT'));
            $accept = explode(',', $hac);

            $formats = array(self::ACCEPT_HTML, self::ACCEPT_JSON, self::ACCEPT_TEXT, self::ACCEPT_JAVASCRIPT);

            foreach ($accept as $a) {
                if (in_array($a, $formats))
                    return $a;
            }
            return false;
        }

        public function getRequestHeaders($name = null) {
            if ($name !== null) {
                if (isset($this->requestHeaders[$name]))
                    return $this->requestHeaders[$name];
                return false;
            }
            return $this->requestHeaders;
        }

        private function clean_param($string) {
            if (is_numeric($string))
                return (int) $string;
            $search = array('--', '..', '__', ' ');
            $replace = array('-', '.', '_', '');
            $string = str_replace($search, $replace, $string);
            $string = preg_replace("/[^a-z0-9@._-]+/", "", strtolower($string));
            return trim($string);
        }

        private function clean_data($string) {

            if (is_array($string)) {                                                                                    // Jeżeli dane są tablicą obrabiany kolejno każdy jej element
                foreach ($string as $key => $val)
                    $string[$key] = $this->clean_data($val);
                return $string;
            }

            if (is_numeric($string)) {                                                                                  // Jeżeli dane są numeryczne, nie obrabiamy ich
                if (floor((float) $string) != $string and fmod((float) $string, 1) !== 0)
                    return (float) $string;      // Jeżeli dane są wartością 'float', zwracamy je ...
                else
                    return (int) $string;                                                                               // w przeciwnym razie zwracamy je jako 'int'
            } else {
                $string = strip_tags($string);                                                                          // Usuwamy tagi html
                $string = htmlspecialchars($string, ENT_QUOTES);                                                        // Usuwamy znaki specjalne
                return (string) trim($string);                                                                          // Trimujemy i zwracamy
            }
        }

        private function is_decimal($val) {
            return is_numeric($val) && floor($val) == $val;
        }

        private function parse_accept_language($string) {            
            $lang_parse = '';            
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $string, $lang_parse);

            if (count($lang_parse[1])) {
                $langs = array_combine($lang_parse[1], $lang_parse[4]);                                                 // Create a list like "en" => 0.8
                foreach ($langs as $lang => $val) if ($val === '') $langs[$lang] = 1;                                   // Set default to 1 for any without q factor	
                arsort($langs, SORT_NUMERIC);                                                                           // Sort list based on value
            }
            
            return $langs;
        }
        
        public function getClientIp(){
            return filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        }
        
        public function getClientIpLong(){
            $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
            return ip2long($ip);
        }

    }

}