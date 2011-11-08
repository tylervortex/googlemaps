<?php
class Converter extends AppModel {
	
	var $name = 'Converter';
	var $displayField = 'manufacturer';
	
	var $validate = array(
		'manufacturer' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Fabricante não pode ser deixado em branco.'
			),
		),
		'model' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Modelo não pode ser deixado em branco.'
			),
		),
	);

	var $hasMany = array(
		'SiteConverter' => array(
			'className' => 'SiteConverter',
			'foreignKey' => 'converter_id',
		)
	);

}
?>
