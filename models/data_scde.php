<?php
class DataScde extends AppModel {

	var $name = 'DataScde';
	var $displayField = 'time';
	var $useTable = 'data_scde';

	var $virtualFields = array(
		'date_format_hour' => 'DATE_FORMAT(time, "%d/%m/%Y %H")',
		'time_mon' => 'MONTH(DataScde.time)',
		'time_year' => 'YEAR(DataScde.time)',
		'time_day' => 'DAY(DataScde.time)',
		'time_ts' => 'UNIX_TIMESTAMP(DataScde.time)',
		'kwh_ger_sum' => 'SUM(DataScde.kwh_ger)',
		'kwh_con_sum' => 'SUM(DataScde.kwh_con)',
		'kvarh_ger_sum' => 'SUM(DataScde.kvarh_ger)',
		'kvarh_con_sum' => 'SUM(DataScde.kvarh_con)',
		'missing_intervals_sum' => 'SUM(DataScde.missing_intervals)',
		'accounting_gercon' => 'SUM(DataScde.kwh_ger-DataScde.kwh_con)',
		'accounting_conger' => 'SUM(DataScde.kwh_con-DataScde.kwh_ger)',
		'accounting_ger' => 'SUM(DataScde.kwh_ger)',
		'accounting_con' => 'SUM(DataScde.kwh_con)',
	);

}
?>
