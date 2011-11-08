<?php
class DataHydro extends AppModel {

	var $name = 'DataHydro';
	var $displayField = 'time';
	var $useTable = 'data_hydros';

	var $virtualFields = array(
		'date_format_hour' => 'DATE_FORMAT(DATE_ADD(time, INTERVAL 55*60 SECOND), "%d/%m/%Y %H")',
		'time_mon' => 'MONTH(DataHydro.time)',
		'time_year' => 'YEAR(DataHydro.time)',
		'time_day' => 'DAY(DataHydro.time)',
		'time_hour' => 'HOUR(DataHydro.time)',
		'time_ts' => 'UNIX_TIMESTAMP(DataHydro.time)',
		'rainfall_sum' => 'SUM(DataHydro.rainfall)',
		'stage_avg' => 'AVG(DataHydro.stage)',
		'stage_min' => 'MIN(DataHydro.stage)',
		'stage_max' => 'MAX(DataHydro.stage)',
		'flow_avg' => 'AVG(DataHydro.flow)',
		'flow_min' => 'MIN(DataHydro.flow)',
		'flow_max' => 'MAX(DataHydro.flow)',
	);

	var $belongsTo = array(
		'SiteHydro' => array(
			'className' => 'SiteHydro',
			'foreignKey' => 'site_hydro_id'
		)
	);

}
?>
