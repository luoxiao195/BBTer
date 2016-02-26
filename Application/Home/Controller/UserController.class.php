<?php
namespace Home\Controller;
use Think\Controller;
class UserController  extends BaseController{
	private function transferParamToUserData(){
		$array = array();
		$array['name'] = $this->checkNotEmptyAndGetParam('username');
		switch ($this->checkNotEmptyAndGetParam('gender')) {
			case '男':
				$array['sex'] = 'MALE';
				break;
			case '女':
				$array['sex'] = 'FEMALE';
				break;
			case '':
				$array['sex'] = 'UNKNOWN';
				break;
			default:
				break;

		}
		$array['college'] = $this->checkNotEmptyAndGetParam('school');
		$tmp = explode('-', $this->checkNotEmptyAndGetParam('room'));
		if(count($tmp) != 2)
			$this->dataFormatWrong('room');
		$array['dormitory'] = $tmp[0];
		$array['room'] = $tmp[1];
		$array['mobile'] = $this->checkNotEmptyAndGetParam('telLong');
		$array['short_mobile'] = $this->checkSetAndGetParam('telShort');
		$array['email'] = $this->checkNotEmptyAndGetParam('email');
		return $array;

	}
	
	private function otherSetData($key,$value){
		if($value === false){
			$this->data['content'] = $value;
			return;
		}
		$value = $this->transferNullToEmpty($value);
		$this->data['content'][$key] = $value;
	}
	private function getInfo($id,$grpAuth = true,$others){
		$realSetData;
		if($others)
			$realSetData = 'otherSetData';
		else
			$realSetData = 'setData';

		$user = D('users');
		$department = D('departments');
		$group = D('groups');
		$userData = $user->find($id);
		if($this->departmentId != $userData['department_id'] && $grpAuth == false)
			$this->forbidden(true);
		$dprtData = $department->find($userData['department_id']);
		$groupData = $group->find($userData['group_id']);
		$this->$realSetData('username',$userData['name']);
		$this->$realSetData('position',$groupData['name']);
		$this->$realSetData('dprt',$dprtData['name']);
		$gender;
		switch ($userData['sex']) {
			case 'FEMALE':
				$gender = '女';	
				break;
			case 'MALE':
				$gender = '男';
				break;
			case 'UNKNOWN':
				$gender = '';
				break;
			default:
				break;
		}
		$this->$realSetData('gender',$gender);
		$this->$realSetData('school',$userData['college']);
		$room = $userData['dormitory'].'-'.$userData['room'];
		if($room == "-")
			$room = '';
		$this->$realSetData('room',$room);
		$this->$realSetData('telLong',$userData['mobile']);
		$this->$realSetData('telShort',$userData['short_mobile']);
		$this->$realSetData('email',$userData['email']);
		$this->$realSetData('photo','');
		$this->$realSetData('studentno',$userData['student_number']);
		$this->code = 200;
		$this->finish();

		
	}

	public function info(){
		$this->getInfo($this->userId);
	}

	public function othersInfo(){
		$grpAuth = true;
		if($this->groupId <= 1) 
			$grpAuth = false;
		$this->setData('editor',$this->groupId == 5,false);
		$id = $this->getParam('id');
		$this->getInfo($id,$grpAuth,true);
	}

	public function infoEdit(){
		$userData  = $this->transferParamToUserData();
		$userData['completed'] = 1;
		$user = D('users');
		$user->where("id = $this->userId")->save($userData);
		$completed = array('completed'=>1);
		$user->where("id = $this->userId")->save($completed);
		$this->successWithEmptyData();
	}
	public function groupChange(){
		if($this->groupId < 3)
			$this->forbidden();
		$userArray = explode(",", $this->checkNotEmptyAndGetParam('id'));
		$groupName = $this->checkNotEmptyAndGetParam('position');
		$group = D('groups');
		$user = D('users');
		$RelationUser = D('User');
		$groupData = $group->where("name = '$groupName'")->find();
		$groupId = $groupData['id'];
		//先判断合法性
		//1.先判断是否只有修改一个
		if(count($userArray) == 1){
			if($this->userId == $userArray[0]){
				$this->code = 108;
				$this->info = '不能操作自己';
				$this->finish();
			}
			$data = $user->find($userArray[0]);
			if(empty($data)){
				$this->dataFormatWrong('id');
			}
			//若不同部门且不为超级管理员
			if($data['department_id'] != $this->departmentId && $this->groupId != 5){
				if($groupId > $this->groupId){
					$this->forbidden();
				}
			}

			$id = $userArray[0];
			$update['group_id'] = $groupId;
			$user->where("id = $id")->save($update);
			$this->successWithEmptyData();
		}
		//修改多个,必须同部门
		foreach ($userArray as $key => $value) {
			# code...
			// $userData = array();
			// $userData['group_id'] = $groupId;
			// $user->where("id = $value")->save($userData);
			if($value == $this->userId){
				$this->code = 108;
				$this->info = '不能操作自己';
				$this->finish();
			}
			$data = $user->find($value);
			if($data['department_id'] != $this->departmentId)
				$this->forbidden();

		}
		foreach ($userArray as $key => $value) {
			# code...
			$userData = array();
			$userData['group_id'] = $groupId;
			$user->where("id = $value")->save($userData);
		}
		$this->successWithEmptyData();
	}

	public function batchAdd(){
		$userArray = explode(".", $this->checkNotEmptyAndGetParam('studentno'));
		$groupName = $this->checkNotEmptyAndGetParam('position');
		$group = D('groups');
		$user = D('users');
		$groupData = $group->where("name = '$groupName'")->find();
		$groupId = $groupData['id'];
		$failed = false;
		$failedArray = array();
		foreach ($userArray as $key => $value) {
			# code...
			$pattern = '/^20[0-9]{10}$/';
			
			if(!preg_match($pattern,$value)){
				$failed = true;
				array_push($failedArray, $value);
				continue;
			}
			$checkData = $user->where("student_number = $value")->select();
			if(!empty($checkData)){
				$failed = true;
				array_push($failedArray, $value);
				continue;
			}
			$userData = array();
			$userData['group_id'] = $groupId;
			$userData['department_id'] = $this->departmentId;
			$userData['student_number'] = $value;
			$userData['password'] = md5($value);
			$userid = $user->data($userData)->add();
			$pitch_user = D('pitch_user');
			$data['pitchTimes'] = 0;
			$data['userId'] = $userid;
			$puId = $pitch_user->data($data)->add();
			$pitch_timetable = D('pitch_timetable');
			$tableData['table'] = 0;
			$tableData['newTable'] = 0;
			$tableData['userid'] = $userid;
			$tableData['state'] = 1;
			$ttid = $pitch_timetable->data($tableData)->add();
			$newdata['timeTableId'] = $ttid;
			$pitch_user->where("id = $puId")->save($newdata);


		}
		if(!$failed)
			$this->successWithEmptyData();
		else{
			$this->code = 202;
			$this->setData('failed',$failedArray);
			$this->finish();
		}
	}

	public function getUnCompleted(){
		$User = D('User');
		$condition = array();
		$completed = array();
		$condition['department_id'] = $this->departmentId;
		$condition['completed'] = 0;
		$userDatas  = $User->relation(true)->where($condition)->select();
		foreach ($userDatas as $key => $user) {
			# code...
			$tmp = array(
				'studentno'=>$user['student_number'],
				'position'=>$user['Group']['name']
			);
			array_push($completed, $tmp);

		}
		$this->setData('uncompleteds',$completed);
		$this->code = 200;
		$this->finish();	
	}

	public function rcl(){
		if($this->groupId<3)
			$this->forbidden();
		$user = D('users');
		$rcl = D('recycles');
		$userArray = explode(",", $this->checkNotEmptyAndGetParam('id'));
		$note = $this->checkNotEmptyAndGetParam('note');
		$status;
		switch ($note) {
			case '退休':
				# code...
				$status = 'RETIRED'; 
				break;
			case '离职':
				$status = 'LEFT';
				break;
			
			default:
				# code...
				$this->dataFormatWrong('note');
				break;
		}
		foreach ($userArray as $key => $value) {
			# code...
			// $userData = array();
			// $userData['group_id'] = $groupId;
			// $user->where("id = $value")->save($userData);
			if($value == $this->userId){
				$this->code = 108;
				$this->info = '不能操作自己';
				$this->finish();
			}
			$data = $user->find($value);
			if($data['department_id'] != $this->departmentId)
				$this->forbidden();
			if($this->groupId<$data['group_id']){
				$this->forbidden();
			}

		}
		foreach ($userArray as $key => $value) {
			# code...
			$userData = array();
			$userData['status'] = $status;
			$user->where("id = $value")->save($userData);
			$rclData = array();
			$rclData['user_id'] = $value;
			$rclData['time'] = date('y-m-d H:i:s',time());
			$rcl->data($rclData)->add();


		}
		$this->successWithEmptyData();
	}

	public function recover(){
		if($this->groupId<3){
			$this->forbidden();
		}
		$user = D('users');
		$rcl = D('recycles');
		$userArray = explode(",", $this->checkNotEmptyAndGetParam('id'));
		foreach ($userArray as $key => $value) {
			# code...
			$rcl->where("user_id = $value")->delete();
			$userData = array();
			$userData['status'] = 'NORMAL';
			$user->where("id = $value")->save($userData);


		}
		$this->successWithEmptyData();
	}

	public function del(){
		if($this->groupId < 3)
			$this->forbidden();
		$idString = $this->checkNotEmptyAndGetParam('id');
		$userArray = explode(",", $idString);
		$user = D('users');
		foreach ($$userArray as $key => $value) {
			# code...
			$data = $user->find($value);
			if($data['department_id'] != $this->departmentId){
				$this->forbidden();
			}
			if($data['group_id'] != $this->groupId)
				$this->forbidden();

		}
		$user->delete($idString);
		$this->successWithEmptyData();
		
	}

	public function pwEdit(){
		$old = $this->getParam('old');
		$new = $this->getParam('new');
		$cfrm = $this->getParam('cfrm');
		$user = D('users');
		$userData = $user->find($this->userId);
		if($old == $new){
			$this->code = 105;
			$this->info = '新旧密码重复';
			$this->finish();
		}
		if($userData['password'] != $old){
			$this->code = 107;
			$this->info = '旧密码错误';
			$this->finish();
		}
		if($new != $cfrm){
			$this->code = 106;
			$this->info = '重复密码不一致';
			$this->finish();
		}
		$userData['password'] = $new;
		$user->where("id = $this->userId")->save($userData);
		$this->token = '';
		$this->successWithEmptyData();
	}
	public function pwApply(){
		$userArray = explode(",", $this->checkNotEmptyAndGetParam('id'));
		$User = D('User');
		$users = array();
		foreach ($userArray as $key => $value) {
			# code...
			// $userData = array();
			// $userData['group_id'] = $groupId;
			// $user->where("id = $value")->save($userData);
			if($value == $this->userId){
				$this->code = 108;
				$this->info = '不能操作自己';
				$this->finish();
			}
			$data = $User->find($value);
			if($data['department_id'] != $this->departmentId)
				$this->forbidden(true);
			if($data['group_id']>$this->groupId)
				$this->forbidden(true);

		}
		foreach ($userArray as $key => $value) {
			# code...
			$data = $User->relation(true)->find($value);
			if($data['department_id'] != $this->departmentId)
			$user['id'] = $this->transferNullToEmpty($data['id']);
			$user['username']= $this->transferNullToEmpty($data['name']);
			$user['position'] = $this->transferNullToEmpty($data['Group']['name']);
			$user['studentno']  = $this->transferNullToEmpty($data['student_number']);
			array_push($users, $user);
		}
		$this->setData('members',$users);
		$this->code = 200;
		$this->finish();

	}
	public function pwReset(){
		$userArray = explode(',', $this->checkNotEmptyAndGetParam('id'));
		//验证
		$password = $this->checkNotEmptyAndGetParam('pw');
		$pwcfrm = $this->checkNotEmptyAndGetParam('pwcfrm');
		if($password != $pwcfrm){
			$this->code = 106;
			$this->info = '重复密码不一致';
			$this->finish();
		}
		$User = D('users');

		$userData['password'] = $password;
		foreach ($userArray as $key => $value) {
			# code...
			// $userData = array();
			// $userData['group_id'] = $groupId;
			// $user->where("id = $value")->save($userData);
			if($value == $this->userId){
				$this->code = 108;
				$this->info = '不能操作自己';
				$this->finish();
			}
			$data = $User->find($value);
			if($data['department_id'] != $this->departmentId)
				$this->forbidden();
			if($data['group_id']>$this->groupId)
				$this->forbidden();

		}
		foreach ($userArray as $key => $value) {
			# code...
			$User->where("id = $value")->save($userData);
		}
		$this->successWithEmptyData();
	}
	public function logout(){

	}

	public function listall(){
		$page = $this->checkNotEmptyAndGetParam('current');
		$pageSize = $this->checkNotEmptyAndGetParam('count');
		$filter = $this->checkNotEmptyAndGetParam('filter');
		//应返回数组
		$User = D('User');
		$kwquery = array();
		$mainQuery = "SELECT `users`.*,`departments`.`name` AS 'dprtname',`groups`.`name` AS 'grpname' ";
		$countQuery = "SELECT count(*) AS cnt ";
		$query = "FROM `departments`,`users`,`groups` WHERE  `users`.`department_id` = `departments`.`id` AND `users`.`group_id` = `groups`.`id` AND `users`.`completed`=1 AND `groups`.`id` != 5  AND `status`='NORMAL'";
		$needAnd = true;

		if(!empty($filter['dprt'])){
			if($needAnd)
				$query.=' AND ';
			$query.='`departments`.`name` = '.'\''.$filter['dprt'].'\'';
		}
		if(!empty($filter['position'])){
			if($needAnd)
				$query.='AND';
			$query.='`groups`.`name` = '.'\''.$filter['position'].'\'';
		}
		if(!empty($filter['keyword'])){
			if($needAnd)
				$query.= 'AND';
			$keyword = '%'.$filter['keyword'].'%';
			$subQuery = "(`users`.`name` like '".$keyword."' OR `users`.`student_number` like '".$keyword."' OR`users`.`mobile` like '".$keyword."' OR `users`.`short_mobile` like '".$keyword."')";
			$query.=$subQuery;

		}




		// $subQuery = $User->table('bbter.users,groups,departments')->where($dgquery)->field("users.*")->select(false);
		// $kwquery = array();
		// if(!empty($filter['dprt'])){
		// 	// $condition['Dept'] = array(
		// 	// 	'name'=>$filter['dprt']
		// 	// );
		// 	$User->join("LEFT JOIN departments ON users.id = departments.id AND departments.name = '$filter[dprt]'");
		// }
		// if(!empty($filter['position'])){
		// 	// $condition['Group'] = array(
		// 	// 	'name'=>$filter['position']
		// 	// );
		// 	$User->join("groups ON users.id = groups.id AND groups.name = '$filter[position]'");
		// }
		// if(!empty($filter['keyword'])){
		// 	$keyword = '%'.$filter['keyword'].'%';
		// 	$kwquery['name'] = array('like',$keyword);
		// 	$kwquery['student_number'] = array('like',$keyword);;
		// 	$kwquery['mobile'] = array('like',$keyword);
		// 	$kwquery['short_mobile'] = array('like',$keyword);
		// 	$kwquery['_logic'] = 'OR';
			
		// }
		 $page--;
		 $countQuery.=$query;
		 $page = $page*$pageSize;
		 $query.="ORDER BY group_id desc LIMIT $page,$pageSize";
		 $mainQuery.=$query;
		 // echo $query;
		 // die();
		 $datas = $User->query($mainQuery);
		// $datas = $User->table($subQuery)->relation(true)->where($kwquery)->limit("$page,$pageSize")->field($fieldarray)->select();
		// echo $User->getLastSql();
		// die();
		 $countD = $User->query($countQuery);
		 $total = $countD[0]['cnt'];
		$totalPage = (int)($total/$pageSize);
		if($totalPage*$pageSize<$total)
			$totalPage++;
		if($totalPage == 0)
			$totalPage++;
		$this->code = 200;
		$this->setData('total',$totalPage);
		$users = array();
		foreach ($datas as $key => $user) {
			# code...
			$data = array();
			$data['id'] = $this->transferNullToEmpty($user['id']);
			$data['username'] = $this->transferNullToEmpty($user['name']);
			$data['position'] = $this->transferNullToEmpty($user['grpname']);
			$data['dprt'] = $this->transferNullToEmpty($user['dprtname']);
			$data['telLong'] = $this->transferNullToEmpty($user['mobile']);
			$data['telShort'] = $this->transferNullToEmpty($user['short_mobile']);
			$data['photo'] = $this->transferNullToEmpty($user['image']);
			array_push($users, $data);
		}
		$this->setData('members',$users);
		$listdetail = false;
		if($this->groupId > 1)
			$listdetail = true;
		$this->data['listdetail'] = $listdetail;
		 $this->finish();





	}

	public function dprtall(){
		$this->code = 200;
		$departmentId = $this->departmentId;
		$User = D('User');
		$where = array();
		$where['department_id'] = $departmentId;
		$where['status'] = 'NORMAL';
		$datas = $User->where($where)->order('group_id desc')->relation(true)->select();
		$users = array();
		foreach ($datas as $key => $user) {
			# code...
			$data = array();
			$data['id'] = $this->transferNullToEmpty($user['id']);
			$data['username'] = $this->transferNullToEmpty($user['name']);
			$data['position'] = $this->transferNullToEmpty($user['Group']['name']);
			$data['studentno'] = $this->transferNullToEmpty($user['student_number']);
			$data['photo'] = $this->transferNullToEmpty($user['image']);
			array_push($users, $data);
		}
		$this->setData('members',$users);
		$editor = false;
		if($this->groupId >=3)
			$editor = true;
		$this->data['editor'] = $editor;
		$this->finish();
	}

	public function listRcl(){
		if($this->groupId <3)
			$this->forbidden(true);
		$page = $this->checkNotEmptyAndGetParam('current');
		$pageSize = $this->checkNotEmptyAndGetParam('count');

		$this->code = 200;
		$departmentId = $this->departmentId;
		$User = D('users');

		// $where = array();
		$where['department_id'] = $departmentId;
		$where['status'] = array('neq','NORMAL');
		$total = $User->where($where)->count();
		$totalPage = (int)($total/$pageSize);
		if($totalPage*$pageSize<$total)
		 	$totalPage++;
		 if($totalPage == 0)
		 	$totalPage = 1;
		$this->setData('total',$totalPage,0);
		$datas = $User->field('u.*,r.id rid,g.name gname')->table('users u,recycles r,groups g')->where("u.id = r.user_id AND u.group_id = g.id AND u.department_id=$departmentId AND (u.status = 'RETIRED' OR u.status='LEFT')")->page("$page,$pageSize")->order('r.id desc')->select(); 
		 // $page = $pageSize*($page-1);
		 // $datas = $User->where($where)->limit("$page,$pageSize")->order('id desc')->relation(true)->select();
		$users = array();
		foreach ($datas as $key => $user) {
			# code...
			$data = array();
			$data['id'] = $this->transferNullToEmpty($user['id']);
			$data['username'] = $this->transferNullToEmpty($user['name']);
			$data['position'] = $this->transferNullToEmpty($user['gname']);
			 $note;
			 if($user['status'] == 'RETIRED')
				$note = "退休";
			else
				$note = "离职";	
			$data['photo'] = $this->transferNullToEmpty($user['image']);
			$data['note'] = $note;
			array_push($users, $data);
		}
		$this->setData('members',$users);
		$this->finish();
		
	}

}

?>