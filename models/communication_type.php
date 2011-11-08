<?php
class CommunicationType extends AppModel {

	var $name = 'CommunicationType';
	var $primaryKey = 'name';

	var $hasMany = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'communication_type_name',
			'dependent' => false,
		)
	);

}
?>
