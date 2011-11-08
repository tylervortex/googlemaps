<?php
class SiteMeter extends AppModel {

	var $name = 'SiteMeter';
	var $displayField = 'model';
	var $useTable = 'sites_meters';

	var $virtualFields = array(
		'log_status_time_ts' => 'UNIX_TIMESTAMP(SiteMeter.log_status_time)',
	);

	var $belongsTo = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'site_id',
		),
		'MeterRole' => array(
			'className' => 'MeterRole',
			'foreignKey' => 'meter_role_name',
		),
		'Manufacturer' => array(
			'className' => 'Manufacturer',
			'foreignKey' => 'manufacturer_name',
		)
	);

	var $hasMany = array(
		'DataMeter' => array(
			'className' => 'DataMeter',
			'foreignKey' => 'site_meter_id',
			'limit' => 1,
			'order' => 'DataMeter.time DESC'
		),
		'SiteMeterLog' => array(
			'className' => 'SiteMeterLog',
			'foreignKey' => 'site_meter_id',
			'limit' => 1,
			'order' => 'SiteMeterLog.created DESC',
		),
	);

	function beforeSave() {
		$convertToNumberFields = array('user_constant_tp', 'user_constant_tc');

		foreach ($convertToNumberFields as $field) {
			if (isset($this->data['SiteMeter'][$field])) {
				$v = $this->data['SiteMeter'][$field];
				$v = str_replace('.', '', $v);
				$v = str_replace(',', '.', $v);
				$this->data['SiteMeter'][$field] = $v;
			}
		}

		return true;
	}

	/**
	 * Returns the number of rows affected by the last query
	 *
	 * @return int Number of rows
	 * @access public
	 */
	function getAffectedRows() {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		return $db->lastAffected();
	}

}
?>
