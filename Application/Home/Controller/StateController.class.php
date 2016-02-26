<?php
//摆摊状态
namespace Home\Controller;
use Think\Controller;
import('ORG.Util.Page');
class StateController  extends BaseController{
    public function authJuge(){
    	if ($this->groupId==3) {
    		$this->selectPitchState();
    		# code...
    	}else{
    		$this->forbidden();
    		
    	}
    }
    private function select($state){
	$page = $this->checkSetAndGetParam('pagenow');
	$pageSize = $this->checkNotEmptyAndGetParam('eachpage');

	$this->code = 200;
	$departmentId = $this->departmentId;
	$User = D('users');
	$total = $User->table('users u,pitch_timetable tt')->where("u.department_id = $departmentId AND tt.state = $state AND u.id = tt.userid")->count();
	$totalPage = (int)($total/$pageSize);
	if($totalPage*$pageSize<$total)
	 	$totalPage++;
	 if($totalPage == 0)
	 	$totalPage = 1;
	//$this->setData('total',$totalPage,0);
	$datas = $User->field('u.*,tt.state,pu.pitchTimes,d.name dprtname,g.name grpname')->table('users u,pitch_timetable tt,pitch_user pu,departments d,groups g')->where("u.id = pu.userId AND u.group_id = g.id AND u.department_id=d.id And d.id = $departmentId AND u.id = tt.userid AND (tt.state = $state)")->page("$page,$pageSize")->order('u.id desc')->select(); 
	 // $page = $pageSize*($page-1);
	 // $datas = $User->where($where)->limit("$page,$pageSize")->order('id desc')->relation(true)->select();
	$users = array();
	$cnt = 0;
	foreach ($datas as $key => $user) {
		# code...
		$cnt++;
		$data = array();
		$data['id'] = $user['id'];
		$data['name'] = $user['name'];
		$data['position'] = $user['grpname'];
		$data['pitchnumber'] = $user['pitchtimes'];
		$data['scnumber'] = $user['student_number'];
		$pitchstatus;
		switch ($user['state']) {
			case 0:
				# code...
				$pitchstatus = '未审核';
				break;
			case 1:
				$pitchstatus = '已审核';
				break;
			default:
				# code...
				break;
		}
		$data['pitchstatus'] = $pitchstatus;
		array_push($users, $data);
	}
	$this->setData('pageall',$totalPage);
	// $this->setData('pagenow',$pa,0);
	$this->setData('pageeach',$cnt);
	$this->setData('list',$users);

	$this->code = 200;
	$this->finish();   
 }
public function selectPitchState(){
	if($this->groupId<3)
		$this->forbidden();
	$kind=$this->checkNotEmptyAndGetParam('kind');
	$pagenow=(int)$this->checkSetAndGetParam('pagenow');
	$pageeach=$this->checkNotEmptyAndGetParam('eachpage');
	$users=D('TableUser');
	$departmentUserId=$this->departmentId;
	switch ($kind) {
		case '全部':
			# code...
			$this->select('0 OR tt.state = 1');
			break;
		case '未审核':
			$this->select(0);
			break;
		case '已审核':
			$this->select(1);
			break;
		default:
			# code...
			break;
	}
	// switch ($kind) {
	// 	case '全部':
	// 	    //$kind=0;
	// 	    $count=$users->where(array('department_id'=>$departmentUserId))->count();
	// 		# code...
	// 		break;
	// 	case '未审核':
	// 		$users->TableState(0);
	// 		$count=$users->where(array('department_id'=>$departmentUserId))->count();# code...
	// 		break;
	// 	case '已审核':
	// 		$users->TableState(1);
	// 		$count=$users->where(array('department_id'=>$departmentUserId))->count();# code...
	// 		break;
	// 	default:
	// 		$this->AssignOwn('code',205);# code...
	// 		break;
	// }
	
	// $Page=new\Think\Page($count,$pageeach);

	// if($pageeach!=0){
	// 	$pageall=ceil($count/$pageeach);

	// }else{
	// 	$this->AssignOwn('code',203);//每页的页数不能为0

	// }
	// $userData;
	// switch ($kind) {
	// 	case '全部':
	// 		$userData=$users->relation(true)->where(array('department_id'=>$departmentUserId))->limit($pageeach)->page($pagenow)->select();# code...
	// 		break;
	// 	case '未审核':
	// 		$userData=$users->relation(true)->where(array('department_id'=>$departmentUserId))->limit($pageeach)->page($pagenow)->select();# code...
	// 		break;
	// 	case '已审核':
	// 		$userData=$users->relation(true)->where(array('department_id'=>$departmentUserId))->limit($pageeach)->page($pagenow)->select();# code...
	// 		break;
	// 	default:
	// 		$this->AssignOwn('code',205);# 输入的kind不为全部已审核未审核之一
	// 		break;
	// }
	// // $userData=$users->where(array('department_id'=>$departmentUserId))->limit($pageeach)->page($pagenow)->select();
	// foreach ($userData as $key => $value) {
	// 	$scnumber=$value['student_number'];
	// 	$name=$value['name'];
	// 	$position=$value['Group']['name'];
	// 	$pitchnumber = $value['PitchInfo']['pitchtimes'];
	// 	$pitchstatus = $value['Table']['state'];
	// 	// $pitch_user=D('pitch_user');
	// 	// if (!$pitch_user->where("userId = $value['id']")->find()) {
			

	// 	// 	# code...
	// 	// }
	// 	//$pitchUserData=$pitch_user->where(array('studentNumber'=>$scnumber))->select();
	
	// 	//$pitchUserId=$pitchUserData['userId'];
	// 	// $pitchnumber=$pitchUserData['pitchTimes'];
	// 	// $pitch_timetable=D('pitch_timetable');
	// 	// $pitchTimeData=$pitch_timetable->where(array('userid'=>$pitchUserId))->find();
	// 	// $pitchstatus=$pitchTimeData['state'];
	// 	$record=array();//list中的一条记录
	// 	$record['scnumber']=$scnumber;
	// 	$record['name']=$name;
	// 	$record['positon']=$position;
	// 	$record['pitchnumber']=$pitchnumber;
	// 	$record['pitchstatus']=$pitchstatus;
	// 	//$list=array();
	// 	$list[$key]=$record;


	// }

	// $this->setData('pageall',$pageall);
	// $this->setData('pagenow',$pagenow,0);
	// $this->setData('pageeach',$pageeach);
	// $this->setData('list',$list);

	// $this->code = 200;
	// $this->finish();


	}
}
?>