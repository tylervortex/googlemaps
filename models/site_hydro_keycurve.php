<?php
class SiteHydroKeycurve extends AppModel {

	var $name = 'SiteHydroKeycurve';
	var $useTable = 'sites_hydros_keycurve';

	var $virtualFields = array(
		'date_format' => 'DATE_FORMAT(date, "%d/%m/%Y")',
	);

	var $belongsTo = array(
		'SiteHydro' => array(
			'className' => 'SiteHydro',
			'foreignKey' => 'site_hydro_id'
		),
	);

}
?>
