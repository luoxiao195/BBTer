<?php
namespace Home\Model;
use Think\Model\RelationModel;
class MessageModel extends RelationModel{
	protected $tableName='bbt_message';
	protected $_link=array(
		'receiver'=>array(
			'mapping_type'=>self::HAS_MANY,
			'class_name'=>'bbter_receiver',
			'foregin_key'=>'msg_Id'
			)
		);

	
}
