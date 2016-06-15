<?php

/** $Id$
 * PaymentModel.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Model\Payments {
    
    use Core\Model\AbstractModel;
    
    class PaymentModel extends AbstractModel {
        
        private $crc;
        private $lang;
        private $number;
        private $amount;
        private $currency;
        private $description;
        private $email;
        private $firstname;
        private $lastname;
        private $url_success;
        private $url_error;
        
        public function __construct($lang = 'pl') {
            $this->currency = 'PLN';
            $this->lang = $lang;
        }
        
        public function getTPayFormArray( $id, $code, $online = 1 ) {
            $data = array(
                'id'            => (string)$id,
                'kwota'         => (string)number_format($this->amount, 2, '.', ''),
                'opis'          => (string)trim($this->description),
                'crc'           => (string)$this->number,
                'direct'        => 0,
                'online'        => $online,
                'email'         => $this->email,
                'nazwisko'      => $this->firstname.' '.$this->lastname,
                'pow_url'       => $this->url_success.'?t=tpay',
          q      'pow_url_blad'  => $this->url_success.'?t=tpay',
                'jezyk'         => $this->lang,
            );
            $data['md5sum'] = md5($data['id'].$data['kwota'].$data['crc'].$code);
            return $data;
        }
        
        public function getDotpayFormArray( $id, $api_ver = 'dev', $redirect_type = 3) {
            return array(
                'id'            => (string)$id,
                'amount'        => (string)number_format($this->amount, 2, '.', ''),
                'currency'      => (string)$this->currency,
                'description'   => trim(substr($this->description, 0, 255)),
                'lang'          => $this->lang,
                'control'       => (string)substr($this->number, 0, 128),
                'api_version'   => trim($api_ver),
                'URL'           => $this->url_success.'?t=dotpay',
                'type'          => (int)$redirect_type,
                'firstname'     => substr($this->firstname, 0, 50),
                'lastname'      => substr($this->lastname, 0, 50),
                'email'         => substr($this->email, 0, 100),
                'buttontext'    => 'Wróć do serwisu',
                'country'       => 'PL',
                'p_info'        => 'Eroticam'
            );
        }
        
        public function getNumber() {
            return $this->number;
        }

        public function getCrc() {
            return $this->crc;
        }

        public function getLang() {
            return $this->lang;
        }

        public function getAmount() {
            return $this->amount;
        }

        public function getCurrency() {
            return $this->currency;
        }

        public function getDescription() {
            return $this->description;
        }

        public function getEmail() {
            return $this->email;
        }

        public function getFirstname() {
            return $this->firstname;
        }

        public function getLastname() {
            return $this->lastname;
        }

        public function getUrlSuccess() {
            return $this->url_success;
        }

        public function setNumber($number) {
            $this->number = $number;
            return $this;
        }

        public function setCrc($crc) {
            $this->crc = $crc;
            return $this;
        }

        public function setLang($lang) {
            $this->lang = $lang;
            return $this;
        }

        public function setAmount($amount) {
            $this->amount = $amount;
            return $this;
        }

        public function setCurrency($currency) {
            $this->currency = $currency;
            return $this;
        }

        public function setDescription($description) {
            $this->description = $description;
            return $this;
        }

        public function setEmail($email) {
            $this->email = $email;
            return $this;
        }

        public function setFirstname($firstname) {
            $this->firstname = $firstname;
            return $this;
        }

        public function setLastname($lastname) {
            $this->lastname = $lastname;
            return $this;
        }

        public function setUrlSuccess($url_success) {
            $this->url_success = $url_success;
            return $this;
        }

            
    }
    
}