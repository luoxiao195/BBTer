<?php

namespace Home\Controller;
use Think\Controller;
import(Org.Util.Token);
import(Org.Util.GibberishAES);

class LoginController extends Controller{
	var $result;
	protected function _initialize(){
		$this->result = array();
	}
	protected function AssignOwn($key,$value){
		$this->result[$key] = $value;
	}
	protected function DisplayOwn(){
		$this->assign('result',json_encode($this->result));
		$this->display('./result');
	}
	
	public function Index(){
		$stuNo = I("post.studentno");
		$password = I("post.password");
		if(empty($stuNo) || empty($password)){
			$this->AssignOwn('code',101);
			$this->AssignOwn('info','数据不能为空');
			$this->AssignOwn('data',null);
			$this->AssignOwn('token',null);
			$this->DisplayOwn();
			die();
		}
		$user = M('users');
		$data = $user->where("student_number=$stuNo")->select();
		if(empty($data)){
			$this->AssignOwn('code',102);
			$this->AssignOwn('info','用户名不存在');
			$this->AssignOwn('data',null);
			$this->AssignOwn('token',null);
			$this->DisplayOwn();
			die();
		}
		if($data['status'] != 'NORMAL'){
			$this->AssignOwn('code',102);
			$this->AssignOwn('info','用户名不存在');
			$this->AssignOwn('data',null);
			$this->AssignOwn('token',null);
			$this->DisplayOwn();
			die();
		}
			
		if($data['password'] != $password){
			$this->AssignOwn('code',103);
			$this->AssignOwn('info','密码错误');
			$this->AssignOwn('data',null);
			$this->AssignOwn('token',null);
			$this->DisplayOwn();
			die();
		}

		$created = time();
		$userId = $data['id'];
		$departmentId = $data['department_id'];
		$groupId = $data['group_id'];
		$token = \Org\Util\TokenInfo::tokenString($userId,$departmentId,$groupId,$created);
		if($data['completed'] == 1)
			$this->AssignOwn('code',200);
		else
			$this->AssignOwn('code',201);
		// $date = time();
		// $auth = $token.'.'.$date;
		// $enc = \Org\Util\GibberishAES::enc($token.':'.$date,'isayserious');
		// $auth = $auth.'.'.$enc;
		$this->AssignOwn('token',$token);

		$this->DisplayOwn();
	}
}


?>