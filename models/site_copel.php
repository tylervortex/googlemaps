<?php
class SiteCopel extends AppModel {

	var $name = 'SiteCopel';
	var $displayField = 'username';
	var $useTable = 'sites_copel';

	var $validate = array(
		'username' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Login não pode ser deixado em branco.',
				'last' => true
			)
		),
		'password' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Senha não pode ser deixado em branco.',
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