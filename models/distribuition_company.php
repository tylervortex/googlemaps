<?php
class DistribuitionCompany extends AppModel {

	var $name = 'DistribuitionCompany';
	var $displayField = 'short_name';

	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Nome não pode ser deixado em branco.',
				'last' => true,
			),
		),
		'short_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Sigla não pode ser deixada em branco.',
				'last' => true,
			),
		),
	);

	var $hasMany = array(
		'Site' => array(
			'className' => 'Site',
		)
	);

}
?>
