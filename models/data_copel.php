<?php
class DataCopel extends AppModel {

	var $name = 'DataCopel';
	var $displayField = 'time';
	var $useTable = 'data_copel';

	var $virtualFields = array(
		'time_ts' => 'UNIX_TIMESTAMP(DataCopel.time)',
		'kwh_sum' => 'SUM(DataCopel.kwh)',
		'kw_sum' => 'SUM(DataCopel.kw)',
		'kvarh_sum' => 'SUM(DataCopel.kvarh)',
		'sh' => 'DataCopel.kvarh_type',
	);

	var $belongsTo = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'site_id'
		)
	);

}
?>
