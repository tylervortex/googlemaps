<?php
class SiteFirewall extends AppModel {

	var $name = 'SiteFirewall';
	var $displayField = 'model';
	var $useTable = 'sites_firewall';

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
