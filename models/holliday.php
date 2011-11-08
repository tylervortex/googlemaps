<?php
class Holliday extends AppModel {
	var $name = 'Holliday';
	var $displayField = 'name';
	var $virtualFields = array(
		'year' => 'YEAR(Holliday.date)',
		'month' => 'MONTH(Holliday.date)',
		'day' => 'DAY(Holliday.date)',
	);
	var $validate = array(
		'date' => array(
			'date' => array(
				'rule' => array('date')
			),
		),
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Informe o feriado.'
			),
		),
	);
}
?>
