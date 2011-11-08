<?php
class Site extends AppModel {

	var $name = 'Site';

	var $actsAs = array(
		'SoftDeletable' => array('field' => 'deleted', 'field_date' => null),
	);

	var $belongsTo = array(
		'CommunicationType' => array(
			'className' => 'CommunicationType',
			'foreignKey' => 'communication_type_name'
		),
		'Segment' => array(
			'className' => 'Segment',
			'foreignKey' => 'segment_name'
		),
		'GenerationType' => array(
			'className' => 'GenerationType',
			'foreignKey' => 'generation_type_name'
		),
		'DistribuitionCompany' => array(
			'className' => 'DistribuitionCompany',
		),
		'SiteHydroType' => array(
			'className' => 'SiteHydroType',
			'foreignKey' => 'hydro_type_name'
		),
	);

	var $hasOne = array(
		'SiteRouter' => array(
			'className' => 'SiteRouter',
			'foreignKey' => 'site_id'
		),
		'SiteTelespazio' => array(
			'className' => 'SiteTelespazio',
			'foreignKey' => 'site_id'
		),
		'SiteFirewall' => array(
			'className' => 'SiteFirewall',
			'foreignKey' => 'site_id'
		),
		'SiteCcee' => array(
			'className' => 'SiteCcee',
			'foreignKey' => 'site_id'
		),
		'SiteConverter' => array(
			'className' => 'SiteConverter',
			'foreignKey' => 'site_id'
		),
		'SiteTimer' => array(
			'className' => 'SiteTimer',
			'foreignKey' => 'site_id'
		)
	);

	var $hasMany = array(
		'SiteMeter' => array(
			'className' => 'SiteMeter',
			'foreignKey' => 'site_id'
		),
		'SiteHydro' => array(
			'className' => 'SiteHydro',
			'foreignKey' => 'site_id'
		),
		'SiteImage' => array(
			'className' => 'SiteImage',
			'foreignKey' => 'site_id'
		)
	);

	var $hasAndBelongsToMany = array(
		'User' => array(
			'className' => 'User',
		),
	);

	function beforeFind($queryData) {
		if (isset($queryData['recursive']) && $queryData['recursive'] == -1) {
			return $queryData;
		}
		$user_id = Configure::read('user_id');
		$group_id = Configure::read('group_id');
		# Grupo principal (ID:1) sempre pode listar todos os pontos.
		if ($group_id == 1) {
			if ($queryData['fields'] != 'COUNT(*) AS `count`') {
				$this->bindModel(array('hasOne' => array('SitesUser')), false);
				$queryData['group'][] = 'Site.id';
			}
			return $queryData;
		}
		$this->bindModel(array('hasOne' => array('SitesUser')), false);
		$queryData['conditions']['SitesUser.user_id'] = Configure::read('user_id');
		if ($queryData['fields'] != 'COUNT(*) AS `count`') {
			$queryData['group'][] = 'SitesUser.site_id';
		}
		return $queryData;
	}

	function afterFind($results) {
		if (isset($results[0][0]['count']) && isset($results[1][0]['count'])) {
			$results[0][0]['count'] = count($results);
			return $results;
		}

		if (empty($results) || !is_array($results)) {
			return $results;
		}

		if (!function_exists('site_meter_weight_cmp')) {
			function site_meter_weight_cmp($a, $b) {
				if ($a['weight'] == $b['weight']) {
					return 0;
				}
				return ($a['weight'] < $b['weight']) ? -1 : 1;
			}
		}

		App::import('Model', 'MeterRole');
		$MeterRole =& ClassRegistry::init('MeterRole');
		$meterRoleWeights = $MeterRole->find('list', array('fields' => array('name', 'weight'), 'order' => 'weight'));

		foreach ($results as $k => $result) {
			if (!isset($result['SiteMeter']) || !is_array($result['SiteMeter'])) {
				continue;
			}
			foreach ($result['SiteMeter'] as $i => $meter) {
				if (isset($meter['meter_role_name'])) {
					$results[$k]['SiteMeter'][$i]['weight'] = $meterRoleWeights[$meter['meter_role_name']];
				}
			}
			usort($results[$k]['SiteMeter'], 'site_meter_weight_cmp');
		}

		return $results;
	}

	function beforeSave() {
		$convertToNumberFields = array('generation_capacity', 'consumption_capacity', 'voltage_level');

		foreach ($convertToNumberFields as $field) {
			if (isset($this->data['Site'][$field])) {
				$v = $this->data['Site'][$field];
				$v = str_replace('.', '', $v);
				$v = str_replace(',', '.', $v);
				$this->data['Site'][$field] = $v;
			}
		}

		return true;
	}

}
?>
