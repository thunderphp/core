<?php

/** $Id$
 * User.php
 * @version 1.0.0, $Revision$
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Users {
    
    use \Core\Users\AbstractUser;
    use \Core\Database\SqlGateway;
    use \Core\Encryption\AbstractEncryption as Encryption;
    
    class User extends AbstractUser {
        
        const ERROR_SUCCESS = 0x00;
        const ERROR_TIMEOUT = 0x01;
        const ERROR_HASH    = 0x02;
        const ERROR_USER    = 0x03;
        
        private static $Instance = false;
        private $timeout = 600; // 10 min

        public static function getInstance() {
            if (self::$Instance == false) self::$Instance = new \Core\Users\User();
            return self::$Instance;
        }
        
        public function __construct() {
            parent::__construct();
            
            $this->id     = (int) filter_var($this->id,    FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE);         // Filtrujemy id użytkownika
            $this->trace  = (int) filter_var($this->trace, FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE);         // Filtrujemy czas dostępu użytkownika
            $this->client = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
            
            $geoip = filter_input(INPUT_SERVER, 'GEOIP_CITY_CONTINENT_CODE').' '.filter_input(INPUT_SERVER, 'GEOIP_CITY_COUNTRY_CODE');
            $this->geoip   = (trim($geoip) == "")?null:$geoip;
            $this->country = filter_input(INPUT_SERVER, 'GEOIP_CITY_COUNTRY_NAME');
            $this->region  = filter_input(INPUT_SERVER, 'GEOIP_REGION');
            $this->city    = filter_input(INPUT_SERVER, 'GEOIP_CITY');
            
            if($this->id != 0 && $this->isAuthorized()){

                if($this->trace == 0 or $this->trace < (time() - $this->timeout)){
                    return $this->logout( self::ERROR_TIMEOUT );
                }

                $hash = $this->get_user_hash($this->id);
                if($hash != $this->attributes['hash']){
                    return $this->logout( self::ERROR_HASH );
                }

                $this->trace  = time();
            } else {
                $this->auth = 'guest';
            }
            
        }

        public function isAuthorized(){
            $auth = $this->auth;
            return (empty($auth) || $auth == 'guest')?false:true;
        }
        
        public function login($username, $password) {
            
            $db = SqlGateway::getInstance();
            /* @var $db \Core\Database\SqlGateway */

            $info   = $db->getRowByField('users', 'id, birthday, created', 'email', $username);
            $user   = false;


            if($info){                                                                                                  // Porównujemy login z hasłem tylko jeśli użytkownik o takim loginie w istnieje
                $salt = $info['birthday'].$info['created'];                                                             // Zapisujemy sól do hashowania hasła
                $hash = Encryption::hash($password, $salt);                                                             // Obliczamy skrót hasła
                $select = "id, firstname, lastname, username, email, status, auth, lang, sex, settings, theme, created, logged";
                $user = $db->getRowByWhere('users', $select, '`id` = "'.$info['id'].'" AND `password` = "'.$hash.'"');
            }

            if($user){
                foreach( $user as $key => $value ){
                    $this->attributes[$key] = $value;
                    $_SESSION[$key] = $value;
                }
                $this->attributes['trace'] = time();
                $this->attributes['hash']  = $this->get_user_hash($this->id);
                $_SESSION['trace'] = $this->attributes['trace'];
                $_SESSION['hash']  = $this->attributes['hash'];
                return true;
            }

            return false;
        }

        public function loginByFacebookId($username, $facebook_id) {

            $db = SqlGateway::getInstance();
            /* @var $db \Core\Database\SqlGateway */

            $select = "id, firstname, lastname, username, email, status, auth, lang, sex, settings, theme, created, logged";
            $user = $db->getRowByWhere('users', $select, '`facebook` = "'.$facebook_id.'" AND `username` = "'.$username.'" LIMIT 1;');

            if($user){
                foreach( $user as $key => $value ){
                    $this->attributes[$key] = $value;
                    $_SESSION[$key] = (string)$value;
                }

                $this->attributes['trace'] = time();
                $this->attributes['hash']  = $this->get_user_hash($this->id);
                $_SESSION['trace'] = (string)$this->attributes['trace'];
                $_SESSION['hash']  = (string)$this->attributes['hash'];
                return true;
            }

            return false;
        }

        public function logout( $error = false ) {
            $data = array();
            foreach($this->attributes as $key => $value){
                if(substr($key, 0, 1) == '_'){
                    $data[$key] = $value;
                }
            }
            $this->attributes = array();
            foreach($data as $key => $value){
                $this->attributes[$key] = $value;
            }
            $_SESSION = $this->attributes;
            $this->logout_reason = $error;
            return $error;
        }
        
        private function get_user_hash($user_id){
            $params = array(
                filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
                filter_input(INPUT_SERVER, 'REMOTE_ADDR'),
            );
            return sha1(json_encode($params).$user_id);
        }
        
    }
    
}