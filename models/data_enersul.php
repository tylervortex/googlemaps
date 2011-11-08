<?php
class DataEnersul extends AppModel {

	var $name = 'DataEnersul';
	var $displayField = 'time';
	var $useTable = 'data_enersul';

	var $virtualFields = array(
		'time_ts' => 'UNIX_TIMESTAMP(DataEnersul.time)',
		'kwh_sum' => 'SUM(DataEnersul.kwh)',
		'kw_sum' => 'SUM(DataEnersul.kw)',
		'kvarh_sum' => 'SUM(DataEnersul.kvarh)',
	);

	var $belongsTo = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'site_id'
		)
	);

}
?>
