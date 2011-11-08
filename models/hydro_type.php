<?php
class HydroType extends AppModel {

	var $name = 'HydroType';
	var $primaryKey = 'name';

	var $hasMany = array(
		'SiteHydro' => array(
			'className' => 'SiteHydro',
			'foreignKey' => 'hydro_type_name',
			'dependent' => false,
		)
	);

}
?>
