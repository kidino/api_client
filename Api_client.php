<?php
/*
	API Client Class

	This library is a REST API client for communication with REST API Server
*/ 

class Api_client {
	var $base_url = '';
	var $token = null;
	var $token_file = 'token.txt';
	var $user_agent = 'PHP-API-CLIENT';
	
	function __construct($base_url = null, $token = null, $user_agent = null) {
		if ($base_url != null) {
			$this->base_url = $base_url;
		}
		if ($token != null) {
			$this->token = $token;
		}
		if ($user_agent != '') {
			$this->user_agent = $user_agent;
		}
	}
		
	function go($uri = '', $method = 'GET', $data = null, $expired = null) {
		
		$ch = curl_init($this->base_url.$uri);  
		$header = array();
		if ($data != null) {
			$data_string = json_encode($data);    
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);    
			$header[] = 'Content-Length: ' . strlen($data_string);
		}
		
		if ($this->token != null) {
			$header[] = 'Authorization: Bearer ' . $this->token;
		}
		
		$header[] = 'Content-Type: application/json';
		$header[] = 'User-Agent: '.$this->user_agent;
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);     

		$result = curl_exec($ch);
		$back_data = json_decode($result, true);
		
		if ($back_data != null) {
            return $back_data;
		} else {
            return $result;
        }
	}
    
    function login($login_url, $username, $password) {
        
        $data = array(
            'username' => $username,
            'password' => $password
        );

        $result = $this->go($login_url,'POST',$data);

        if (isset($result['token'])) {
            $this->save_token($result['token']);
            return true;
        } 
        return false;
    }
    
    function save_token($token) {
        $this->token = $token;
        file_put_contents($this->token_file, $token);
    }
    
    function get_token() {
        if (file_exists($this->token_file)) {
            $this->token = file_get_contents($this->token_file);
            return true;
        }
        return false;
    }
    
}
				
