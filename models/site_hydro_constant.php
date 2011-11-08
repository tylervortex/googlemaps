<?php
class SiteHydroConstant extends AppModel {

	var $name = 'SiteHydroConstant';
	var $useTable = 'sites_hydros_constants';

	var $virtualFields = array(
		'begin_format' => 'DATE_FORMAT(begin, "%d/%m/%Y")',
		'end_format' => 'DATE_FORMAT(end, "%d/%m/%Y")',
	);

	var $belongsTo = array(
		'SiteHydro' => array(
			'className' => 'SiteHydro',
			'foreignKey' => 'site_hydro_id'
		),
	);

}
?>
