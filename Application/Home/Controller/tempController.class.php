<?php

namespace Home\Controller;
use Think\Controller;
import(Org.Util.BoxTokenHelper);
class tempController extends Controller{
	protected $auth;
	protected $token;
	protected $info;
	protected $data;
	protected $code;
	protected $param;
	protected $userId;
	protected $departmentId;
	protected $groupId;
	protected $result;
	protected function _initialize(){
		//检查token
		/*$this->auth = I('param.auth');
		//先检查auth
		$token =  \Org\Util\BoxTokenHelper::verifyAuth($this->auth);
		if($token == false){
	
			$this->authFailed();
		}
		//调用token类验证
		$tokenArray = \Org\Util\BoxTokenHelper::verifyToken($token);
		if($tokenArray == false){
	
			$this->authFailed();
		}
		$this->token = $tokenArray['str'];
		$this->userId = $tokenArray['info']->userId; */

		if(empty(I('post.userId')))
			$this->userId=17;
		else
			$this->userId=I('post.userId');
		$this->token='666';
		$User = D('users');
		$data = $User->find($this->userId);

		if(empty($data)){
			$this->authFailed();
		}

		$this->groupId = $data['group_id'];
		$this->departmentId = $data['department_id'];
		$this->code = 200;
		/*if(!$data['completed']){
			$this->code = 201;
			if(CONTROLLER_NAME == 'User' && ACTION_NAME=='infoEdit')
				$this->code = 200;
			else
				$this->finish();
		}
		$this->parseDataTypeParam();*/
		$this->result = array();
	}
	
	protected function transferNullToEmpty($param){
		if($param == null || empty($param) || !isset($param))
			return '';
		return $param;
	}

	protected function AssignOwn($key,$value){
		$value = $this->transferNullToEmpty($value);
		$this->result[$key] = $value;
	}
	protected function DisplayOwn(){					//将数组result中的数据输出
		$this->assign('result',json_encode($this->result));
		$this->display('./result');
		die();
	}

	protected function getParam($key){
		return $this->param[$key];
	}

	protected function finish(){
		if($this->token == null || $this->code==null){
			return false;
		}
		$this->AssignOwn('code',$this->code);
		$this->AssignOwn('info',$this->info);
		$this->AssignOwn('data',$this->data);
		$this->AssignOwn('token',$this->token);
		$this->DisplayOwn();
	}

	private function parseDataTypeParam(){
		$this->param = I('param.data');
	}

	private function authFailed(){
		$this->AssignOwn('code',302);
		$this->AssignOwn('info',"请重新登陆");
		$this->AssignOwn('data',null);
		$this->AssignOwn('token',null);
		$this->DisplayOwn('./result');
		die();

	}

	protected function forbidden($read = false){
		if($read)
			$this->AssignOwn('code',404);
		else
			$this->AssignOwn('code',403);
		$this->AssignOwn('info',"无此权限");
		$this->AssignOwn('data',null);
		$this->AssignOwn('token',$this->token);
		$this->DisplayOwn('./result');
		die();
	}

	protected function checkNotEmptyAndGetParam($key){
		if(!isset($this->param[$key]) || empty($this->param[$key])){
			$this->AssignOwn('code',101);
			$this->AssignOwn('info',"$key"."不能为空");
			$this->AssignOwn('data',null);
			$this->AssignOwn('token',$this->token);
			$this->DisplayOwn();
			die();
		}
		return $this->getParam($key);
	}

	public function checkSetAndGetParam($key){
		if(!isset($this->param[$key]) ){
			$this->AssignOwn('code',101);
			$this->AssignOwn('info',"$key"."不能为空");
			$this->AssignOwn('data',null);
			$this->AssignOwn('token',$this->token);
			$this->DisplayOwn();
			die();
		}
		return $this->getParam($key);
	}

	protected function dataFormatWrong($key){
		$this->AssignOwn('code',104);
		$this->AssignOwn('info',"$key"."格式错误");
		$this->AssignOwn('data',null);
		$this->AssignOwn('token',$this->token);
		$this->DisplayOwn();
		die();
	}

	protected function successWithEmptyData(){
		$this->AssignOwn('code',200);
		$this->AssignOwn('info',null);
		$this->AssignOwn('data',null);
		$this->AssignOwn('token',$this->token);
		$this->DisplayOwn('./result');
		die();
	}


	protected function setData($key,$value,$permission=''){
		if($value === $permission)
			$this->data[$key] = $value;
		else{
			$value = $this->transferNullToEmpty($value);
			$this->data[$key] = $value;	
		}
	}

	protected function serviceError(){
		$this->AssignOwn('code',500);
		$this->AssignOwn('info',null);
		$this->AssignOwn('data',null);
		$this->AssignOwn('token',$this->token);
		$this->DisplayOwn();
		die();
	}






}