<?php
class SiteHydroType extends AppModel {

	var $name = 'SiteHydroTrye';
	var $useTable = 'sites_hydros_types';
	var $primaryKey = 'name';

	var $hasMany = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'hydro_type_name',
			'dependent' => false,
		)
	);

}
?>
