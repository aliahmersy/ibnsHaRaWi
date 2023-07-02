<?php

class Telegram {
	private $token ;
	private $admin = 501030516;
	public function __construct ($token) {
		$this->token = $token;
	}
	public function __call ($method,$args) {
		$url = "https://api.telegram.org/bot".$this->token."/".$method;
	    $ch = curl_init();
	    curl_setopt($ch,CURLOPT_URL,$url);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	    curl_setopt($ch,CURLOPT_POSTFIELDS,$args[0]);
	    $res = curl_exec($ch);
	    if(curl_error($ch)){
	        var_dump(curl_error($ch));
	    }else{
	        $res = json_decode($res); 
			if(isset($res->description) && ! empty($res->description)) {
				$r=print_r($res,1);
				file_get_contents("https://api.telegram.org/bot".$this->token."/sendmessage?chat_id=".$this->admin."&text=$r");
			}
			return $res;
	    }
	}
}


