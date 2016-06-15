<?php

/** $Id$
 * StandardRouter.php
 * @version 1.0.0, $Revision$
 * @package TestApp
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Router {

    class StandardRouter
    {

        const ROUTER_SOURCE_CONSOLE = 'SOURCE_CONSOLE';
        const ROUTER_SOURCE_REMOTE = 'SOURCE_REMOTE';

        private $http_request = null;
        private $console_request = null;
        private $source = null;
        private $module = null;
        private $table = array();
        private static $Instance = false;

        /** Tworzy nową instancje klasy, lub zwraca już istniejącą
         * @return \Core\Router\StandardRouter
         */
        public static function getInstance()
        {
            if (self::$Instance == false)
                self::$Instance = new \Core\Router\StandardRouter();
            return self::$Instance;
        }

        private function __construct()
        {
            $this->source = $this->get_request_source();
            $this->generate_run_path();
            header("Server: AmigaOS 1.2, 7,16 MHz", true);
            header("X-Geek: Please don't kill our penguin-powered server.");
            header("X-Powered-By: Bananas, Rum and unicorn farts", true);
        }

        /** Uruchamia router, którz wywołuje odpowiednią akcje.
         * @return String Zwraca ścieżkę url
         */
        public function run()
        {

            if ($this->source == self::ROUTER_SOURCE_REMOTE) {
                $param = '/' . $this->http_request->getFirstParam();
                $action = $this->http_request->getParam(1);
            } else if ($this->source == self::ROUTER_SOURCE_CONSOLE) {
                $param = '/' . $this->console_request->getFirstParam();
                $action = $this->console_request->getParam(1);
            }

            if (empty($param) || $param == '/') {
                $default = \Api::getConfig()->getDefaultRoute();
                if (!empty($default) && $default != '/') {
                    $this->basicRedirect($default);
                    return $default;
                }
            }

            if (key_exists($param, $this->table)) {
                header("HTTP/1.1 200 Be my guest");
                $this->module = $this->table[$param]['module'];
                $config = $this->parse_config($this->table[$param]);
                $source = $this->get_request_source();
                $config['action'] = $action;

                if ($source == self::ROUTER_SOURCE_REMOTE) {
                    $result = $this->runRemoteSource($config);
                } else if ($source == self::ROUTER_SOURCE_CONSOLE) {
                    $result = $this->runConsoleSource($config);
                }
            } else {
                header("HTTP/1.1 404 I do not have what you're looking for");
                $msg = 'Path "' . $param . '" does not exist.';
                throw new RouterException($msg, RouterException::ERROR_MISSING_CONTROLLER);
            }
            return $param;
        }

        /**
         * Function convert url action from some-nice-work to run action: someNiceWorkAction
         *
         * @param $action string
         * @return string
         */
        private function makeAction($action)
        {
            if(!empty($action)) {
                if (strpos($action, '-') !== FALSE) {
                    $actionParts = explode('-', $action);

                    foreach ($actionParts as &$part) {
                        $part = ucwords($part);
                    }

                    $actionParts[0] = strtolower($actionParts[0]);

                    return implode('', $actionParts);
                }

                return strtolower($action);
            }
        }

        private function runRemoteSource($config)
        {
            $controller = new $config['class']();
            $interfaces = class_implements($controller);
            $action = $this->makeAction($config['action']);

            if (!in_array('Core\Controller\BasicActionController', $interfaces)) {
                $msg = 'Class ' . $config['class'] . ' must implements BasicActionController interface!';
                throw new RouterException($msg, RouterException::ERROR_MISSING_INTERFACE);
            }

            $actionName = null;
            if ($this->http_request->isAjaxRequest()) {
                if (method_exists($controller, $action . 'Ajax')) {
                    $actionName = $action . 'Ajax';
                } else if (method_exists($controller, 'defaultAjax')) {
                    $actionName = 'defaultAjax';
                }
            }
            if ($actionName == null && $this->http_request->isXmlRequest()) {
                $xml = substr($action, 0, -4);
                if (method_exists($controller, $xml . 'Xml')) {
                    $actionName = $xml . 'Xml';
                } else if (method_exists($controller, 'defaultXml')) {
                    $actionName = 'defaultXml';
                }
                if ($actionName !== null) header('Content-type: application/xml; charset="utf-8"');
            }
            if ($actionName == null) {
                if (method_exists($controller, $action . 'Action')) {
                    $actionName = $action . 'Action';
                } else {
                    $actionName = 'defaultAction';
                }
            }

            $controller->$actionName();
        }

        private function runConsoleSource($config)
        {
            $controller = new $config['class']();
            $interfaces = class_implements($controller);
            $action = strtolower($config['action']);

            if (!in_array('Core\Controller\BasicConsoleController', $interfaces)) {
                $msg = 'Class ' . $config['class'] . ' must implements BasicConsoleController interface!';
                throw new RouterException($msg, RouterException::ERROR_MISSING_INTERFACE);
            }

            $actionName = null;
            if (method_exists($controller, $action . 'Run')) {
                $actionName = $action . 'Run';
            } else {
                $actionName = 'defaultRun';
            }

            $controller->$actionName();
        }

        /** Powoduje wewnętrzne przekierowanie bez zmiany adresu w przeglądarce
         * @param String $path Ścieżka adresu url do przekierowania
         */
        public function internalRedirect($path)
        {
            $path = trim('/' . trim($path, '/'));
            $this->generate_run_path($path);
            $this->run();
        }

        // @TODO: Dokończyć
        public function getReferer()
        {

        }

        /** Powoduje przekierowanie na inny adres url
         * @param String $url Adres do przekierowania, jeśli pusty - przekieruje do strony głównej serwisu
         */
        public function basicRedirect($url = null)
        {
            if ($url === null) {
                header('location: ' . $this->getRemotePath());
            } else {
                header('location: ' . $this->getRemotePath() . '/' . trim($url, '/'));
            }
            die;
        }

        public function sslRedirect()
        {
            header('location: ' . $this->getHttpRequest()->getRequestPath(true));
        }

        private function generate_run_path($path = null)
        {

            if ($path != null) {
                $base = dirname($_SERVER['SCRIPT_NAME']);
                $_SERVER['REQUEST_URI'] = $base . $path;
            }

            if ($this->source == self::ROUTER_SOURCE_REMOTE) {
                $this->http_request = new \Core\Router\HttpRequest();
            } else if ($this->source == self::ROUTER_SOURCE_CONSOLE) {
                $this->console_request = new \Core\Router\ConsoleRequest();
            }
        }

        private function get_request_source()
        {
            // Drupal way (patch)
            $is_cli = (!isset($_SERVER['SERVER_SOFTWARE']) && (php_sapi_name() == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0)));

            $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : "";
            $argv = (isset($_SERVER['argv'])) ? $_SERVER['argv'] : null;
            $argc = (isset($_SERVER['argc'])) ? $_SERVER['argc'] : 0;

            if ($is_cli) {
                return self::ROUTER_SOURCE_CONSOLE;
            } else {
                return self::ROUTER_SOURCE_REMOTE;
            }
        }

        private function parse_config($conf)
        {
            $action = array();
            $action['class'] = implode('\\', array('', ucfirst($conf['module']), 'Controller', $conf['controller']));
            return $action;
        }

        public function setRoutingTable(array $table)
        {
            $this->table = $table;
        }

        /** Zwraca obiekt żądania http
         * @return \Core\Router\HttpRequest
         */
        public function getHttpRequest()
        {
            return $this->http_request;
        }

        /** Zwraca obiekt żądania konsolowego
         * @return \Core\Router\ConsoleRequest
         */
        public function getConsoleRequest()
        {
            return $this->console_request;
        }

        /** Metoda zwraca ścieżkę <b>zdalną</b> do katalogu głównego serwera, np. katalog <i>public</i>.
         *  Jest to najwyższa lokalizacja dostępna zdalnie.
         *
         * @return String
         */
        public function getRemotePath()
        {
            static $path = false;
            if (!isset($_SERVER["HTTPS"])) $_SERVER["HTTPS"] = 'off';
            $scheme = ($_SERVER["HTTPS"] == "on") ? 'https' : 'http';
            if (!$path) {
                if (isset($_SERVER['PHP_SELF'])) {
                    $path = $_SERVER['PHP_SELF'];
                } elseif (isset($_SERVER['SCRIPT_NAME'])) {
                    $path = $_SERVER['SCRIPT_NAME'];
                }
                if ($this->source == self::ROUTER_SOURCE_REMOTE) {
                    $path = $scheme . '://' . $_SERVER['HTTP_HOST'] . dirname($path);
                    $path = trim($path, '/');
                } else if ($this->source == self::ROUTER_SOURCE_CONSOLE) {
                    $path = dirname($path);
                    $path = '/' . trim($path, '/');
                }
            }
            return $path;
        }

        /** Metoda zwraca ścieżkę <b>lokalną</b> do katalogu głównego serwera, np. <i>public</i>.
         *  Jest to najwyższa lokalizacja dostępna zdalnie.
         *
         * @return String
         */
        public function getLocalPath()
        {
            static $path = false;
            if (!$path) {
                if (DIRECTORY_SEPARATOR == "/") {
                    $path = realpath(str_replace("\\", "/", dirname($_SERVER['SCRIPT_FILENAME'])));
                } else {
                    $path = realpath(str_replace("/", "\\", dirname($_SERVER['SCRIPT_FILENAME'])));
                }
            }
            return $path;
        }

        public function getRootPath()
        {
            return realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
        }

        public function getModuleName()
        {
            return $this->module;
        }

    }

}