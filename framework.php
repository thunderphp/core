<?php
require_once 'Loader/StandardAutoloader.php';                                                                           // Klasa autoloadera musi być wczytana tradycyjnie
require_once 'api.php';                                                                                                 // Klasa "Api" też, bo używamy jej przed autoloaderem

const CORE_VERSION = "1.0.1";                                                                                           // Wersja tekstowa biblioteki
const CORE_VER     = 101.0;                                                                                             // Wersja numeryczna biblioteki

use Core\Loader\StandardAutoloader  as AutoLoader;
use Core\Loader\ConfigurationLoader as ConfLoader;

# Inicjalizacja API
Api::init();                                                                                                            // Inicjalizacja niektórych funkcji API

# Rejestrowanie autoloadera
$loader = new AutoLoader(__DIR__);                                                                                      // Tworzenie instancji autoloadera
spl_autoload_register(array($loader, 'spl_autoload'), AutoLoader::OPT_THROW, AutoLoader::OPT_PREPEND);                  // Rejestrowanie autoloadera
set_exception_handler(array('\Core\Loader\ExceptionHandler', 'catch_exception'));                                       // Rejestrowanie obsługi wyjątków
set_error_handler(array('\Core\Loader\ExceptionHandler', 'catch_error'));                                               // Rejestrowanie obsługi błędów

# Wczytywanie konfiguracji
$config = ConfLoader::getInstance();                                                                                    // Tworzenie konfiguracji serwisu
$config->loadModulesConfig($loader->getModulesPath(), $loader->getModules());                                           // Wczytywanie konfiguracji modułów
$config->loadCoreConfig($loader->getRootPath());                                                                        // Wczytywanie głównej konfiguracji

# Przygotowywanie routera
$router = Api::getRouter();                                                                                             // Stworzenie routera
$router->setRoutingTable($config->getRoutingTable());                                                                   // Przygotowywanie tablicy routingu
