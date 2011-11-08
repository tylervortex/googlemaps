<?php
class Segment extends AppModel {

	var $name = 'Segment';
	var $primaryKey = 'name';

	var $hasMany = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'segment_name',
			'dependent' => false,
		)
	);

}
?>
