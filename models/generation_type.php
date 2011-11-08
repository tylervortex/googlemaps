<?php
class GenerationType extends AppModel {

	var $name = 'GenerationType';
	var $primaryKey = 'name';

	var $hasMany = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'generation_type_name',
			'dependent' => false,
		)
	);

}
?>
