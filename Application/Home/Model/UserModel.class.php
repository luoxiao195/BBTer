<?php

namespace Home\Model;
use Think\Model\RelationModel;

class UserModel extends RelationModel{
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
			
		)


	);
}