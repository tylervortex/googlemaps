<?php
class DataMeter extends AppModel {

	var $name = 'DataMeter';
	var $displayField = 'time';
	var $useTable = 'data_meters';

	var $virtualFields = array(
		'date_format_hour' => 'DATE_FORMAT(DATE_ADD(time, INTERVAL 55*60 SECOND), "%d/%m/%Y %H")',
		'time_mon' => 'MONTH(DataMeter.time)',
		'time_year' => 'YEAR(DataMeter.time)',
		'time_day' => 'DAY(DataMeter.time)',
		'time_ts' => 'UNIX_TIMESTAMP(DataMeter.time)',
		'kwh_ger_sum' => 'SUM(DataMeter.kwh_ger)',
		'kvarhind_ger_sum' => 'SUM(DataMeter.kvarhind_ger)',
		'kvarhcap_ger_sum' => 'SUM(DataMeter.kvarhcap_ger)',
		'kvarh_ger_sum' => 'SUM(DataMeter.kvarhind_ger + DataMeter.kvarhcap_con)',
		'kwh_con_sum' => 'SUM(DataMeter.kwh_con)',
		'kvarhind_con_sum' => 'SUM(DataMeter.kvarhind_con)',
		'kvarhcap_con_sum' => 'SUM(DataMeter.kvarhcap_con)',
		'kvarh_con_sum' => 'SUM(DataMeter.kvarhind_con + DataMeter.kvarhcap_ger)',
		'va_avg' => 'AVG(DataMeter.va)',
		'vb_avg' => 'AVG(DataMeter.vb)',
		'vc_avg' => 'AVG(DataMeter.vc)',
		'ia_avg' => 'AVG(DataMeter.ia)',
		'ib_avg' => 'AVG(DataMeter.ib)',
		'ic_avg' => 'AVG(DataMeter.ic)',
		'fp_avg' => 'SUM(kwh_ger-kwh_con)/SQRT(SUM(kwh_ger-kwh_con)*SUM(kwh_ger-kwh_con)+SUM(kvarhind_ger+kvarhcap_con-kvarhind_con-kvarhcap_ger)*SUM(kvarhind_ger+kvarhcap_con-kvarhind_con-kvarhcap_ger))',
		'accounting_gercon' => 'SUM(DataMeter.kwh_ger-DataMeter.kwh_con)',
		'accounting_conger' => 'SUM(DataMeter.kwh_con-DataMeter.kwh_ger)',
		'accounting_ger' => 'SUM(DataMeter.kwh_ger)',
		'accounting_con' => 'SUM(DataMeter.kwh_con)',
	);

	var $belongsTo = array(
		'SiteMeter' => array(
			'className' => 'Site',
			'foreignKey' => 'site_meter_id'
		)
	);

}
?>
