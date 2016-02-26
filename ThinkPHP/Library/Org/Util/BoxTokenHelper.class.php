<?php
namespace Org\Util;

import("TokenInfo");
function hexToStr($hex)//十六进制转字符串
{   
	$string=""; 
	for($i=0;$i<strlen($hex)-1;$i+=2)
	$string.=chr(hexdec($hex[$i].$hex[$i+1]));
	return  $string;
}
class BoxTokenHelper{
	static private $expire = 604800;
	public static function verifyToken($token){
		$tokenString= hexToStr($token);
		$jsonData = \Think\Crypt\Driver\Base64::decrypt($tokenString,'easonchan');
		$array = json_decode($jsonData,true);
		if($array == null){
			return false;
		}
	
		$created = $array['created'];
		$expire = BoxTokenHelper::$expire;
		$now = time();
		if($created+$expire<$now)
			return false;
		$info = new TokenInfo($array['userId'],$array['departmentId'],$array['groupId'],$array['created']);
		$array['created'] = time();
		$tokenString =  strToHex(\Think\Crypt\Driver\Base64::encrypt(json_encode($array),'easonchan'));
		return array('str'=>$tokenString,'info'=>$info);
	} 
	public static function verifyAuth($auth){
		$array = explode(".", $auth);
		if(count($array) != 3)
			return false;
		$token = $array[0];
		$time = $array[1];
		$sign = $array[2];
		$c = new \sodium\crypto();
		$mysec = $c->keypair();
		$mysec->load("d9e51b64202a4e5d45ae44aad312b2c800771d09f8335b8da664c9d8cc724345","858f393c6446da67e5c3913ec66a8de3c9293f76c0d63d432e6852102eb9418d",true);
		$nonce = new \sodium\nonce();
		$client_public = new \sodium\public_key();
		$client_public->load("65a248a7e527d576d44b918cb3ae02303c9a206bfc2ec56cc135bb9e659e757c", true);
		$sign = $c->box_open(hex2bin($sign),$nonce->set_nonce(hex2bin('565870a7000bd8466f83d97a04333245000067dd443bbb4b'),false),$client_public,$mysec);
		$signArray = explode(":", $sign);
		if(count($signArray)!=2)
			return false;
		if($token != $signArray[0])
			return false;
		if($time!=$signArray[1])
			return false;
		return $token;
	}
}
