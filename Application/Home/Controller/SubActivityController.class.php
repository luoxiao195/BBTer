<?php
namespace Home\Controller;
use Think\Controller;
class SubActivityController extends BaseController{
public function addactivity(){
	$dprt = D('departments');
	$rl_id = $dprt->where("name = '人力资源部'")->find();
	$rl_id  = $rl_id['id'];
	if($this->departmentId != $rl_id && $this->groupId != 5)
		$this->forbidden();
	$sActivity = D('pitch_sub_activity');
	$data = array();
	$data['activity_id'] = $this->checkNotEmptyAndGetParam('id');
	$date = $this->checkNotEmptyAndGetParam('date');
	$date = explode(" ", $date);
	$data['date'] = $date[0];

	$data['place'] = $this->checkNotEmptyAndGetParam('place');
	switch ($this->checkNotEmptyAndGetParam('time')) {
		case '一二节':
			$data['lession'] = '1';
			break;
		case '三四节':
			$data['lession'] = '2';
			break;
		case '五六节':
			$data['lession'] = '3';
			break;
		case '七节':
			$data['lession'] = '4';
			break;
		case '八节':
			$data['lession'] = '5';
			break;
		case '晚上':
			$data['lession'] = '6';
			break;
		default:
			break;
		}
	$scnumber = $this->checkNotEmptyAndGetParam('commander');
	$User = D('users');
	$ud = $User->where("student_number=$scnumber")->find();
	if(empty($ud)){
		$this->code = 109;
		$this->finish();
	}
	$data['header'] = $ud['id'];
	$data['needNumber'] = $this->checkNotEmptyAndGetParam('neednumber');
	$dprtString = $this->checkSetAndGetParam('needdepartment');
	$idArray = array();
	if(!empty($dprtString)){
		$dprtArray = explode(" ", $dprtString);
		foreach ($dprtArray as $key => $dprtname) {
			# code...
			if($key == 0)
				continue;
			$dprt = D('departments');
			$dprtData = $dprt->where("name = '$dprtname'")->find();
			array_push($idArray,$dprtData['id']);
		}
		$data['needDepartmentId'] = json_encode($idArray);
	}else{
		$data['needDepartmentId'] = json_encode($idArray);
	}
	
	$data['boyfirst'] = (int)$this->checkSetAndGetParam('boy');
	// $datearr = explode("-",$data['date']);  
	// $year = $datearr[0]; 
	// $hour = $minute = $second = 0; 
	// $dayofweek = mktime($hour,$minute,$second,$month,$day,$year);   
	// $printdate = date("w",$dayofweek); 
	$data['day'] = transferDaytoInt($date[1]); 
	$sActivity->data($data)->add();
	$this->successWithEmptyData();
}
public function getSubactivity(){
	$dprt = D('departments');
	$rl_id = $dprt->where("name = '人力资源部'")->find();
	$rl_id  = $rl_id['id'];
	if($this->departmentId != $rl_id && $this->groupId != 5)
		$this->forbidden();
	$id = $this->checkNotEmptyAndGetParam('id');
	$sActivity = D('pitch_sub_activity');
	$sAdata = $sActivity->find($id);
	$this->setData('date',$sAdata['date']);
	// switch ($sAdata['place']) {
	// 		case '1':
	// 			$place = '一饭';	
	// 			break;
	// 		case '2':
	// 			$place = '二饭';
	// 			break;
	// 		case '0':
	// 			$place = '';
	// 			break;
	// 		default:
	// 			break;
	// 		}
	$this->setData('place',$sAdata['place']);
	$lession = transferLessionToText($sAdata['lession']);
	$day = transferDayToText($sAdata['day']);
		// switch ($sAdata['day']) {
		// 	case '0':
		// 		$lesson = '周日';	
		// 		break;
		// 	case '1':
		// 		$lesson = '周一';
		// 		break;
		// 	case '2':
		// 		$lesson = '周二';
		// 		break;
		// 	case '3':
		// 		$lesson = '周三';
		// 	case '4':
		// 		$lesson = '周四';
		// 		break;
		// 	case '5':
		// 		$lesson = '周五';
		// 		break;
		// 	case '6':
		// 		$lesson = '周六';
		// 	default:
		// 		break;
		// 	}
	$this->setData('day',$day);
	$this->setData('time',$lession);
	$this->setData('neednumber',$sAdata['neednumber']);
	$this->setData('boy',$sAdata['boyfirst']);
	$this->setData('needdepartment',$sAdata['needdepartmentid']);
	$studentid = (int)$sAdata['header'];
	$user = D('users');
	$userData = $user->where->find($studentid);
	$this->setData('commander',$userData['name']);
	$this->code = 200;
	$this->finish();
}
public function editactivity(){
	// $sActivity = D('pitch_sub_activity');
	// $data = array();
	// $data['date'] = $this->checkNotEmptyAndGetParam('date');
	// switch ($this->checkNotEmptyAndGetParam('place')) {
	// 	case '一饭':
	// 		$data['place'] = '1';
	// 		break;
	// 	case '二饭':
	// 		$data['place'] = '2';
	// 		break;
	// 	case '':
	// 		break;
	// 	default:
	// 		$data['place'] = '0';
	// 		break;
	// 	}
	// switch ($this->checkNotEmptyAndGetParam('lesson')) {
	// 	case '一二节':
	// 		$data['lesson'] = '1';
	// 		break;
	// 	case '三四节':
	// 		$data['lesson'] = '2';
	// 		break;
	// 	case '五六节':
	// 		$data['lesson'] = '3';
	// 		break;
	// 	case '七八节':
	// 		$data['lesson'] = '4';
	// 	default:
	// 		$data['lesson'] = '0';
	// 		break;
	// 	}
	// $data['header'] = $this->checkNotEmptyAndGetParam('header');
	// $data['needNumber'] = $this->checkNotEmptyAndGetParam('needNumber');
	// $data['needDepartmentId'] = $this->checkNotEmptyAndGetParam('needDepartmentId');
	// $data['boyfirst'] = $this->checkNotEmptyAndGetParam('boyfirst');
	// $datearr = explode("-",$data['date']);  
	// $year = $datearr[0];      
	// $month = sprintf('%02d',$datearr[1]); 
	// $day = sprintf('%02d',$datearr[2]); 
	// $hour = $minute = $second = 0; 
	// $dayofweek = mktime($hour,$minute,$second,$month,$day,$year);   
	// $printdate = date("w",$dayofweek);  
	// $data['day'] = $printdate; 
	// $sActivity->where("id=$id")->sava($data);
	// $this->successWithEmptyData();
	$dprt = D('departments');
	$rl_id = $dprt->where("name = '人力资源部'")->find();
	$rl_id  = $rl_id['id'];
	if($this->departmentId != $rl_id && $this->groupId!=5)
		$this->forbidden();
	$sActivity = D('pitch_sub_activity');
	$pa = D('pitch_activity');
	$data = array();
	$id = $this->checkNotEmptyAndGetParam('id');
	$sd = $sActivity->find($id);
	$this->setData('activityid',$sd['acitivity_id']);	
	$date = $this->checkNotEmptyAndGetParam('date');
	$date = explode(" ", $date);
	$data['date'] = $date[0];
	$data['day'] = $date[1];
	$data['place'] = $this->checkNotEmptyAndGetParam('place');
	switch ($this->checkNotEmptyAndGetParam('time')) {
		case '一二节':
			$data['lession'] = '1';
			break;
		case '三四节':
			$data['lession'] = '2';
			break;
		case '五六节':
			$data['lession'] = '3';
			break;
		case '七节':
			$data['lession'] = '4';
			break;
		case '八节':
			$data['lession'] = '5';
			break;
		case '晚上':
			$data['lession'] = '6';
			break;
		default:
			break;
		}
	$scnumber = $this->checkNotEmptyAndGetParam('commander');
	$User = D('users');
	$ud = $User->where("student_number=$scnumber")->find();
	$data['header'] = $ud['id'];
	$data['needNumber'] = $this->checkNotEmptyAndGetParam('neednumber');
	$dprtString = $this->checkNotEmptyAndGetParam('needdepartment');
	$dprtArray = explode(" ", $dprtString);
	$idArray = array();
	$i = 0;
	foreach ($dprtArray as $key => $dprtname) {
		# code...
		if($key == 0)
			continue;
		$dprt = D('departments');
		$dprtData = $dprt->where("name = '$dprtname'")->find();
		array_push($idArray,$dprtData['id']);
	}
	$data['needDepartmentId'] = json_encode($idArray);
	$data['boyFirst'] = (int)$this->checkSetAndGetParam('boy');
	// $datearr = explode("-",$data['date']);  
	// $year = $datearr[0]; 
	// $hour = $minute = $second = 0; 
	// $dayofweek = mktime($hour,$minute,$second,$month,$day,$year);   
	// $printdate = date("w",$dayofweek);  
	$data['day'] = transferDaytoInt($data['day']); 
	$sActivity->where("id = $id")->save($data);
	$this->successWithEmptyData();
}
public function getpublishsa(){
	$dprt = D('departments');
	$rl_id = $dprt->where("name = '人力资源部'")->find();
	$rl_id  = $rl_id['id'];
	if($this->departmentId != $rl_id && $this->groupId!=5)
		$this->forbidden();
	$id = (int)$this->checkNotEmptyAndGetParam('id');
	$pa = D('pitch_activity');
	$sActivity = D('pitch_sub_activity');
	$sAdata = $sActivity->find($id);
	$this->setData('activityid',$sAdata['activity_id']);
	$this->setData('name',$padata['name']);
	$this->setData('date',$sAdata['date']);
	$place;
	$this->setData('place',$sAdata['place']);
	$lesson;
	$day;
		// switch ($sAdata['lession']) {
		// 	case '1':
		// 		$lesson = '一二节';	
		// 		break;
		// 	case '2':
		// 		$lesson = '三四节';
		// 		break;
		// 	case '3':
		// 		$lesson = '五六节';
		// 		break;
		// 	case '4':
		// 		$lesson = '七八节';
		// 	default:
		// 		break;
		// 	}
	$lesson = transferLessionToText($sAdata['lession']);
		switch ($sAdata['day']) {
			case '7':
				$day = '周日';	
				break;
			case '1':
				$day = '周一';
				break;
			case '2':
				$day = '周二';
				break;
			case '3':
				$day = '周三';
				break;
			case '4':
				$day = '周四';
				break;
			case '5':
				$day = '周五';
				break;
			case '6':
				$day = '周六';
			default:
				break;
			}
	$this->setData('day',$day);
	$this->setData('time',$lesson);
	$this->setData('neednumber',$sAdata['neednumber']);
	$this->setData('boy',(int)$sAdata['boyfirst'],0);
	$dprtIdArray = $sAdata['needdepartmentid'];
	$dprtIdArray = json_decode($dprtIdArray);
	$i = 0;
	$dprts;
	foreach ($dprtIdArray as $key => $dprtid) {
		# code...
		if($i != 0)
			$dprts.=" ";
		$dprt = D('departments');
		$data = $dprt->find($dprtid);
		$dprts.=$data['name'];
		$i++;
	}

	$this->setData('needdepartment',$dprts);
	$studentid = $sAdata['header'];
	$user = D('users');
	$userData = $user->where('student_number' == $studentid)->find();
	$this->setData('commander',$userData['name']);
	$this->setData('commanderId',$userData['student_number']);
	$assignment = D('pitch_assignment');
	$datas = $assignment->field("u.*,grp.name grpname,dprt.name dprtname")->table("users u,departments dprt,groups grp,pitch_assignment pas")->where("u.id = pas.userId AND pas.subActivityId = $id AND u.department_id = dprt.id AND u.group_id = grp.id")->select();
	$list = array();
	foreach ($datas as $key => $usr) {
		# code...
		$data = array();
		$data['name'] = $usr['name'];
		$data['position'] = $usr['grpname'];
		$data['department'] = $usr['dprtname'];
		$data['tel'] = $usr['mobile'];
		$data['shorttel'] = $usr['short_mobile'];
		array_push($list, $data);
	}
	$number = count($list);
	$this->setData('list',$list);
	$this->setData('number',$number);
	$this->code = 200;
	$this->finish();
}
private function pitchassign($pitch){
	$dprt = D('departments');
	$rl_id = $dprt->where("name = '人力资源部'")->find();
	$rl_id  = $rl_id['id'];
	if($this->departmentId != $rl_id && $this->groupId!=5)
		$this->forbidden();
	$ass = D('pitch_assignment');	
	$usr = D('pitch_user');
	$day = $pitch['day'];
	$lession = $pitch['lession'];
	$lessionInWeek = ($lession-1)*7+$day;
	$init = 1;
	$init<<=$lessionInWeek;
	$userData = $ass->field("u.id")->table("users u,departments dprt,pitch_timetable tt,pitch_user pu")->where("(tt.table&$init=0) AND tt.userId=u.id AND u.department_id=dprt.id AND u.id = pu.userid AND u.completed = 1 AND u.status = 'NORMAL'")->order("pu.pitchTimes desc")->limit($pitch['neednumber'])->select();
	foreach ($userData as $key => $value) {
		# code...
		$data['userId'] = $value['id'];
		$data['subActivityId'] = $pitch['id'];
		$ass->data($data)->add();
		$ud = $usr->where("userId = $data[userId]")->find();
		$pt = $ud['pitchtimes'];
		$pt++;
		$d = array();
		$d['pitchTimes'] = $pt;
		$usr->where("id = $value[id]")->save($d);
	}

}
public function submit(){
	$dprt = D('departments');
	$rl_id = $dprt->where("name = '人力资源部'")->find();
	$rl_id  = $rl_id['id'];
	if($this->departmentId != $rl_id && $this->groupId!=5)
		$this->forbidden();
	// $assignment = D('pitch_activity');
	// $data['assigned'] = 1; 
	// $assignment->where("id=$id")->sava($data);
	$psa = D('pitch_sub_activity');
	$pa = D('pitch_activity');
	$id = $this->checkNotEmptyAndGetParam('id');
	$pitchData = $psa->where("activity_id=$id")->select();
	foreach ($pitchData as $key => $pitch) {
		# code...
		$this->pitchassign($pitch);
	}
	$data['assigned'] = 1; 
	$pa->where("id=$id")->save($data);
	$this->successWithEmptyData();

}

public function getSaByAct(){
	$dprt = D('departments');
	$rl_id = $dprt->where("name = '人力资源部'")->find();
	$rl_id  = $rl_id['id'];
	if($this->departmentId != $rl_id && $this->groupId!=5)
		$this->forbidden();
	$id = $this->checkNotEmptyAndGetParam('id');
	$pitch_sub_activity = D('pitch_sub_activity');
	$pitch_activity = D('pitch_activity');
	$actData = $pitch_activity->find($id);
	$saDatas = $pitch_sub_activity->where("activity_id = $id")->select();
	$saCount = $pitch_sub_activity->where("activity_id = $id")->count();
	$list = array();
	foreach ($saDatas as $key => $saData){
		# code...
		$sub = array();
		$sub['subid'] = $saData['id'];
		$sub['subdate'] = $saData['date'];
		$sub['subday'] = transferDayToText($saData['day']);
		$sub['subtime'] = transferLessionToText($saData['lession']);
		$sub['subplace'] = $saData['place'];
		$sub['subnumber'] = $saData['neednumber'];
		$studentid = $saData['header'];
		$user = D('users');
		$userData = $user->find($studentid);
		$sub['subcommander'] = $userData['name'];
		array_push($list,$sub);
	}
	$this->setData('name',$actData['name']);
	$this->setData('date',$actData['date']);
	$status;
	switch ($actData['assigned']) {
		case 1:
			# code...
			$status = '已发布';
			break;
		
		case 0:
			# code...
			$status = '未发布';
			break;
		default:
			break;
	}
	$this->setData('status',$status);
	$this->setData('ifconfirm',(int)$actData['assigned'],0);
	$this->setData('description',$actData['detail']);
	$this->setData('list',$list);
	$this->setData('number',(int)$saCount,0);
	$this->code = 200;
	$this->finish();
}
public function rmSa(){
	$dprt = D('departments');
	$rl_id = $dprt->where("name = '人力资源部'")->find();
	$rl_id  = $rl_id['id'];
	if($this->departmentId != $rl_id && $this->groupId!=5)
		$this->forbidden();
	$id = $this->checkNotEmptyAndGetParam('id');
	$psa = D('pitch_sub_activity');
	$psa->delete($id);
	$this->successWithEmptyData();
}
}
?>
