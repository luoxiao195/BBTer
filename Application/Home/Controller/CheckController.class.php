<?php
namespace Home\Controller;
use Think\Controller;
class CheckController  extends BaseController{

	public function checkTimeTable(){
		if($this->groupId<3)
			$this->forbidden();
		$timeUserId=$this->checkNotEmptyAndGetParam('id');
		if ($this->userId==3) {
			$pitch_timetable=D('pitch_timetable');
		    $pitchTimeData=$pitch_timetable->where('userid=$timeUserId')->select();
		    $pitchTimeData['state']=1;
			
		}else{
			$this->AssignOwn('code',204);//不是部长不可以审核课表
		}
		$this->code=200;
		$this->finish();
	}
}
?>