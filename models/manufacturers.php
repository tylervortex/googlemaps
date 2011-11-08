<?php
class Manufacturer extends AppModel {

	var $name = 'Manufacturer';
	var $primaryKey = 'name';

	var $hasMany = array(
		'SiteMeter' => array(
			'className' => 'SiteMeter',
			'foreignKey' => 'manufacturer_name',
		)
	);

}
?>
