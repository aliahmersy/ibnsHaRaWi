<?php


class MainClass
{
	private $user,
	$pass,
	$key;
	private $link = 'http://api.drncloud.com/out/ext_api/';
	public function __construct($user, $pass, $key) {
		$this->user = $user;
		$this->pass = $pass;
		$this->key = $key;
		return true;
	}
	public function returnLinkWithMethodAndMainData($method) {
		return $this->link . $method . '?name=' . $this->user . '&pwd=' . $this->pass . '&ApiKey=' . $this->key;
	}
	// get number
	public function getNumber($countryCode, $app = "0107", $boackList = 0 /*0=yours,1=all*/) {
		$get = json_decode(file_get_contents($this->returnLinkWithMethodAndMainData("getMobile") . "&cuy=$countryCode&pid=$app&num=1&noblack=$boackList&serial=2"));
		if ($get->code == 200) {
			$num = $get->data;
			if (empty($num))
				return array(
					'Error' => 'empty number'
				);
			else
				return array(
					'Error' => null,
					'num' => $num,
					'id' => $app
			);
		} else {
			return array(
				'Error' => $get->msg
			);
		}
	}
	public function getCode($num, $id) {
		$get = json_decode(file_get_contents($this->returnLinkWithMethodAndMainData("getMsg") . "&pn=$num&pid=$id&serial=2"));
		if ($get->code == 200) {
			$code = $get->data;
			if (empty($code))
				return array(
					'Error' => 'empty code'
				);
			else
				return array(
					'Error' => null,
					'code' => $code
				);
		} else {
			return array(
				'Error' => $get->msg
			);
		}
	}
	public function banNum($num, $id) {
		$get = json_decode(file_get_contents($this->returnLinkWithMethodAndMainData("addBlack") . "&pn=$num&pid=$id"));
		return $get->msg;
	}
}