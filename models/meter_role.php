<?php
class MeterRole extends AppModel {

	var $name = 'MeterRole';
	var $primaryKey = 'name';

	var $hasMany = array(
		'SiteMeter' => array(
			'className' => 'SiteMeter',
			'foreignKey' => 'meter_role_name',
		)
	);

}
?>
