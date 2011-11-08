<?php
class SiteCcee extends AppModel {

	var $name = 'SiteCcee';
	var $displayField = 'ip';
	var $useTable = 'sites_ccee';

	var $validate = array(
		'ip' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'IP não pode ser deixado em branco.',
				'last' => true
			),
			'ip' => array(
				'rule' => 'ip',
				'message' => 'IP inválido.',
				'last' => true
			)
		)
	);

	var $belongsTo = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'site_id'
		)
	);

}
?>