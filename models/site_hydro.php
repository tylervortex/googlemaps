<?php
class SiteHydro extends AppModel {

	var $name = 'SiteHydro';
	var $displayField = 'name';
	var $useTable = 'sites_hydros';

	var $belongsTo = array(
		'HydroType' => array(
			'className' => 'HydroType',
			'foreignKey' => 'hydro_type_name',
		),
	);

	var $hasMany = array(
		'DataHydro' => array(
			'className' => 'DataHydro',
			'foreignKey' => 'site_hydro_id',
			'limit' => 1,
			'order' => 'DataHydro.time DESC'
		),
		'SiteHydroSeiom' => array(
			'className' => 'SiteHydroSeiom',
			'foreignKey' => 'site_hydro_id',
			'limit' => 1,
		),
		'SiteHydroKeycurve' => array(
			'className' => 'SiteHydroKeycurve',
			'foreignKey' => 'site_hydro_id',
			'limit' => 1,
		),
		'SiteHydroConstant' => array(
			'className' => 'SiteHydroConstant',
			'foreignKey' => 'site_hydro_id',
			'limit' => 1,
		),
	);

	function afterFind($results) {
		if (empty($results) || !is_array($results)) {
			return $results;
		}

		App::import('Model', 'HydroType');
		$HydroType =& ClassRegistry::init('HydroType');
		
		App::import('Model', 'SiteHydroSeiom');
		$SiteHydroSeiom =& ClassRegistry::init('SiteHydroSeiom');
		
		foreach ($results as $k => $result) {
			if ($result['SiteHydro']['hydro_type_name'] == 'seiom') {
				$seiom = $SiteHydroSeiom->find('first', array('recursive' => -1, 'conditions' => array('id' => $result['SiteHydro']['id'])));
				$results[$k]['SiteHydro']['SiteHydroSeiom'] = $seiom['SiteHydroSeiom'];
			}
			
			$type = $HydroType->find('first', array('recursive' => -1, 'conditions' => array('name' => $result['SiteHydro']['hydro_type_name'])));
			$results[$k]['SiteHydro']['HydroType'] = $type['HydroType'];
		}

		return $results;
	}

}
?>
