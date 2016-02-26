<?php

namespace Home\Model;
use Think\Model\RelationModel;

class RecycleModel extends RelationModel{
	protected $tableName = "recycles";

	protected $_link = array(
		'User'=>array(
			'mapping_type' => self::BELONGS_TO,
			'class_name'  => 'User',
			'foreign_key'  => 'user_id'
			
		)


	);
}