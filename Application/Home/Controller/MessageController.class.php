<?php
namespace Home\Controller;
use Think\Controller;
class MessageController extends baseController
{
	protected function showResult($codes,$info='',$data=''){
		$this->AssignOwn('code',$codes);
		$this->AssignOwn('info',$info);
		$this->AssignOwn('data',$data);
		$this->AssignOwn('token',$this->token);
		$this->DisplayOwn();
	}
	public function addMessage()
	{
		$description=I('post.description');
		$title=I('post.title');
		$recipient=I('post.recipient');
		$ifNotice=I('post.ifNotice');
	/*	$description="通知内容";
		$title="通知标题";
		$recipient=array(1,2,4,5);	
		$ifNotice=true;			*/	
		$message=D('message');

		$data=array();
		$data['description']=$description;
		$data['title']=$title;

		$data['type']='NORMAL';
		$data['publisher_id']=$this->userId;
		$data['publish_time']=date('Y-m-d');
		$mesId=$message->add($data);

		$receiver=D('bbter_receiver');

		foreach($recipient as $key => $value)
		{
			$another=array(

				'user_id'=>(int)$value,
				'readed'=>$ifNotice,
				'msg_id'=>$mesId
				);
			$receiver->add($another);
		}
		$this->successWithEmptyData();
	}
	public function checkMessage()
	{
	
		$msg_id=I('post.msg_id');
		$user_id=$this->userId;
		
		//$msg_id=55;
		
		$rec=M('bbter_receiver');
		$rec->readed=1;
		$result=$rec->where("msg_id=$msg_id&&user_id=$user_id")->save();
		if(empty($result))
			$this->forbidden();
		$this->successWithEmptyData();
	}
	public function addTask()
	{
		$description=I('post.description');
		$title=I('post.title');
		$headerId=I('post.headerId');
		$sequence=I('post.sequence');
		$observerId=I('post.observerId');
		$deadLine=I('post.deadLine');
/*
		$description="通知内容";
		$title="通知标题";	

		$headerId=17;
		$sequence=array(0=>"烧开水",1=>"下面条",2=>"放盐");
		$observerId=array(1,2,3,4);
		$deadLine='2016-02-15 20:45:07';*/


		$ifNotice=false;			
		$message=D('message');

		$data=array();
		$data['description']=$description;
		$data['title']=$title;

		$data['type']='TASK';
		$data['publisher_id']=$this->userId;
		$data['publish_time']=date('Y-m-d');
		$mesId=$message->add($data);

		$receiver=D('bbter_receiver');
		$another=array(
				'user_id'=>$headerId,
				'readed'=>$ifNotice,
				'msg_id'=>$mesId
				);
		$receiver->add($another);

		$header=D('bbt_task_other');
		$headerData=array(
			'header'=>$headerId,
			'deadLine'=>$deadLine,
			'msg_id'=>$mesId
			);
		$header->add($headerData);

		$obser=D('bbt_task_observer');

		foreach ($observerId as $key => $value) {
			$obserData=array(
				'task_id'=>$mesId,
				'user_id'=>$value
				);
			$obser->add($obserData);
		}
		$seq=D('bbt_task_sequence');
		foreach ($sequence as $key => $value) {
			$seqData=array(
				'task_id'=>$mesId,
				'content'=>$value,
				'finished'=>false
				);
			$seq->add($seqData);
		}
		$this->successWithEmptyData();
	}
	public function showMyMessage()
	{
		$message=M('bbt_message');
		$return=$message->where("publisher_id=$this->userId && type=\"NORMAL\" ||type=\"TASK\"")->select();
		foreach ($return as $key => $value) {
			$receiver=D('bbter_receiver');
			$r1=$receiver->where('msg_id='.$value['id'])->select();
			if(!empty($r1))
			{
				foreach ($r1 as $k => $v) {
					$return[$key]['recipient'][$k]=$v['user_id'];
				}
			}
			else
				$return[$key]['recipient']=null;
			
			if($value['type']=='TASK')
			{
				$obser=D('bbt_task_observer');
				$obserData=$obser->where('task_id='.$value['id'])->select();
				if(!empty($obserData))
					foreach ($obserData as $o_k => $o_v) {
						$return[$key]['observerId'][$o_k]=$o_v['user_id'];
					}
				else
					$return[$key]['observerId']=null;

				$header=D('bbt_task_other');
				$headerData=$header->where('msg_id='.$value['id'])->find();
				if(!empty($headerData))
				{
					$return[$key]['headerId']=$headerData['header'];
					$return[$key]['deadLine']=$headerData['deadline'];			//转化日期仍然有问题
					if(date('Y-m-d h:m:s')>$headerData['deadline'])
						$return[$key]['ifPass']=false;
					else
						$return[$key]['ifPass']=true;
				}
				else
				{
					$return[$key]['headerId']=null;
					$return[$key]['deadLine']=null;
				}

				$seq=D('bbt_task_sequence');
				$seqData=$seq->where('task_id='.$value['id'])->select();
				foreach ($seqData as $s_k => $s_v) {
					$return[$key]['sequence'][$s_k]=$s_v['content'];
				}
			
			}
		}
		$this->showResult('200','',$return);
	}
	public function checkStep()
	{
		
		$msg_id=I('post.msg_id');
		$sequence=I('post.sequence');
		

		/*$msg_id=60;
		$sequence=array(1,2);*/


		$step=M('bbt_task_sequence');		
		$stepData=$step->where("task_id=$msg_id")->find();
		$tempId=$stepData['id']-1;

		$data=array();

		foreach ($sequence as $key => $value) {
		
			$data['finished']=1;
			$trueId=$tempId+$value;
			$result=$step->where("id=$trueId")->save($data);
			if(!$result)
				$this->serviceError();
		}
		
		$this->successWithEmptyData();
			
	}
	public function sendFeedback()
	{
		$content=I('post.content');
		$user_name=I('post.userName');
		$user_num=I('post.userNum');

		/*$userName='xxx';
		$userNum='2018xxxx';
		$content='sos';*/

		$fb=M('feedback');
		$data=array(
			'user_id'=>$this->userId,
			'user_name'=>$userName,
			'user_par'=>$this->departmentId,
			'user_num'=>$userNum,
			'content'=>$content
			);
		$result=$fb->add($data);

		if($result)
			$this->successWithEmptyData();
		else
			$this->serviceError();
	}
	public function showMessage()
	{
		$return=array();
		$receiver=D('bbter_receiver');
		$recData=$receiver->where("user_id=$this->userId")->select();

		$haveNum=1;
		$haveNOt=1;

		foreach ($recData as $rec_k => $rec_v) {
			if($rec_v['readed']==0)
			{
				$return['unRead'][$haveNot]['msg_id']=$rec_v['msg_id'];

				$message=D('bbt_message');
				$messageData=$message->where('id='.$rec_v['msg_id'])->find();

				if($messageData['type']=='NORMAL')
				{
					$return['unRead'][$haveNot]['ifPass']=false;
					$return['unRead'][$haveNot]['type']='NORMAL';
					$return['unRead'][$haveNot]['publish_time']=$messageData['publish_time'];
					$return['unRead'][$haveNot]['publisher_id']=$messageData['publisher_id'];
					$return['unRead'][$haveNot]['description']=$messageData['description'];
					$return['unRead'][$haveNot]['title']=$messageData['title'];

				}
				else if($messageData['type']=='TASK')
				{
					$return['unRead'][$haveNot]['type']='TASK';
					$return['unRead'][$haveNot]['publish_time']=$messageData['publish_time'];
					$return['unRead'][$haveNot]['publisher_id']=$messageData['publisher_id'];
					$return['unRead'][$haveNot]['description']=$messageData['description'];
					$return['unRead'][$haveNot]['title']=$messageData['title'];


					$header=D('bbt_task_other');
					$headerData=$header->where('msg_id='.$rec_v['msg_id'])->find();
					if(date('Y-m-d h:m:s')>$headerData['deadline'])
						$return['unRead'][$haveNot]['ifPass']=false;
					else
						$return['unRead'][$haveNot]['ifPass']=true;
					$return['unRead'][$haveNot]['headerId']=$headerData['header'];

					$obser=D('bbt_task_observer');
					$obserData=$obser->where('task_id='.$rec_v['msg_id'])->select();
					if(!empty($obserData))
						foreach ($obserData as $obser_key => $obser_value) {
							$return['unRead'][$haveNot]['observerId'][$obser_key]=$obser_value['user_id'];
						}
						else
							$return['unRead'][$haveNot]['observerId']=null;

					$seq=D('bbt_task_sequence');
					$seqData=$seq->where('task_id='.$rec_v['msg_id'])->select();
					if(!empty($seqData))
						foreach ($seqData as $seq_key => $seq_value) {
							$return['unRead'][$haveNot]['sequence'][$seq_key]=$seq_value['content'];
						}
						else
							$return['unRead'][$haveNot]['sequence']=null;

				}
				$haveNot++;
			}
			else
			{
				$return['haveRead'][$haveNum]['msg_id']=$rec_v['msg_id'];

				$message=D('bbt_message');
				$messageData=$message->where('id='.$rec_v['msg_id'])->find();
				if($messageData['type']=='NORMAL')
				{
					$return['haveRead'][$haveNum]['ifPass']=true;
				}
				else if($messageData['type']=='TASK')
				{
					$header=D('bbt_task_other');
					$headerData=$header->where('msg_id='.$rec_v['msg_id'])->find();
					if(date('Y-m-d h:m:s')>$headerData['deadline'])
						$return['haveRead'][$haveNum]['ifPass']=false;
					else
						$return['haveRead'][$haveNum]['ifPass']=true;
				}
				$haveNum++;
			}
		}
		$this->showResult('200','',$return);
	}
	public function getList()
	{
		$return=array();
	
		$user=D('users');
		$userData=$user->select();

		foreach ($userData as $user_key => $user_value) {
			$return[$user_key]['email']=$user_value['email'];

			$detail=D('user_details');

			$detData=$detail->where('user_id='.$user_value['id'])->find();
			$return[$user_key]['name']=$detData['name'];
			$return[$user_key]['sex']=$detData['sex'];
			$return[$user_key]['short_mobile']=$detData['short_mobile'];
			$return[$user_key]['mobile']=$detData['mobile'];
			$return[$user_key]['dormitory']=$detData['dormitory'].'-'.$detData['room'];
			$group=D('groups');
			$groupData=$group->where('id='.$user_value['group_id'])->find();
			$return[$user_key]['position']=$groupData['name'];

			$depa=D('departments');
			$depaData=$depa->where('id='.$user_value['department_id'])->find();
			$return[$user_key]['department']=$depaData['name'];

		}

		$this->showResult('200','',$return);
	}
}