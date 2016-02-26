<?php

namespace Org\Util;


function strToHex($string)//字符串转十六进制
{ 
	$hex="";
	for($i=0;$i<strlen($string);$i++)
	$hex.=dechex(ord($string[$i]));
	$hex=strtoupper($hex);
	return $hex;
}   


class TokenInfo{
	public $userId;
	public $departmentId;
	public $groupId;
	public $created;
	public function __construct($userId,$departmentId,$groupId,$created){
		$this->userId = $userId;
		$this->departmentId = $departmentId;
		$this->groupId = $groupId;
		$this->created = $created;
	}
	public static function tokenString($userId,$departmentId,$groupId,$created){
		$tokenString = array();
		$tokenString['userId'] = $userId;
		$tokenString['departmentId'] = $departmentId;
		$tokenString['groupId'] = $groupId;
		$tokenString['created'] = $created;
		return strToHex(\Think\Crypt\Driver\Base64::encrypt(json_encode($tokenString),'easonchan'));
	}
};


