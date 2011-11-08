<?php
class SiteTimer extends AppModel {

	var $name = 'SiteTimer';
	var $displayField = 'model';
	var $useTable = 'sites_timers';

	var $validate = array(
	);

	var $belongsTo = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'site_id'
		)
	);

}
?>
