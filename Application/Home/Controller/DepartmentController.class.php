<?php

namespace Home\Controller;
use Think\Controller;

class DepartmentController extends BaseController{
	public function listAll(){
		$this->setData('editor',$this->groupId>=4,false);
		$depr = D('departments');
		$datas = $depr->select();
		$dprtArray = array();
		foreach ($datas as $index => $dprt) {
			# code...
			$department= array();
			$department['id'] = $this->transferNullToEmpty($dprt['id']);
			$department['dprtname'] = $this->transferNullToEmpty($dprt['name']);
			$department['dprtnote'] = $this->transferNullToEmpty($dprt['note']);
			array_push($dprtArray, $department);
			
		}
		$this->setData('dprt',$dprtArray);
		$this->code =200;
		$this->finish();
	}

	public function del(){
		if($this->groupId<4)
			$this->forbidden();
		$id = $this->checkNotEmptyAndGetParam('id');
		$dprt = D('departments');
		$dprt->delete("$id");
		$this->successWithEmptyData();
	}

	public function add(){
		if($this->groupId<4)
			$this->forbidden();
		$data['name'] = $this->checkNotEmptyAndGetParam('dprtname');
		$data['note'] = $this->checkSetAndGetParam('dprtnote');
		$dprt = D('departments');
		$dprt->data($data)->add();
		$this->successWithEmptyData();

	}

	public function detail(){
		if($this->groupId<4)
			$this->forbidden();
		$id = $this->checkNotEmptyAndGetParam('id');
		$dprt = D('departments');

		$data = $dprt->find($id);
		$this->setData('id',$data['id']);
		$this->setData('dprtname',$data['name']);
		$this->setData('dprtnote',$data['note']);
		$this->code = 200;
		$this->finish();
	}

	public function edit(){
		if($this->groupId<4)
			$this->forbidden();
		$id = $this->checkNotEmptyAndGetParam('id');
		$data['name'] =  $this->checkNotEmptyAndGetParam('dprtname');
		$data['note'] = $this->checkSetAndGetParam('dprtnote');
		$dprt = D('departments');
		$dprt->where("id=$id")->save($data);
		$this->successWithEmptyData();
	}
}