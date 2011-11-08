<?php
class SiteMeterLog extends AppModel {

	var $name = 'SiteMeterLog';
	var $useTable = 'sites_meters_log';

	var $virtualFields = array(
		'time_ts' => 'UNIX_TIMESTAMP(SiteMeterLog.time)',
		'created_ts' => 'UNIX_TIMESTAMP(SiteMeterLog.created)',
		'updated_ts' => 'UNIX_TIMESTAMP(SiteMeterLog.updated)',
	);

	var $validate = array(
	);

	var $belongsTo = array(
		'SiteMeter' => array(
			'className' => 'Site',
			'foreignKey' => 'site_meter_id'
		),
		'LogStatus' => array(
			'className' => 'LogStatus',
			'foreignKey' => 'status_id'
		),
	);

	function afterFind($results) {
		if (empty($results) || !is_array($results)) {
			return $results;
		}

		App::import('Model', 'LogStatus');
		$LogStatus =& ClassRegistry::init('LogStatus');
		$status = $LogStatus->find('list', array('fields' => array('id', 'description')));

		foreach ($results as $k => $result) {
			if (!isset($result['SiteMeterLog'])) {
				continue;
			}
			if (isset($result['SiteMeterLog']['status_id'])) {
				$results[$k]['SiteMeterLog']['description'] = $status[$result['SiteMeterLog']['status_id']];
			}
		}
		return $results;

	}

}
?>
