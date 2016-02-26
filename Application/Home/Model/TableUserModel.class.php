<?php
namespace Home\Model;
use Think\Model\RelationModel;

class TableUserModel extends RelationModel{
	protected $tableName = 'pitch_timetable';
	protected $_link = array(
		'PUI'=>array(
			'mapping_type' => self::BELONGS_TO,
			'class_name'  => 'PUI',
			'foreign_key'  => 'userid',
			
		), 
	);
	public function TableState($state){
		$this->_link['Table']['condition'] = "state = $state";
	}
}
?>