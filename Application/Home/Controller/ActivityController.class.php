<?php
namespace Home\Controller;
use Think\Controller;
class ActivityController  extends BaseController{
	public function obtainActivityList(){
		// $dprt = D('departments');
		// $rl_id = $dprt->where("name = '人力资源部'")->find();
		// $rl_id  = $rl_id['id'];
		// if($this->groupId != $rl_id)
		// 	$this->forbidden();

		$kind=$this->checkNotEmptyAndGetParam('kind');


		$pitch_activity=D('pitch_activity');
		
		switch ($kind) {
			case '全部':
		        	    $kind=0;
			    $activity=$pitch_activity->order('id desc')->select();
		 		break;
		 	case '未发布':
		 	   	$kind=1;
		 		$activity=$pitch_activity->where(array('assigned'=>0))->order('id desc')->select();# code...
		 		break;//array('department_id'=>$departmentUserId)
		 	case '已发布':
		 	    $kind=2;
		 	    $activity=$pitch_activity->where(array('assigned'=>1))->order('id desc')->select();
		 		# code...
		 		break;
			
			default:
			    $this->AssignOwn('code',204);//输入的kind参数不为全部已发布未发布之一
			// 	# code...
				break;
		 }
		 
		 
		foreach ($activity as $key => $value) {
			$actid=$value['id'];
			$actname=$value['name'];
			$acttime=$value['time'];
			$actstatus=$value['assigned'];
			$record=array();
			$record['actid']=$actid;
			$record['actname']=$actname;
			$record['acttime']=$value['date'];
			switch ($actstatus) {
				case 0:
					$record['actstatus'] = "未发布";
					# code...
					break;
				
				case 1:
					$record['actstatus']= '已发布';
					# code...
					break;
				default :
					break;
			}
			// $record['actstatus']=$actstatus;
			//$list=array();
			$list[$key]=$record;
		}

		//补
		$number = $pitch_activity->count();
		 $this->setData('number',$number);
		 $this->setData('list',$list);
		 $this->code=200;
		 $this->finish();
		
	}

	public function addActivity(){
		$dprt = D('departments');
		$rl_id = $dprt->where("name = '人力资源部'")->find();
		$rl_id  = $rl_id['id'];
		if($this->departmentId != $rl_id && $this->groupId != 5)
			$this->forbidden();
		$name = $this->checkNotEmptyAndGetParam("name");
		$description = $this->checkSetAndGetParam('description');
		$activity = D('pitch_activity');
		$data = array();
		$data['name'] = $name;
		$data['detail'] = $description;
		$data['date']=date('Y-m-d',time());
		$activity->data($data)->add();
		$this->successWithEmptyData();
	}

	public function rmActivity(){
		$dprt = D('departments');
		$rl_id = $dprt->where("name = '人力资源部'")->find();
		$rl_id  = $rl_id['id'];
		if($this->departmentId != $rl_id && $this->groupId != 5)
			$this->forbidden();
		$id = $this->checkNotEmptyAndGetParam('id');
		$activity = D('pitch_activity');
		$activity->delete($id);
		$sa = D('pitch_sub_activity');
		$sa->where("activity_id = $id")->delete();
		$this->successWithEmptyData();
	}

	public function editActivity(){
		$dprt = D('departments');
		$rl_id = $dprt->where("name = '人力资源部'")->find();
		$rl_id  = $rl_id['id'];
		if($this->departmentId != $rl_id && $this->groupId != 5)
			$this->forbidden();
		$name = $this->checkNotEmptyAndGetParam("name");
		$id = $this->checkNotEmptyAndGetParam('id');
		$description = $this->checkSetAndGetParam('description');
		$activity = D('pitch_activity');
		$data = array();
		$data['name'] = $name;
		$data['detail'] = $description;
		$activity->where("id = $id")->save($data);
		$this->code = 200;
		$this->successWithEmptyData();
	}


}
?>