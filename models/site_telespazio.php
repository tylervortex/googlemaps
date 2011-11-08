<?php
class SiteTelespazio extends AppModel {

	var $name = 'SiteTelespazio';
	var $displayField = 'model';
	var $useTable = 'sites_telespazio';

	var $validate = array(
		'number' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Número da estação não pode ser deixado em branco.'
			)
		),
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Nome da estação não pode ser deixado em branco.'
			)
		),
		'designation' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Designação não pode ser deixado em branco.'
			)
		),
	);

	var $belongsTo = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'site_id'
		)
	);

}
?>
