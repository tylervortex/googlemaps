<?php
class LogStatus extends AppModel {

	var $name = 'LogStatus';
	var $useTable = 'log_status';

	var $hasMany = array(
		'SiteMeterLog' => array(
			'className' => 'SiteMeterLog',
			'foreignKey' => 'status_id',
		)
	);

}
?>
