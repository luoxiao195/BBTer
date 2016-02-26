<?php
namespace Home\Model;
use Think\Model\RelationModel;

class PUIModel extends RelationModel{
	protected $tableName = 'users';
	protected $_link = array(
		'Dept'=>array(
			'mapping_type' => self::BELONGS_TO,
			'class_name'  => 'departments',
			'foreign_key'  => 'department_id',
			
		), 
		'Group'=>array(
			'mapping_type' => self::BELONGS_TO,
			'class_name'  => 'groups',
			'foreign_key'  => 'group_id',
			
		),
		'PitchInfo'=>array(
			'mapping_type'=>self::HAS_ONE,
			'class_name'=>'pitch_user',
			'foreign_key'=>'userId' 
		),
		'Table'=>array(
			'mapping_type'=>self::HAS_ONE,
			'class_name'=>'pitch_timetable',
			'foreign_key'=>'userid',
		)


	);
	public function TableState($state){
		$this->_link['Table']['condition'] = "state = $state";
	}
}
?>