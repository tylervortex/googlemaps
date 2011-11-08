<?php
class SiteRouter extends AppModel {

	var $name = 'SiteRouter';
	var $displayField = 'model';
	var $useTable = 'sites_routers';

	var $validate = array(
		'ip' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'IP do roteador não pode ser deixado em branco.'
			)
		),
		'username' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Usuário do roteador não pode ser deixado em branco.'
			)
		),
		'password' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Senha do roteador não pode ser deixada em branco.'
			)
		),
		'manufacturer' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Fabricante do roteador não pode ser deixado em branco.'
			)
		),
		'model' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Modelo do roteador não pode ser deixado em branco.'
			)
		),
		'firmware' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Firmware do roteador não pode ser deixado em branco.'
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
